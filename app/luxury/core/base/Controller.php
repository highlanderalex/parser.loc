<?php

	namespace luxury\base;

	abstract class Controller
	{
		public $route;
		public $controller;
		public $model;
		public $view;
		public $prefix;
		public $layout;
		public $data = [];
		public $meta = ['title' => '', 'description' => '', 'keywords' => ''];
		
		public function __construct($route)
		{
			$this->route = $route;
			$this->controller = $route['controller'];
			$this->model = $route['controller'];
			$this->view = $route['action'];
			$this->prefix = $route['prefix'];
		}
		
		public function set($data)
		{
			$this->data = $data;
		}
		
		public function getView()
		{
			$viewObj = new View($this->route, $this->layout, $this->view, $this->meta);
			$viewObj->render($this->data);
		}
		
		public function setMeta($title = '', $desc = '', $keywords = '')
		{
			$this->meta['title'] = $title;
			$this->meta['description'] = $desc;
			$this->meta['keywords'] = $keywords;
		}
		
		public function isAjax() 
		{
			return isset($_SERVER['HTTP_X_REQUESTED_WITH']) && $_SERVER['HTTP_X_REQUESTED_WITH'] === 'XMLHttpRequest';
		}

		public function loadView($view, $vars = [])
		{
			extract($vars);
			require APP . "/views/{$this->prefix}{$this->controller}/{$view}.php";
			die;
		}
	}