<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Home extends CI_Controller {
	
	public function index()
	{
		$data['title']="Welcome to MMO Game!";
		
		$this->load->view('_shared/header', $data);
		$this->load->view('home', $data);
		$this->load->view('_shared/footer', $data);
	}
}