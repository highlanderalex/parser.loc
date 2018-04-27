<?php
	
	namespace app\controllers;
	
	use app\models\Product;
	use luxury\libs\Parser;
	use luxury\libs\Csv;
	
	class MainController extends AppController
	{
		private $responce = [];
		private $firstMail = false;
		
		public function indexAction()
		{
			$canonical = PATH;
			$this->setMeta('Парсер', 'Парсер главной стр', 'ключевики главной');
			$this->set(compact('canonical'));	
		}
		
		public function runAction()
		{
			if($this->isAjax())
			{
				$start = time();
			
				if ( $urls = $this->getUrlsProducts() )
				{
					if ( $newProducts = $this->getDataProducts($urls) )
					{
						$file = $this->createCsvFiles($newProducts);
				
						$end = time();
						$total_time = $end - $start;
						$start = date('d/m/Y H:i:s', $start);
						$total_time = sec_to_time($total_time);
						
						ob_start();
						require APP . '/views/mail/mail.php';
						$body = ob_get_clean();
						
						$model = new Product();
						try
						{
							$model->sendMail($body, $this->firstMail);
						}
						catch(\Exception $e)
						{
							$this->responce['error'][] = 'Письмо не отправлено ';
						}
						$this->responce['success'] = SUCCESS_SCRIPT;
					}
					else
					{
						$this->responce['success'] = FAIL_SCRIPT;
						$this->responce['error'][] = 'Данные продуктов не получены';
					}
				}
				else
				{
					$this->responce['success'] = FAIL_SCRIPT;
					$this->responce['error'][] = 'URL адреса продуктов не получены';
				}
				
				header("Content-type: application/json; charset=utf-8");
				echo json_encode($this->responce);
				die;
			}
			else
			{
				throw new \Exception('Страница не найдена', 404);
			}
		}
		
		//Get all urls for products
		public function getUrlsProducts()
		{
			$parser = new Parser();
			$urlOut = array();
			//Get all urls for products
			for($i = 1; $i <= PAGES; $i++)
			{
				if ( 1 == $i )
				{
					$html = get_html(BASE_SITE . '/' . MAIN_PAGE);
				}
				else
				{
					$html = get_html(BASE_SITE . '/' . MAIN_PAGE . '?page=' . $i);
				}
				if (!$html)
				{
					$this->responce['error'][] = BASE_SITE . '/' . MAIN_PAGE . '?page=' . $i . ': ' . FAIL_URL;
					loggit(BASE_SITE . '/' . MAIN_PAGE . '?page=' . $i, FAIL_URL);
					continue;
					
				}
				$parser->loadData($html);
				$urls[] = $parser->getArrayFromSelector('a.lst_a', 'href');
				$parser->clear();
			}
			$urlOut = merge($urls);
			
			return $urlOut;
		}
		
		public function getDataProducts($urls)
		{
			$parser = new Parser();
			for($i = 0; $i < count($urls); $i++)
			{
				$html = get_html(BASE_SITE . $urls[$i]);
				if ( !$html )
				{
					$this->responce['error'][] = BASE_SITE . $urls[$i] . ': ' . FAIL_URL;
					loggit(BASE_SITE . $urls[$i], FAIL_URL);
					continue;
				}
				$product = array();
				$parser->loadData($html);
				
				$id = $parser->getTextFromSelector('b[itemprop=sku]');
				if( !$id ) 
				{
					$this->responce['error'][] = BASE_SITE . $urls[$i] . ': ' . FAIL_DATA;
					loggit(BASE_SITE . $urls[$i], FAIL_DATA);
					continue;
				}
				$product[] = $id;
				
				$product[] = $parser->getTextFromSelector('h1.prod_name');
				
				$product[] = $parser->getTextFromSelector('span.js-product-price-hide');
				
				$product[] = array_to_str(add_str($parser->getArrayFromSelector('div.prod-thumbs a[style]', 'href')));
				//video
				$product[] = array_to_str(add_str($parser->getArrayFromSelector('div.prod-thumbs a[href$=mp4]', 'href')));
				//pdf
				$product[] = array_to_str(add_str($parser->getArrayFromSelector('div.prod-thumbs a[href$=pdf]', 'href')));
				//features
				$product[] = array_to_str($parser->getArrayText('div.js-spoiler-block > ul li'), '[:os:]');
				
				$review = $parser->getChildren('span[class=prod-rvw-info-posted]', 'b > span');
				$total_review = $parser->getAttrValue('span[data-total]', 'data-total');
				
				if ($total_review)
				{
					$c = get_cookie(BASE_SITE . $urls[$i]);
					$param = array();
					$postHtml = '';
					$param[] = 'id=' . $parser->getAttrValue('span[data-id]', 'data-id');
					$param[] = 'type=new';
					$param[] = 'action=getSuperProductReviews';
					for($page_review = 10; $page_review <= $total_review; $page_review +=10)
					{	
						$param[] = 'offset=' . $page_review;
						$postHtml .= get_html(BASE_SITE . '/submit_review.php', true, $param, $c);
					}
					$parser->loadData($postHtml);
					$new_review = $parser->getChildren('span[class=prod-rvw-info-posted]', 'b > span');
					$product[] = array_to_str(array_merge($review, $new_review), ',', true);
				}
				else
				{
					$product[] = array_to_str($review, ',', true);
				}
				
				$newProducts[] = $product;
				$parser->clear();
				
			}
			return $newProducts;
		}
		
		public function createCsvFiles($newProducts)
		{
			//create csv files and write db
			$columns = 'Product Identifier,Product Name,Product Price,Product Images,';
			$columns .= 'Product Video,Product PDF,Product Features,Dates of Reviews';
			
			$csv = new Csv();
			$newData = get_str_from_array($newProducts);
			
			$products = new Product();
			$old = $products->getLastProducts();
			$data['product'] = $newData;
			$products->load($data);
			
			if(!$products->save('products'))
			{
				$this->responce['error'][] = 'Insert to DataBase: Error';
			}
			
			if(!$old)
			{
				$this->firstMail = true;
				$productsCSV = $csv->setCsv(CSV_FILES . '/products.csv', $columns, $newProducts, true);
			}
			else
			{
				$oldProducts = get_array_from_str($old->product);
				$idOldProducts = create_array_by_index($oldProducts);
				$idNewProducts = create_array_by_index($newProducts);
				
				$columns = 'Product Identifier';
				// id's new product
				$newProd = array_diff($idNewProducts, $idOldProducts);
				$newProductsCSV = $csv->setCsv(CSV_FILES . '/new_products.csv', $columns, $newProd);
				
				// id's del product
				$delProd = array_diff($idOldProducts, $idNewProducts);
				$delProductsCSV = $csv->setCsv(CSV_FILES . '/disapperaed_products.csv', $columns, $delProd);
				
				//id's reviewed prod
				$arrId = array_uintersect($idOldProducts, $idNewProducts, 'strcasecmp');
				
				$reviewedProd = get_reviewed($oldProducts, $newProducts, $arrId);
				$reviewedProductsCSV = $csv->setCsv(CSV_FILES . '/recently_reviewed_products.csv', $columns, $reviewedProd);	
			}
			return true;
		}
			
	}