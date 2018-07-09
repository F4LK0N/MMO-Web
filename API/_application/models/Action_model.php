<?php defined('BASEPATH') OR exit('No direct script access allowed');

class Action_model extends F_Model {

	//DB
	protected $table = "none";
	protected $fields = array();
	
	
	public function __construct()
	{
		parent::__construct();
		$this->load->model('users_model');
	}
	
	public function Walk($action)
	{
		$interval = $this->users_model->walkInterval;
		$last     = $this->users_model->t_walk;
		
		
		//Action - Cooldown
		if($last+$interval > $this->users_model->GetTime())
			return;
		//Action - Allowed
		$this->load->model('map_model');
		
		
		
		//Ground
		if(!$this->map_model->CanWalk($action)){
			return; }
		
		//Objects
		
		//Players
		$this->users_model->Walk($action);
		return;
	}

	public function Attack($action){

    }
	
	public function Shoot($action)
	{

	}
}