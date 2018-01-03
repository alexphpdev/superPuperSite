<?php

namespace application\models;

class MainModel{
	function render($view, $args = null){
		include 'application/views/' . $view;
	}

	protected $passwordHashCost = ["cost" => 9];
}

?>