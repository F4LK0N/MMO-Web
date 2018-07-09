<?php defined('BASEPATH') OR exit('No direct script access allowed');

//### DB FIELDS ###
//Fields Count
define("DBF_COUNT",      15);
//Fields
define("DBF_INT_TINY",   0);
define("DBF_INT_SMALL",  1);
define("DBF_INT",        2);
define("DBF_INT_BIG",    3);
define("DBF_UINT_TINY",  4);
define("DBF_UINT_SMALL", 5);
define("DBF_UINT",       6);
define("DBF_UINT_BIG",   7);
define("DBF_ID_MAIN",    8);
define("DBF_ID",         9);
define("DBF_STS",        10);
define("DBF_TIME",       11);
define("DBF_MICROTIME",  12);
define("DBF_TEXT",       13);
define("DBF_TEXT25",     14);
define("DBF_TEXT255",    15);



//### DB ###
class F_DB {

	protected $CI;

	public function __construct()
	{
		$this->CI = &get_instance();
		$this->CI->load->database();
	}
	
	private function DBFieldSQL($index)
	{
		if($index == DBF_INT_TINY)   return "TINYINT     SIGNED   NULL     DEFAULT NULL";   //A number from -127 to 127;
		if($index == DBF_INT_SMALL)  return "SMALLINT    SIGNED   NULL     DEFAULT NULL";   //A number from -32.768 to 32.767;
		if($index == DBF_INT)        return "INT         SIGNED   NULL     DEFAULT NULL";   //A number from -2.147.483.648 to 2.147.483.647;
		if($index == DBF_INT_BIG)    return "BIGINT      SIGNED   NULL     DEFAULT NULL";   //A number from -9.223.372.036.854.775.808 to 9.223.372.036.854.775.807;
		
		if($index == DBF_UINT_TINY)  return "TINYINT     UNSIGNED NULL     DEFAULT NULL";   //A number from 0 to 255; 
		if($index == DBF_UINT_SMALL) return "SMALLINT    UNSIGNED NULL     DEFAULT NULL";   //A number from 0 to 65.535;
		if($index == DBF_UINT)       return "INT         UNSIGNED NULL     DEFAULT NULL";   //A number from 0 to 4.294.967.295;
		if($index == DBF_UINT_BIG)   return "BIGINT      UNSIGNED NULL     DEFAULT NULL";   //A number from 0 to 18.446.744.073.709.551.615;
		
		if($index == DBF_ID_MAIN)    return "BIGINT      UNSIGNED NOT NULL AUTO_INCREMENT"; //An ID greater than 0 (1 to 18.446.744.073.709.551.615). Table Primary Key;
		if($index == DBF_ID)         return "BIGINT      UNSIGNED NULL     DEFAULT NULL";   //An ID greater than 0 (1 to 18.446.744.073.709.551.615); Foreign Key;
		if($index == DBF_STS)        return "TINYINT     SIGNED   NOT NULL DEFAULT 1";      //A boolean state or a small set of states;     
		if($index == DBF_TIME)       return "INT         UNSIGNED NULL     DEFAULT NULL";   //A php timestamp in seconds (1.123.123.123);
		if($index == DBF_MICROTIME)  return "FLOAT(10,4) UNSIGNED NULL     DEFAULT NULL";   //A php timestamp in seconds with miliseconds (1.123.123.123,1234);
		if($index == DBF_TEXT)       return "TEXT                 NULL     DEFAULT NULL";   //A text field.
		if($index == DBF_TEXT25)     return "VARCHAR(25)          NULL     DEFAULT NULL";   //A text field with 25 chars max.
		if($index == DBF_TEXT255)    return "VARCHAR(255)         NULL     DEFAULT NULL";   //A text field with 255 chars max.
		
		return "<!--ERROR--!>";
	}

    public function TableDrop($table)
    {
        $this->CI->db->query("DROP TABLE ".$table);
    }


	public function TableCreate($table, $fields)
	{
		//CREATE
		$query = "CREATE TABLE IF NOT EXISTS `$table` (\n";
			$query .= "`id` ".$this->DBFieldSQL(DBF_ID_MAIN).",\n";
			foreach($fields as $name => $field){
				$query .= "`$name` ".$this->DBFieldSQL($field).",\n";
			}
			$query .= "PRIMARY KEY (`id`)\n";
		$query .= ")\n";
		$query .= "CHARSET='Latin1'   COLLATE='latin1_swedish_ci'   ENGINE=MyISAM;";
		
		//EXECUTE
		$this->CI->db->query($query);
	}
	
	public function TableCreateIndexes($indexes)
	{
		// $query = 
		// "
			// ALTER TABLE `".self::$table."` ADD INDEX(`sts`);
		// ";
	}
	
	
	public function Query($query)
	{
		$query = $this->CI->db->query($query);
		if(method_exists($query, "result"))
			return $query->result();
	}
	
	public function DB()
	{
		return $this->CI->db;
	}
	
	public function Dev()
	{
		
	}
	
	

	// public function foo()
	// {
		// $this->CI->load->helper('url');
		// redirect();
	// }public function bar()
	// {
		// echo $this->CI->config->item('base_url');
	// }
}
