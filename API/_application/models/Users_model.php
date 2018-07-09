<?php defined('BASEPATH') OR exit('No direct script access allowed');

class Users_model extends F_Model {

	//DB
	protected $table = "users";
	protected $fields = array(
        "token"      => DBF_TEXT,       //The session identifier.
		"nick"       => DBF_TEXT255,    //User nick in the game.
		"x"          => DBF_INT,
		"y"          => DBF_INT,
		"t_login"    => DBF_INT_BIG,    //Time login - the timestamp of when the users has logged in.
		"t_last"     => DBF_INT_BIG,    //Time of the last movement the user have made.
		"t_walk"     => DBF_INT_BIG,    //Time the last step the user made.
		"t_shoot"    => DBF_INT_BIG,    //Time the last shoot the user made
	);
	
	//SESSION
	// private $sessionDuration = 15;
	private $sessionDuration = 60;
	private $token = "";
	
	//SESSION EXTERNAL
	private $ip     = "";
	private $agent  = "";
	
	//SESSION DATA
	public $id      = FALSE;
	public $logged  = FALSE;
	public $nick    = FALSE;
	public $t_last  = 0;
	public $t_walk  = 0;
	public $t_shoot = 0;
	
	
	//PLAYER FIELD OF VISION
	private $fov = 5;
	public function FOV(){ return $this->fov; }
	
	public $walkInterval  =  160;//ms
	public $shootInterval = 1000;//ms
	
	//POSITION
	private $position = array(
		'x' => -9999,
		'y' => -9999,
	);
	public function Position (){
		return $this->position;
	}
	
	
	
	public function __construct()
	{
		parent::__construct();
		
		//INSTALL (EXCEPTION)
		$this->load->helper('url');
		if(substr_count(strtolower(current_url()), "/install"))
			return;
		
		//LOAD
		$this->load->database();
		$this->load->library('session');
		$this->load->helper('cookie');
		
		//EXTERNAL
		$this->ip    = $_SERVER['HTTP_HOST'];
		$this->agent = $_SERVER['HTTP_USER_AGENT'];



        //DATA
        if(isset($_SERVER['HTTP_AUTHTOKEN'])){

            $this->token = $_SERVER['HTTP_AUTHTOKEN'];

            $query = $this->f_db->query("SELECT *   FROM `".$this->table."`   WHERE (t_last>".($this->GetTime($this->sessionDuration)).") AND (token='".$this->token."'); ");
            if(count($query)){
                $query=$query[0];

                //Get Data
                $this->logged  = true;
                $this->id      = $query->id;
                $this->nick    = $query->nick;
                $this->t_last  = $query->t_last;
                $this->t_walk  = $query->t_walk;
                $this->t_shoot = $query->t_shoot;
                $this->position['x'] = $query->x;
                $this->position['y'] = $query->y;

                //UpdateRequest Last
                $this->db->query("UPDATE `".$this->table."`   SET `t_last` =".$this->GetTime()."   WHERE (id='".$this->id."'); ");
            }

        }

	}
	
	public function GetTime($difference=0)
	{
		return (microtime(true)*10000) - ($difference*10000);
	}
	
	public function Logged()
	{
		return $this->logged;
	}
	
	public function Login($nick)
	{
		//CLEAR
		//$this->Logout();


        //TOKEN
        $token = md5($this->ip."@".$this->agent."@".microtime(true));
		
		//LOGIN
		$this->db->insert(
			$this->table,
			array(
				'token'      => $token,
				'nick'       => $nick,
				'x'          => 25,
				'y'          => 25,
				't_login'    => $this->GetTime(),
				't_last'     => $this->GetTime(),
				't_walk'     => $this->GetTime(),
				't_shoot'    => $this->GetTime(),
			)
		);

        return $token;
	}
	
	public function Logout()
	{
		//$this->db->delete($this->table, array('id_session' => $this->ip."@".$this->agent));
	}
	
	
	
	public function Update()
	{
		$view['left']  = $this->users_model->Position()['x'] - $this->users_model->FOV();
		$view['right'] = $this->users_model->Position()['x'] + $this->users_model->FOV();
		$view['top']   = $this->users_model->Position()['y'] + $this->users_model->FOV();
		$view['bottom']= $this->users_model->Position()['y'] - $this->users_model->FOV();
		$width=$this->users_model->FOV()*2+1;
		
		$query = "SELECT id,x,y,nick   FROM ".$this->table."   WHERE (t_last>".($this->GetTime($this->sessionDuration)).") AND x>=".$view['left']." AND x<=".$view['right']." AND y<=".$view['top']." AND y>=".$view['bottom']."   ORDER BY y ASC, x ASC; ";
		$query = $this->f_db->Query($query);

		
		
		$json=
		',"Players":{';

		    if(count($query)) {
                foreach ($query as $player) {
                    $json .= '"' . $player->id . '":{"nick":"' . $player->nick . '","x":' . $player->x . ',"y":' . $player->y . '},';
                }
                $json = substr($json, 0, -1);
            }
		print"$json}";
	}

	public function Walk($direction)
	{
		//INPUT
		if($direction!="L"&&$direction!="R"&&$direction!="U"&&$direction!="D"){
			print"!\n"; return; }
		
		
		//MOVE
		switch($direction)
		{
			case "L": $this->position['x']--; break;
			case "R": $this->position['x']++; break;
			case "U": $this->position['y']--; break;
			case "D": $this->position['y']++; break;
		}
		$this->t_walk = $this->t_last = $this->GetTime();
		
		
		//UPDATE
		$this->db->query("UPDATE `".$this->table."`   SET `x`=".$this->position['x'].", `y`=".$this->position['y'].", `t_walk` =".$this->GetTime()."   WHERE (id='".$this->id."'); ");
        $this->db->query("UPDATE `map`   SET `tile` = ".$this->id."   WHERE (`x`=".$this->position['x'].") AND (`y`=".$this->position['y']."); ");
	}
	
}