<?php
	use luxury\Router;
	
	//default routes
	Router::add('^$', ['controller' => 'Main', 'action' => 'index']);
	Router::add('^(?P<controller>[a-z-]+)/?(?P<action>[a-z-]+)?$');