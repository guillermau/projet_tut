<?php 

class Recherche_model extends CI_Model {
	// Nom des tables
	protected $utilisateurs = 'utilisateurs';
	protected $projets = 'projets';
	protected $documents = 'documents';
	protected $tag_projet = 'tag_projet';
	protected $tag_document = 'tag_document';

	function __construct()
    {
        parent::__construct();
    }

	public function recherche_projet($nom, $statut = "tous", $date_debut, $date_fin, $tag, $tri = "alphabetique", $ordre = "ASC", $limit = 100, $offset = 0){

		if( ! empty($tag)){
			$tags = explode ( "," , $tag );
			$id_tag = $this->recherche_tag($tags, "projet");
			if(!empty($id_tag) && count($id_tag) > 0){
				$this->db->where_in('idprojet', $id_tag);
			}
		}

		if( ! empty($nom)) {
			$this->db->like("nom",$nom);
		}

		if( $statut == "clos") {
			$this->db->where("statut","clos");
		} elseif ($statut == "ouvert") {
			$this->db->where("statut","ouvert");
		}

		if( ! empty($date_debut) && empty($date_fin)) {
			$debut = strtotime($date_debut);
			$this->db->where("creation >=",$debut);
		} elseif(empty($date_debut) && ! empty($date_fin)) {
			$fin = strtotime($date_fin);
			$this->db->where("creation <=",$fin);
		} elseif( ! empty($date_debut) && ! empty($date_fin)) {
			$debut = strtotime($date_debut);
			$fin = strtotime($date_fin);
			if($debut == $fin) {
				$fin += 24*60*60;
			}
			$this->db->where("creation >=",$debut);
			$this->db->where("creation <=",$fin);
		}

		if($ordre != "ASC") {
			$ordre = "DESC";
		}

		switch($tri) {
			case "alphabetique":
				$this->db->order_by("nom",$ordre);
			break;
			case "date":
				$this->db->order_by("creation",$ordre);
			break;
		}

		return $this->db->select('*')
		  		 ->from($this->projets)
		  		 ->limit($limit,$offset)
				 ->get()
				 ->result();
	}

	public function recherche_utilisateur($nom, $statut = "tous", $date_debut, $date_fin, $tri = "alphabetique", $ordre = "ASC", $limit = 100, $offset = 0){

		if( ! empty($nom)) {
			$this->db->like("concat_ws(' ',nom,prenom)",$nom);
		}

		if( $statut == "clos") {
			$this->db->where("statut","clos");
		} elseif ($statut == "actif") {
			$this->db->where("statut","actif");
		} elseif ($statut == "bloque") {
			$this->db->where("statut","bloque");
		}

		if( ! empty($date_debut) && empty($date_fin)) {
			$debut = strtotime($date_debut);
			$this->db->where("creation >=",$debut);
		} elseif(empty($date_debut) && ! empty($date_fin)) {
			$fin = strtotime($date_fin);
			$this->db->where("creation <=",$fin);
		} elseif( ! empty($date_debut) && ! empty($date_fin)) {
			$debut = strtotime($date_debut);
			$fin = strtotime($date_fin);
			if($debut == $fin) {
				$fin += 24*60*60;
			}
			$this->db->where("creation >=",$debut);
			$this->db->where("creation <=",$fin);
		}

		if($ordre != "ASC") {
			$ordre = "DESC";
		}

		switch($tri) {
			case "alphabetique":
				$this->db->order_by("nom",$ordre);
			break;
			case "date":
				$this->db->order_by("creation",$ordre);
			break;
		}

		return $this->db->select('*')
		  		 ->from($this->utilisateurs)
		  		 ->limit($limit,$offset)
				 ->get()
				 ->result();
	}

	public function recherche_documents($nom_original, $proprietaire, $date_debut, $date_fin, $type, $date_debut_prise, $date_fin_prise, $tag, $tri = "alphabetique", $ordre = "ASC", $limit = 100, $offset = 0){

		$utilisateur = $this->recherche_utilisateur($proprietaire,"tous","","","","alphabetique","ASC",1000,0);

		//if( ! empty($tag) && count($id_tag) > 0){
		if( ! empty($tag) ){
			$tags = explode ( "," , $tag );
			$id_tag = $this->recherche_tag($tags, "document");
		}

		if($type == "image"){
			if( ! empty($date_debut_prise) && empty($date_fin_prise)) {
				$debut_prise = strtotime($date_debut_prise);
				$this->db->where("date_prise >=",$debut_prise);
			} elseif(empty($date_debut_prise) && ! empty($date_fin_prise)) {
				$fin_prise = strtotime($date_fin_prise);
				$this->db->where("date_prise <=",$fin_prise);
			} elseif( ! empty($date_debut_prise) && ! empty($date_fin_prise)) {
				$debut_prise = strtotime($date_debut_prise);
				$fin_prise = strtotime($date_fin_prise);
				if($debut_prise == $fin_prise) {
					$fin_prise += 24*60*60;
				}
				$this->db->where("creation >=",$debut);
				$this->db->where("creation <=",$fin);
			}
			if( ! empty($date_debut_prise) || ! empty($date_fin_prise)) {
				$images = $this->db->select("iddocument")->from("images")->get()->result();
				foreach($images as $i) {
					$id_images[] = $i->iddocument;
				}
				$this->db->where_in('iddocument', $id_images);
			}
		}

		if(!empty($id_tag)){
			$this->db->where_in('iddocument', $id_tag);
		}

		if(! empty($utilisateur)){
			$id_utilisateur = array();
			foreach($utilisateur as $u) {
				$id_utilisateur[] = $u->idutilisateur;
			}
			$this->db->where_in('idutilisateur', $id_utilisateur);
		}

		if( ! empty($nom_original)) {
			$this->db->like("nom_original",$nom_original);
		}

		if( ! empty($date_debut) && empty($date_fin)) {
			$debut = strtotime($date_debut);
			$this->db->where("creation >=",$debut);
		} elseif(empty($date_debut) && ! empty($date_fin)) {
			$fin = strtotime($date_fin);
			$this->db->where("creation <=",$fin);
		} elseif( ! empty($date_debut) && ! empty($date_fin)) {
			$debut = strtotime($date_debut);
			$fin = strtotime($date_fin);
			if($debut == $fin) {
				$fin += 24*60*60;
			}
			$this->db->where("creation >=",$debut);
			$this->db->where("creation <=",$fin);
		}

		if( ! empty($type)) {
			$this->db->where("type",$type);
		}

		if($ordre != "ASC") {
			$ordre = "DESC";
		}

		switch($tri) {
			case "alphabetique":
				$this->db->order_by("nom_original",$ordre);
			break;
			case "date":
				$this->db->order_by("creation",$ordre);
			break;
			case "type":
				$this->db->order_by("type",$ordre);
			break;
		}

		return $this->db->select('*')
		  		 ->from($this->documents)
		  		 ->limit($limit,$offset)
				 ->get()
				 ->result();
	}

	public function recherche_tag($tags,$type = "document"){
		if($type == "document") {
			$this->db->select('iddocument')
					->from($this->tag_document)
					->join('tags', 'tags.tag = '.$this->tag_document.'.tag');
		} elseif ($type == "projet") {
			$this->db->select('idprojet')
					->from($this->tag_projet)
					->join('tags', 'tags.tag = '.$this->tag_projet.'.tag');
		}

		foreach($tags as $t){
			$this->db->or_like('tags.tag',$t);
		}

		$result = $this->db->order_by("occurrence")
				 ->get()
				 ->result();

		$typeid = "id".$type;
		$id = array();
		foreach($result as $r) {
			$id[] = $r->$typeid;
		}
		return $id;
	}

}