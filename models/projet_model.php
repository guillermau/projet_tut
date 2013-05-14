<?php 

class Projet_model extends CI_Model{
	// Nom des tables projet, documents
	protected $tProjet		= 'projets';
	protected $tDocument	= 'documents';
	protected $tMembre		= 'membres';
	protected $tTagCons		= 'tag_conseille';
	protected $tGroupe		= 'groupes';
	protected $tDroitG		= 'droit_groupe';
	protected $tUtilisateur = 'utilisateurs';
	protected $tagProjet    = 'tag_projet';
	protected $typeDefaut   = 'Administrateur';
	protected $droitsDft = array(
							  'ecriture'      => true,
							  'lecture'		  => true,
							  'upload'		  => true);

	function __construct()
    {
        parent::__construct();
    }
    
	/*
	** Gestions des projets
	*/

	// Tous les projets
	public function lister_projets() {
		return $this->db->select('*')
						->from($this->tProjet)
						->where("statut","actif")
						->order_by('nom')
						->get()
						->result();
	}

	// Projet par id
	public function recuperer_projet($idprojet) {
		$projet =  $this->db->select('*')
							->from($this->tProjet)
							->where('idprojet', $idprojet)
							->get()
							->row();
		$this->load->model('tag_model');
		$projet->tags = $this->tag_model->recuperer_tags_projet($idprojet);
		return $projet;
	}
	
	// Créer un projet
	public function creer_projet($nom, $description, $idadmin, $tags) {
		$data = array('nom'			=> $nom,
					  'statut'		=> "actif",
					  'creation'	=> date("Y-m-d h:i:s"),
					  'description'	=> $description);

		$this->db->insert($this->tProjet, $data);
		$idprojet = $this->db->insert_id();
		$idgroupe = $this->creer_groupe($idprojet,
										$this->typeDefaut,
										$this->droitsDft['lecture'],
										$this->droitsDft['ecriture'],
										$this->droitsDft['upload']);
		$this->ajouter_membre($idadmin, $idprojet,$idgroupe);

		$data = array('groupe_adm' => $idgroupe);
		$this->db->where('idprojet',$idprojet)->update($this->tProjet, $data);

		if( ! empty($tags)){
			$this->load->model('tag_model');
			$tags = explode ( "," , trim($tags) );
			foreach ($tags as $t){
				$this->tag_model->ajouter_tag($t);
				$data = array('tag'	=> trim($t),
							  'idprojet' => $idprojet);

				$this->db->insert($this->tagProjet, $data);
			}
			
		}

		return $idprojet;
	}

	// Modifier le projet
	public function modifier_projet($idprojet, $nom, $description, $tags) {
		$this->load->model('tag_model');
		$this->tag_model->supprimer_tags_projet($idprojet);
		if(! empty($tags)){
			$tags = explode ( "," , $tags );
			foreach ($tags as $t){
				$this->tag_model->ajouter_tag($t);
				$data = array('tag'	=> trim($t),
							  'idprojet' => $idprojet);

				$this->db->insert($this->tagProjet, $data);
			} 	
		}
		$data = array('nom'			=> $nom,
					  'description'	=> $description);
		return $this->db->where('idprojet',$idprojet)->update($this->tProjet, $data);
	}

	// Supprimer le projet
	public function supprimer_projet($idprojet) {
		$this->load->model("tag_model");
		$data = array('statut' => 'clos');
		$this->tag_model->supprimer_tags_projet($idprojet);
		return $this->db->where('idprojet', $idprojet)
				 		->update($this->tProjet, $data);
	}

	/*
	** Membres d'un projet
	*/

	// Tous les membres d'un projet
	public function lister_membres($idprojet) {
		$query = $this->db->select('*')
						->from($this->tMembre)
						->join($this->tUtilisateur, $this->tMembre.".idutilisateur = ".$this->tUtilisateur.".idutilisateur")
						->where('idprojet', $idprojet)
						->order_by($this->tUtilisateur.'.nom')
						->get();
		$data = array();
		if ($query->num_rows() > 0) {
			$data = array();
			foreach ($query->result() as $row)
			{
				$data[$row->idutilisateur] = $row;
			}
			return $data;
		} else {
			return false;
		}
	}

	// Tous les membres d'un projet
	public function lister_membres_par_groupe($idprojet) {
		$query = $this->db->select('*')
						  ->from($this->tMembre)
						  ->join($this->tUtilisateur,$this->tUtilisateur.".idutilisateur = ".$this->tMembre.".idutilisateur")
						  ->where('idprojet', $idprojet)
						  ->order_by($this->tUtilisateur.'.nom')
						  ->get();

		if ($query->num_rows() > 0) {
			$data = array();
			foreach ($query->result() as $row)
			{
			   $data[$row->idgroupe][] = $row;
			}
			return $data;
		} else {
			return false;
		}
	}

	// Liste des groupes d'un projet
	public function lister_groupes($idprojet) {
		$query = $this->db->select('*')
						  ->from($this->tGroupe)
						  ->where('idprojet', $idprojet)
						  ->order_by('type')
						  ->get();
		$data = array();
		if ($query->num_rows() > 0) {
			$data = array();
			foreach ($query->result() as $row)
			{
				$data[$row->idgroupe] = $row;
			}
			return $data;
		} else {
			return false;
		}
	}

	// Liste des groupes d'un projet avec ces droits
	public function lister_groupes_droits($idprojet) {
		$query = $this->db->select('*')
						  ->from($this->tGroupe)
						  ->where($this->tGroupe.'.idprojet', $idprojet)
						  ->join($this->tDroitG, $this->tDroitG.".idgroupe = ".$this->tGroupe.".idgroupe")
						  ->order_by('type')
						  ->get();
		$data = array();
		if ($query->num_rows() > 0) {
			$data = array();
			foreach ($query->result() as $row)
			{
				$data[$row->idgroupe] = $row;
			}
			return $data;
		} else {
			return false;
		}
	}

	// Vérifier si un groupe appartient au projet
	public function groupe_appartient($idgroupe, $idprojet) {
		$query = $this->db->select('idgroupe')
						  ->from($this->tGroupe)
						  ->where('idprojet', $idprojet)
						  ->where('idgroupe', $idgroupe)
						  ->get();
		if ($query->num_rows() > 0) {
			return $query->row()->idgroupe;
		} else {
			return false;
		}
	}

	// Ajouter un membre
	public function ajouter_membre($idutilisateur, $idprojet, $idgroupe) {
		$data = array('idutilisateur'	=> $idutilisateur,
					  'idprojet'		=> $idprojet,
					  'idgroupe'		=> $idgroupe);

		return $this->db->insert($this->tMembre, $data);
	}

	// Supprimer un membre
	public function supprimer_membre($idutilisateur, $idprojet) {
		$data = array('idutilisateur'	=> $idutilisateur,
					  'idprojet'		=> $idprojet);

		return $this->db->delete($this->tMembre, $data);
	}

	// Compter n de documents par projet
	public function nombre_documents_prj($idprojet) {
		return $this->db->where('idprojet', $idprojet)
		                ->count_all_results($this->tDocument);
	}

	// Compter n de membres
	public function nombre_membres_prj($idprojet) {
		return $this->db->where('idprojet', $idprojet)
						->count_all_results($this->tMembre);
	}

	// Compte n de invités
	public function nombre_invites_prj($idprojet) {
		return $this->db->where('idprojet', $idprojet)
						->where('utilisateurs.invite', 1)
						->join('utilisateurs','utilisateurs.idutilisateur = '.$this->tMembre.'.idutilisateur')
						->count_all_results($this->tMembre);
	}

	// Récuperer tags conseillées
	public function lister_tags_prj($idprojet) {
		return $this->db->select('nom')
						->from($this->tTagCons)
						->where('idprojet', $idprojet)
						->get()
						->row();
	}

	/*
	** Gestion des groupes
	*/

	// Créer un groupe
	public function creer_groupe($idprojet, $type, $lecture, $ecriture, $upload) {
		$data = array('type'			=> $type,
					  'idprojet'		=> $idprojet);

		$this->db->insert($this->tGroupe, $data);
		$idGroupe = $this->db->insert_id();
		$data = array('idgroupe'		=> $idGroupe,
					  'lecture'			=> $lecture,
					  'ecriture'		=> $ecriture,
					  'upload'			=> $upload);

		$this->db->insert($this->tDroitG, $data);
		return $idGroupe;
	}

	// Modifier les droits d'un groupe
	public function modifier_droits_gr($idgroupe, $lecture, $ecriture, $upload) {
		$data = array(
			'lecture'		=> $lecture,
			'ecriture'		=> $ecriture,
			'upload'		=> $upload);
		$this->db->where('idgroupe', $idgroupe)
	 			 ->update($this->tDroitG,$data);
	}

	// Suprimer un groupe
	public function supprimer_groupe($idgroupe) {
		// Vérifie si le groupe n'est pas un groupe admin d'un projet
		$query = $this->db->select('*')
						  ->from($this->tProjet)
						  ->where('groupe_adm', $idgroupe)
						  ->get();

		if ($query->num_rows() == 0)
		{
			return $this->db->where('idgroupe', $idgroupe)->delete($this->tGroupe);
		} else {
			return false;
		}
	}

	// Changer groupe du membre
	public function modifier_groupe_mbr($idutilisateur, $idprojet, $idancgroupe, $idnouvgroupe) {
		$this->supprimer_membre($utilisateur, $idprojet);
		return $this->ajouter_membre($idutilisateur, $idprojet, $idnouvgroupe);
	}	

	// retourner droits d'un groupe 
	public function verifier_droit_groupe($idgroupe){
		if($idgroupe == "superadmin") {
			$droits = new StdClass();
			$droits->ecriture = true;
			$droits->lecture  = true;
			$droits->upload   = true;
			return $droits; 
		}
		return $this->db->where('idgroupe', $idgroupe)
					->from ($this->tDroitG)
					->get()
					->row();

	}
}

?>