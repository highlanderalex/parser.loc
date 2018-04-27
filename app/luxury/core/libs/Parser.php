<?php
	
	namespace luxury\libs;
	
	use Symfony\Component\DomCrawler\Crawler;
	
	/**
	* Class for parse a html page.
	*
	*/
	class Parser
	{
		protected $html;
		
		/**
		* Create a new instance of class.
		*
		*
		* @return void
		*/
		public function __construct()
		{
			
		}
		
		/**
		* Load dom from html.
		*
		*
		* @param string $url
		*/
		public function loadData($html)
		{
			$this->html = new Crawler($html);
		}
		
		/**
		* Get array of attributes from selector.
		*
		*
		* @param string $sel
		* @param string $attr
		* @return array
		*/
		public function getArrayFromSelector($sel, $attr)
		{
			$result = $this->html->filter($sel)->each(function (Crawler $node) use ($attr){
				return $node->attr($attr);
			});
			
			return $result;
		}
		
		/**
		* Get value of attribute from selector.
		*
		*
		* @param string $sel
		* @param string $attr
		* @return string
		*/
		public function getAttrValue($sel, $attr)
		{
			$res = $this->html->filter($sel);
			if (!count($res))
				return false;
			
			return $res->attr($attr);
		}
		
		/**
		* Get text from selector.
		*
		*
		* @param string $sel
		* @return string
		*/
		public function getTextFromSelector($sel)
		{
			$block = $this->html->filter($sel);
			
			if(!count($block))
				return false;
			
			$result = $block->text();
			
			return $result;
		}
		
		/**
		* Get array text info.
		*
		*
		* @param string $sel
		* @return array
		*/
		public function getArrayText($sel)
		{
			$result = $this->html->filter($sel)->each(function (Crawler $node){
				return $node->text();
			});
			
			return $result;
		}
		
		/**
		* Get array from selector and filter.
		*
		*
		* @param string $sel
		* @param string $filter
		* @return array
		*/
		public function getChildren($sel, $filter)
		{
			
			$result = $this->html->filter($sel)->each(function (Crawler $node) use ($filter){
				return $node->filter($filter)->last()->text();
			});
			
			return $result;
		}
		
		public function clear()
		{
			$this->html->clear();
		}
		
	}