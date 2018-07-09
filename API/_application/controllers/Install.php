<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Install extends CI_Controller {

	public function index()
	{
		print("Installing php web system...<br><br>");
		
		print("DB procedures:<br>");
		$path=APPPATH."models";
		$tree=scandir($path);
		foreach($tree as $obj){ if(!($obj=="." || $obj=="..")){
			if(is_file("$path/$obj") && "php"==pathinfo($obj, PATHINFO_EXTENSION)){
				$obj = strtolower(pathinfo($obj, PATHINFO_FILENAME));
				print"- <b>$obj</b> : Load Model ";
				$this->load->model($obj);
				
				if(method_exists($this->$obj, "Install")){
					$this->$obj->Install();
				}
				
				print"<br>";
			}
		}}
		
	}
}