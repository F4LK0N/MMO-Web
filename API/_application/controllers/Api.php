<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class API extends CI_Controller {

	static private $version = 1;
	
	function __construct()
	{
		parent::__construct();
	}
	
	public function index()
	{
		print
		'{'.
			'"API":'.'{'.'"v":'.API::$version.'}'.
		'}';
	}
	
	public function Login()
	{
		//INPUT
		$nick="";
		if($this->input->get('nick'))
			$nick = $this->input->get('nick');
		if($this->input->post('nick'))
			$nick = $this->input->post('nick');
		
		
		//ACCESS
		if($nick){
			$this->load->model('users_model');
			$this->users_model->Login($nick);
			
			print
			'{'.
				'"API":'.'{'.'"v":'.API::$version.'},'.
				'"User":"LOGGED"'.
			'}';
			die;
		}
		
		
		//FORM
		print
		"<form action='' method='post' enctype='multipart/form-data' style='margin:auto;display:block;width:200px;'>".
			"Nick:<br>".
			"<input type='text' name='nick'><br>".
			"<input type='submit' value='Login'>".
		"</form>";
	}
	
	public function Logout()
	{
		//INPUT
		$sent = $this->input->post('sent');
		
		
		//ACCESS
		if($sent){
			$this->load->model('users_model');
			$this->users_model->Logout();
			print
			'{'.
				'"API":'.'{'.'"v":'.API::$version.'},'.
				'"User":0'.
			'}';
			die;
		}
		
		
		//FORM
		print
		"<form action='' method='post' enctype='multipart/form-data' style='margin:auto;display:block;width:200px;'>".
			"Logout:<br>".
			"<input type='hidden' name='sent' value='1'><br>".
			"<input type='submit' value='Logout'>".
		"</form>";
	}
	
	public function Update()
	{
		//USER - AUTH
		$this->UserAuthCheck();
		
		
		
		print
		'{'.
//			'"API":'.'{'.'"v":'.API::$version.'},'.
//			'"User":'.($this->users_model->id)','.
            '"Time":'.(microtime(true)*10000).','.
		    '"User":{'.
                '"x":'.$this->users_model->Position()['x'].','.
                '"y":'.$this->users_model->Position()['y'].
            '}';

			//MAP
			$this->load->model('map_model');
			$this->map_model->Update();
			
			//PLAYERS
			$this->load->model('users_model');
			$this->users_model->Update();
		
		print
		'}';
	}
	
	public function Action()
	{
		//USER - AUTH
		$this->UserAuthCheck();


        $didAction = false;



		//### WALK ###
		{
            //INPUT
            $walk = "";
            if ($this->input->get('walk'))
                $walk = $this->input->get('walk');
            if ($this->input->post('walk'))
                $walk = $this->input->post('walk');


            //ACCESS
            if ($walk) {
                $this->load->model('action_model');
                $this->action_model->Walk($walk);
                $didAction = true;
            }
        }

        //ATTACK
        {
            //INPUT
            $attack = "";
            if ($this->input->get('attack'))
                $attack = $this->input->get('attack');
            if ($this->input->post('attack'))
                $attack = $this->input->post('attack');

            //ACCESS
            if ($attack) {
                $this->load->model('action_model');
                $this->action_model->Attack($walk);
                $didAction = true;
            }
        }



        if($didAction){
            print
            '{' .
                '"API":' . '{' . '"v":' . API::$version . '},' .
                '"User":"ACTION"' .
            '}';
            return;
        }

		//FORM
		print
		"<form action='' method='post' enctype='multipart/form-data' style='margin:auto;display:block;width:200px;'>".
			"Walk: (L,R,U,D)<br>".
			"<input type='text' name='walk' value='U'><br>".
			"<input type='submit' value='Walk'>".
		"</form>".
        "<form action='' method='post' enctype='multipart/form-data' style='margin:auto;display:block;width:200px;'>".
            "Attack: (A)<br>".
            "<input type='text' name='attack' value='A'><br>".
            "<input type='submit' value='Attack'>".
        "</form>";
	}
	
	
	
	private function UserAuthCheck() {
		
		$this->load->model('users_model');
		if(!$this->users_model->Logged()) {
			print
			'{'.
				'"API":'.'{'.'"v":'.API::$version.'},'.
				'"User":0'.
			'}';
			die;
		}
	}
	
}




