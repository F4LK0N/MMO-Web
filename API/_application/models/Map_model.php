<?php defined('BASEPATH') OR exit('No direct script access allowed');
 
class Map_model extends F_Model {
 
    protected $table = "map";
    protected $fields = array(
        "x"     => DBF_STS,
        "y"     => DBF_STS,
        "tile"  => DBF_STS,
    );
    protected $afterInstallQuery = "";
   
   
    //SIZE
    private $mapWidth  = 50;
    private $mapHeight = 50;
   
   
   
   
    public function __construct()
    {
        parent::__construct();
        $this->ConstructAfterInstallQuery();
    }
   
    private function ConstructAfterInstallQuery()
    {
        $query=array();
       
        $query[]="TRUNCATE TABLE ".$this->table.";";
       
        for($i=0; $i<$this->mapWidth; $i++)
            for($j=0; $j<$this->mapHeight; $j++)
                $query[]="INSERT INTO ".$this->table."   (x, y, tile) VALUES ($i, $j, 0);";
           
        $this->afterInstallQuery = $query;
    }
   
    public function Update()
    {
        $this->load->model('users_model');
       
        $view['left']  = $this->users_model->Position()['x'] - $this->users_model->FOV();
        $view['right'] = $this->users_model->Position()['x'] + $this->users_model->FOV();
        $view['top']   = $this->users_model->Position()['y'] + $this->users_model->FOV();
        $view['bottom']= $this->users_model->Position()['y'] - $this->users_model->FOV();
        $width=$this->users_model->FOV()*2+1;
       
        $query = "SELECT x, y, tile   FROM ".$this->table."   WHERE x>=".$view['left']." AND x<=".$view['right']." AND y<=".$view['top']." AND y>=".$view['bottom']."   ORDER BY y ASC, x ASC; ";
        $query = $this->f_db->Query($query);
       
       
        $y=0;
		$i=0;
        $json=
        ','.
        '"Map":{'.
            '"'.$y.'":{';
            foreach($query as $tile){
                if($i>=$width){ $json.='},"'.++$y.'":{';  $i=0; }

                $json.='"'.($i).'":'.$tile->tile;
                $i++;

                if($i!=$width)
                    $json.=',';
            }
            $json.=
            '}'.
        '}';
       
        print"$json";
    }
   
    public function CanWalk($direction){

        $this->load->model('users_model');

        $x = $this->users_model->Position()['x'];
        $y = $this->users_model->Position()['y'];


        if($direction==="R")
            $x++;
        else if($direction==="L")
            $x--;
        else if($direction==="U")
            $y--;
        else if($direction==="D")
            $y++;


        if($x<0 || $x>$this->mapWidth || $y<0 || $y>$this->mapHeight)
            return false;
        else
            return true;
    }
   
}