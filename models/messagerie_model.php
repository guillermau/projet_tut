<?php 

class Messagerie_model extends CI_Model {
	// Nom de la table utilisateurs & projets
	protected $tUtilisateurs 	= 'utilisateurs';
	protected $tProjets 		= 'projets';
	protected $tMembre			= 'membres';

	public function __construct(){
		parent::__construct();
	}

	// Récupérer mails administrateurs
	public function recuperer_mails_administrateurs() {
		return $this->db->select('email')
						->from($this->tUtilisateurs)
						->where('superadmin', 1)
						->get()
						->result();
	}
	
	// Récupérer mail membre
	public function recuperer_mail_membre($id) {
		$this->load->model("utilisateur_model");
		
		$exist = $this->db->select("idutilisateur")
						  ->from($this->tUtilisateurs)
						  ->where("idutilisateur", $id)
						  ->count_all_results();
						  
		if($exist == 1)
			return $this->utilisateur_model->recuperer_utilisateur($id);
		else {
			throw new Exception('Utilisateur innexistant', 701);
			return false;
		}
	}
	
	// Récupérer mails du projets
	public function recuperer_mails_projet($idprojet) {
		$exist = $this->db->select("idprojet")
						  ->from($this->tProjets)
						  ->where("idprojet", $idprojet)
						  ->count_all_results();
						  
		if($exist == 1) {
			return $this->db->select('email')
							->from($this->tMembre)
							->join($this->tUtilisateurs, 'membres.idutilisateur = utilisateurs.idutilisateur')
							->where("idprojet", $idprojet)
							->get()
							->result();
		}
		else {
			throw new Exception('Projet innexistant', 701);
			return false;
		}
	}
}
?>