<?php 

// Gestion des types
class Type_model extends CI_Model{

	protected $table = "types";

	function __construct()
    {
        parent::__construct();
    }

	// liste des types
	public function liste_types(){
		$query   = $this->db->select('*')
			 			->from($this->table)
			 			->order_by('type')
						->get();
		if ($query->num_rows() > 0) {
			$data = array();
			foreach($query->result() as $row) {
				$row->extensions    = explode(',', $row->extensions);
				$data[$row->idtype] = $row;
			}
			return $data;
		} else {
			return false;
		}
	}

	// id type a partir de extension
	public function id_extension($extension){
		$ext = ','.strtolower(trim($extension)).',';
		$query = $this->db->select('*')
			 			->from($this->table)
			 			->like('extensions',$ext)
						->get();
		if ($query->num_rows() > 0)
		{
   			$row = $query->row(); 
   			return $row->idtype;
   		} else {
   			return 1;
   		}
   }	

	// recuperer un type par id
	public function recuperer_type($id){
		$query = $this->db->select('*')
			 			->from($this->table)
			 			->like('idtype',$id)
						->get();
		if ($query->num_rows() > 0)
		{
   			$row = $query->row(); 
   			$row->extensions = explode(',', $row->extensions);
   			return $row;
   		}
   		return false;
	}
}