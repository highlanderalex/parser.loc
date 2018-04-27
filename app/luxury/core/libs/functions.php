<?php
	
	function debug($data)
	{
		echo '<pre style="color: #e1e0e0;background: #232323; margin:0; padding: 10px;">';
		print_r($data);
		echo '</pre>';
	}
	
	function redirect($http = false)
	{
		if ($http)
		{
			$redirect = $http;
		}
		else
		{
			$redirect = isset($_SERVER['HTTP_REFERER']) ? $_SERVER['HTTP_REFERER'] : PATH;
		}
		header("Location: $redirect");
		exit;
	}
	
	/**
	* Log errors to file.
	*
	* @param  string $url
	* @param  string $msg
	*/
	function loggit($url, $msg)
	{
		$d = date('d/m/Y H:i:s');
		$str = '[' . $d . '] ERROR - ' . $url . ' - ' . $msg . PHP_EOL;
		file_put_contents(LOG_FILE, $str, FILE_APPEND);
	}
	
	/**
	* Merge multi array to simple array .
	*
	* @param  array $arrInn
	* @return array
	*/
	function merge($arrIn)
	{
		if(empty($arrIn))
			return false;
		$arrOut = array();
		foreach($arrIn as $subArr)
		{
			$arrOut = array_merge($arrOut, $subArr);
		}
		return $arrOut;
	}
	
	/**
	* Create array by index.
	*
	* @param  array $arrInn
	* @param  integer $ind
	* @return array
	*/
	function create_array_by_index($arrIn, $ind = 0)
	{
		$arrOut = array();
		foreach($arrIn as $subArr)
		{
			$arrOut[] = $subArr[$ind];
		}
		return $arrOut;
	}
	
	/**
    * Transform array to string.
    *
    * @param  array $arr
	* @param  string $delim
	* @param  boolean $data
	* @return string
    */
	function array_to_str($arr, $delim = ',' , $data = false)
	{
		if (is_array($arr) && !empty($arr))
		{
			if ( $data )
			{
				$tmp = array();
				foreach($arr as $val)
				{ 
					$tmp[] = date('m-d-Y', strtotime($val));;
				}
				return implode($delim, $tmp);
			}
			
			return implode($delim, $arr);
		}
		else
		{
			return 'empty';
		}
	}
	
	/**
	* Compare two array by values.
	*
	* @param  array $oldVal
	* @param  array $newVal
	* @param  array $arrKey
	* @return array
	*/
	function get_reviewed($oldVal, $newVal, $arrKey)
	{
		$result = array();
		if ( empty($arrKey) )
			return $result;
		
		foreach($arrKey as $id)
		{
			$old = array_values(array_filter($oldVal, function($innerArray) use($id){
				return ($innerArray[0] == $id);
			}));
			
			$new = array_values(array_filter($newVal, function($innerArray) use($id){
				return ($innerArray[0] == $id);
			}));
			
			if ( $old[0][7] != $new[0][7] )
				$result[] = $id;
		
		}	
		return $result;
	}
	
	/**
	* Get time H:i:s.
	*
	* @param  int $seconds
	* @return string
	*/
	function sec_to_time($seconds) 
	{ 
		$hours = floor($seconds / 3600); 
		$minutes = floor($seconds % 3600 / 60); 
		$seconds = $seconds % 60; 
		return sprintf("%d:%02d:%02d", $hours, $minutes, $seconds); 
	}
	
	/**
	* Get string from array.
	*
	* @param  array $data
	* @return string
	*/
	function get_str_from_array($data)
	{
		$tmp = array();
		foreach($data as $row)
		{
			$tmp[] = implode('|', $row);
		}
		
		$str = implode(PHP_EOL, $tmp);
		return $str;
	}
	
	/**
	* Get array from string.
	*
	* @param string $str
	* @return array
	*/
	function get_array_from_str($str)
	{
		$res = array();
		$tmp = explode("\n", str_replace("\r", '', $str));
		
		foreach($tmp as $row)
		{
			$res[] = explode('|', $row);
		}
		return $res;
	}
	
	/**
	* Get html from url.
	*
	* @param  string $url
	* @param  boolean $post
	* @param  array $data
	* @param  array $cookie
	* @return boolean or str
	*/
	function get_html($url, $post = false, $data = array(), $cookie = array())
	{
		$ch = curl_init();
		curl_setopt($ch, CURLOPT_URL, $url);
		curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
		if (USE_PROXY)
			curl_setopt($ch, CURLOPT_PROXY, get_proxy());
		if ($post)
		{
			$strData = implode('&', $data);
			
			$tmpCookie = array_map(function($k, $v){ 
				return "$k=$v";
			}, array_keys($cookie), $cookie);
			
			$strCookie = implode(';', $tmpCookie);
			
			curl_setopt($ch, CURLOPT_POST, true);
			curl_setopt($ch, CURLOPT_COOKIE, $strCookie);
			curl_setopt($ch, CURLOPT_HTTPHEADER, array('X-Requested-With: XMLHttpRequest'));
			curl_setopt($ch, CURLOPT_POSTFIELDS, $strData);
		}
		
		$result = curl_exec($ch);
		$httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
		curl_close($ch);
		if ( '404' == $httpCode )
			$result = false;
		
		return $result;
	}
	
	/**
	* Get cookie from url.
	*
	* @param  string $url
	* @return array
	*/
	function get_cookie($url)
	{
		$ch = curl_init();
		curl_setopt($ch, CURLOPT_URL, $url);
		curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
		curl_setopt($ch, CURLOPT_HEADER, 1);
		if (USE_PROXY)
			curl_setopt($ch, CURLOPT_PROXY, get_proxy());
		
		$result = curl_exec($ch);
		preg_match_all('/^Set-Cookie:\s*([^;]*)/mi', $result, $matches);
		$cookies = array();
		foreach($matches[1] as $item) 
		{
			parse_str($item, $cookie);
			$cookies = array_merge($cookies, $cookie);
		}
		return $cookies;
	}
	/**
	* Get proxy.
	*
	* @return string
	*/
	function get_proxy()
	{
		static $list;
		if(empty($list)) 
		{
			$list = explode(PHP_EOL, file_get_contents(PROXY_FILE));
		}
		$i = rand(0, count($list)-1);
		return $list[$i];
	}
	
	function add_begin_url($i)
	{
		if( 0 === strpos('http://', $i) )
			return $i;
			
		return BASE_SITE . $i;
	}
			
	/**
	* Add string to element array.
	*
	* @param  string $url
	* @return array
	*/
	function add_str($data = array())
	{
		if(empty($data))
			return $data;
			
		$result = array_map('add_begin_url', $data);
		return $result;
	}
	