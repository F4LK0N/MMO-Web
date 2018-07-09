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
