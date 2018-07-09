<?php

//############
//### PATH ###
//############
class PATH {
    
    static private $root;
    
    
    static function SetROOT ($_root){
        self::$root = $_root;
    }
    
    static function ROOT (){
        return self::$root;
    }
    
    static function PHP (){
        return self::$root."_php/";
    }
    
    static function IMG (){
        return self::$root."_img/";
    }
    
    static function CSS (){
       return self::$root."_css/";
    }
    
    static function JS (){
       return self::$root."_js/";
    }
    
    static function URL (){
        return $_SERVER['REQUEST_URI'];
    }
}
PATH::SetROOT("");



//### ENVIRONMENT ###
class ENV {
	
	//SERVE ENVIRONMENTS
	const SERVER_OFFLINE = 0;
	const SERVER_ONLINE  = 1;
	
	static private $server = self::SERVER_OFFLINE;
	static public function Server(){
		return self::$server;
	}
	static private function ServerInit(){
		
		//OFFLINE
		if(!isset($_SERVER['SERVER_ADDR']) || $_SERVER['SERVER_ADDR'] === "127.0.0.1" || $_SERVER['SERVER_ADDR'] === "127.0.0.1:80" || $_SERVER['SERVER_ADDR'] === "127.0.0.1:8080" || $_SERVER['SERVER_ADDR'] === "127.0.0.1"){
			self::$server = self::SERVER_OFFLINE;
		}
		//ONLINE
		else{
			self::$server = self::SERVER_ONLINE;
		}
		
	}
	
	
	
	//EXECUTION ENVIRONMENTS
	const EXECUTION_DEV     = 0;
	const EXECUTION_STAGING = 1;
	const EXECUTION_PROD    = 2;
	
	static private $execution = self::EXECUTION_DEV;
	static public function Execution(){
		return self::$execution;
	}
	static private function ExecutionInit(){
		
		//PROD
		if (isset($_ENV['EXECUTION']) && $_ENV['EXECUTION'] == "PROD")
			self::$execution = self::EXECUTION_PROD;
		
		//STAGING
		else if (isset($_ENV['EXECUTION']) && $_ENV['EXECUTION'] == "STAGING")
			self::$execution = self::EXECUTION_STAGING;
		
		//DEV
		else
			self::$execution = self::EXECUTION_DEV;
		
	}
	
	
	
	static public function Init(){
		self::ServerInit();
		self::ExecutionInit();
	}
	
}
ENV::Init();