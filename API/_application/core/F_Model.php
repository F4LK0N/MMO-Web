<?php defined('BASEPATH') OR exit('No direct script access allowed');

class F_Model extends CI_Model {
	
	protected $table = "";
	protected $fields = array();
	protected $beforeInstallQuery = NULL;
	protected $afterInstallQuery = NULL;
	
	public function Table()
	{
		return $this->table;
	}
	public function Fields()
	{
		return $this->fields;
	}
	public function Field($name)
	{
		if(isset($this->fields[$name]))
			return $this->fields[$name];
	}
	
	
	
	public function __construct()
	{
		parent::__construct();
		$this->load->database();
		$this->load->library('f_db');
	}
	
	
	
	public function Install()
	{
		//Check
		if(!$this->table || !$this->fields)
			return;
		//Header
		print
		": DB Install";
		
		
        //Clear
        $this->f_db->TableDrop($this->table);

		//Before
		$this->InstallQueries($this->beforeInstallQuery);
		//Run
		$this->f_db->TableCreate($this->table, $this->fields);
		//After
		$this->InstallQueries($this->afterInstallQuery);
	}
	
	private function InstallQueries($queries)
	{
		if(!$queries)
			return;
		
		if(!is_array($queries))
			$queries[0] = $queries;
		
		foreach($queries as $query)
			$this->f_db->DB()->query($query);
	}
	
}
