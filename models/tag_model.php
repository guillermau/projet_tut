<?php 

class Tag_model extends CI_Model{
	// Nom de la table tags
	protected $table = 'tags';
	protected $table_document = 'tag_document';
	protected $table_projet = 'tag_projet';

	function __construct()
    {
        parent::__construct();
    }

	// Ajouter un tag
	public function ajouter_tag($tag){
		$tag = trim($tag);
		$query = $this->db->select('*')->from($this->table)->where('tag',$tag)->get();
		if ($query->num_rows() > 0) {
			$this->db->where('tag', $tag)->set('occurrence', 'occurrence+1', FALSE)->update($this->table); 
		} else {
			$data = array('tag' => $tag,
					  'occurrence' => '1');
			$this->db->insert($this->table, $data);
		}

		
	}

	// Récupérer liste des tags
	public function lister_tag() {
		return $this->db->select('*')
						->from($this->table)
						->get()
						->result();
	}

	// Fonction pour la recherche autocompletion
	public function rechercher_autocpl($tag) {
		$tag = trim($tag);
		return $this->recherche_ress_occur($tag,5);
	}

	// Récupérer tags par ressemblance et occurrence
	public function recherche_ress_occur($tag, $limit) {
		$tag = trim($tag);
		return $this->db->select('*')
						->from($this->table)
						->like('tag',$tag)
						->order_by('occurrence')
						->limit($limit)
						->get()
						->result();
	}

	public function recuperer_tags_projet($idprojet){
		return $this->db->from($this->table_projet)
						->where("idprojet",$idprojet)
						->get()
						->result();
	}

	public function recuperer_tags_document($iddoc){
		return $this->db->from($this->table_document)
						->where("iddocument",$iddoc)
						->get()
						->result();
	}

	public function supprimer_tags_document($iddoc){
		$tags_doc = $this->db->select("*")
						 ->from($this->table_document)
						 ->where("iddocument",$iddoc)
						 ->join($this->table, $this->table.".tag = ".$this->table_document.".tag")
						 ->get()
						 ->result();
		foreach($tags_doc as $tag) {
			if($tag->occurrence > 1) {
				$this->db->where('tag', $tag->tag)->set('occurrence', 'occurrence-1', FALSE)->update($this->table); 
				$this->db->where('tag', $tag->tag)->where('iddocument', $iddoc)->delete($this->table_document);
			} else {
				$this->db->where('tag', $tag->tag)->where('iddocument', $iddoc)->delete($this->table_document);
				$this->db->where('tag', $tag->tag)->delete($this->table);
			}
		}
	}

	public function supprimer_tags_projet($idprojet){
		$tags_proj = $this->db->select("*")
						  ->from($this->table_projet)
						  ->join($this->table, $this->table.".tag = ".$this->table_projet.".tag")
						  ->where("idprojet",$idprojet)
						  ->get()
						  ->result();
		foreach($tags_proj as $tag) {
			if($tag->occurrence > 1) {
				$this->db->where('tag', $tag->tag)->set('occurrence', 'occurrence-1', FALSE)->update($this->table); 
				$this->db->where('tag', $tag->tag)->where('idprojet', $idprojet)->delete($this->table_projet);
			} else {
				$this->db->where('tag', $tag->tag)->where('idprojet', $idprojet)->delete($this->table_projet);
				$this->db->where('tag', $tag->tag)->delete($this->table);
			}
		}
	}

}

?>