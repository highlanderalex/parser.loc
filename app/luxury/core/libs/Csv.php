<?php
	
	namespace luxury\libs;
	/**
	* Class for work with csv files.
	*
	*/
	class Csv
	{ 
		private $delim = '|';
	
		public function __construct() 
		{
			
		}
		
		/**
		* Create csv file and write info.
		*
		*
		* @param string $file
		* @param string $column
		* @param array $data
		* @param boolean $multi
		* @return boolean
		*/
		public function setCsv($file, $column, $data = array(), $multi = false) 
		{
			$handle = fopen($file, 'w');
			fputcsv($handle, explode(',', $column), $this->delim);
			if ( !empty($data) )
			{
				if( !$multi )
				{
					@fputcsv($handle, $data, PHP_EOL);
				}
				else
				{
					foreach ($data as $item) 
					{ 
						fputcsv($handle, $item, $this->delim);	
					}
				}
			}
			fclose($handle);
			
			return true;
		}
	 
		/**
		* Get info from csv file.
		*
		*
		* @param string $file
		* @return array
		*/
		public function getCsv($file) 
		{
			$handle = fopen($file, 'r');
	 
			$result = array(); 
			while (($line = fgetcsv($handle, 0, '|')) !== FALSE)
			{ 
				$result[] = $line;
			}
			fclose($handle);
			array_shift($result);
			
			return $result; 
		}
	}