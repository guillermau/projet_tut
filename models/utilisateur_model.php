<?php 

require_once(APPPATH.'/libraries/PasswordHash.php');

class Utilisateur_model extends CI_Model {
	// Nom de la table utilisateurs
	protected $table = 'utilisateurs';

	private $ci;

	public function __construct(){
		parent::__construct();
		// Récupère les données du fichier de configuration "config/connexion.php"
		$this->ci =& get_instance();
	 	$this->ci->config->load('connexion');
	}

	// Créer un utilisateur
	public function creer_utilisateur($email, $nom, $prenom, $mdp, $superadmin, $adresse) {
		$hasher = new PasswordHash(
			$this->ci->config->item('iterations_hash_mdp', 'connexion'), 
			$this->ci->config->item('mdp_portable', 'connexion')
		);

		$mdp = $hasher->HashPassword($mdp);
		$data = array('email'		=> $email,
					  'nom' 		=> $nom,
					  'prenom' 		=> $prenom,
					  'mdp'			=> $mdp,
					  'superadmin'	=> $superadmin,
					  'adresse'		=> $adresse,
					  'creation'	=> date("Y-m-d h:i:s"),
					  'connexion'	=> date('Y-m-d h:i:s'),
					  'image'		=> "",
					  'statut'		=> 'bloque',
					  'invite'		=> 1);
		
		$this->db->insert($this->table, $data);
		return $this->db->insert_id();
	}
        
        //Ajoute la justification d'inscription
	public function justifier_inscription($id,$justification){
                $data = array(  'id' => $id,
                                'justification' => $justification);
                
                $this->db->insert('justification', $data);
                return $this->db->insert_id();
        }
        
        

        // Récupérer un utilisateur en connaissant son identifiant
	public function recuperer_utilisateur($id) {
		return $this->db->select('*')
						->from($this->table)
						->where('idutilisateur', $id)
						->get()
						->row();
	}

	// Récupérer un utilisateur en connaissant son email
	public function recuperer_utilisateur_email($email) {
		return $this->db->select('*')
						->from($this->table)
						->where('email', $email)
						->get()
						->row();
	}
        
        public function recuperer_justification_utilisateur(){
            return $this->db->select('*')
                            ->from('justification')
                            ->get()
                            ->result();
        }
        
	// Récupérer tous les utilisateurs
	public function recuperer_liste_utilisateurs($statut = false) {
		if($statut == false || $statut == 'actif') {
			$this->db->where('statut','actif');
		} elseif ($statut == 'clos') {
			$this->db->where('statut','clos');
		} elseif ($statut == 'bloque') {
			$this->db->where('statut','bloque');
		}

		return $this->db->select('*')
						->from($this->table)
						->get()
						->result();
	}

	// Modifier un utilisateur
	public function modifier_utilisateur($idusr, $email, $nom, $prenom, $superadmin, $adresse, $image, $statut, $invite) {

		$data = array('email'		=> $email,
					  'nom' 		=> $nom,
					  'prenom' 		=> $prenom,
					  'superadmin'	=> $superadmin,
					  'adresse'		=> $adresse,
					  'image'		=> $image,
					  'statut'		=> $statut,
					  'invite'		=> $invite);

		return $this->db->where('idutilisateur', $idusr)
				 ->update($this->table, $data);
	}

	// Modifier mdp utilisateur
	public function modifier_mdp_utilisateur($idusr, $mdp){
		$hasher = new PasswordHash(
			$this->ci->config->item('iterations_hash_mdp', 'connexion'), 
			$this->ci->config->item('mdp_portable', 'connexion')
		);
		$mdp = $hasher->HashPassword($mdp);
		$data = array("mdp" => $mdp);
		return $this->db->where('idutilisateur', $idusr)
				 ->update($this->table, $data);
	}

	// Supprimer un utilisateur
	public function supprimer_utilisateur($idusr) {
		$data = array('statut'		=> 'clos');

		return $this->db->where('idutilisateur', $idusr)
				 ->update($this->table, $data);
	}

	// Reactiver un utilisateur
	public function reactiver_utilisateur($idusr) {
		$data = array('statut'		=> 'actif');

		return $this->db->where('idutilisateur', $idusr)
				 ->update($this->table, $data);
	}
	
	// afficher tous les groupes d'un utilisateur classées par projet
	public function recuperer_groupes($idusr) {
		$query = $this->db->select('*')
						->from("membres")
						->join("groupes", "membres.idgroupe = groupes.idgroupe")
						->where('membres.idutilisateur',$idusr)
						->get();
		if ($query->num_rows() > 0) {
			$data = array();
			foreach($query->result() as $row) {
				$data[$row->idprojet] = $row;
			}
			return $data;
		} else {
			return false;
		}
	}
		
	// Récupere tous les projets d'un utilisateur
	public function projets_utilisateur($idusr){
		$query = "SELECT * FROM projets WHERE statut = 'actif' AND idprojet IN (SELECT groupes.idprojet FROM membres, groupes WHERE membres.idutilisateur = ? AND membres.idgroupe = groupes.idgroupe) ORDER BY nom;";
		$data = array($idusr);
		$query = $this->db->query($query, $data); 
		if ($query->num_rows() > 0) {
			$data = array();
			foreach($query->result() as $row) {
				$data[$row->idprojet] = $row;
			}
			return $data;
		} else {
			return false;
		}
	}

	// Vérifie si un utilisateur a le droit d'acceder a un projet
	public function verifier_acces_projet($idprojet, $idutilisateur){
		if($this->profil_model->verifier_superAdmin()){
			return "superadmin";
		}
		$query = "SELECT idgroupe from membres WHERE idutilisateur = ? AND idgroupe IN (SELECT idgroupe FROM groupes WHERE idprojet = ?)";
		$data = array($idutilisateur, $idprojet);
		$result = $this->db->query($query, $data); 

		if ($result->num_rows() > 0) {
			return $result->row()->idgroupe;
		} else {
			return false;
		}
	}

	// Vérifie si l'utilisateur est admin d'un projet
	public function verifier_admin_projet($idprojet,$idutilisateur) {
		if($this->profil_model->verifier_superAdmin()){
			return "superadmin";
		}
		$query = "SELECT idgroupe from membres WHERE idutilisateur = ? AND idgroupe IN (SELECT groupe_adm FROM projets WHERE idprojet = ?)";
		$data = array($idutilisateur, $idprojet);
		$result = $this->db->query($query, $data); 

		if ($result->num_rows() > 0) {
			return $result->row()->idgroupe;
		} else {
			return false;
		}
	}

	// Vérifier les droits de l'utilisateur par rapport a un document
	public function verifier_droit($iddocument,$idutilisateur){
		$this->load->model("document_model");
		$doc = $this->document_model->recuperer_document($iddocument);
		
		if($doc->idutilisateur == $idutilisateur || $this->profil_model->verifier_superAdmin()) {
			$droits = new stdClass();
			$droits->lecture = true;
			$droits->ecriture = true;
			$droits->visualisation = true;
			return $droits;
		}
		
		$query = "SELECT * FROM droit_document WHERE iddocument = ".$iddocument." and idgroupe IN (SELECT idgroupe FROM membres WHERE idprojet = ".$doc->idprojet." AND idutilisateur = ".$idutilisateur.");";
		$result = $this->db->query($query); 
		if ($result->num_rows() > 0) {
			return $result->row();
		} else {
			$query2 = "SELECT * FROM droit_groupe WHERE idgroupe IN (SELECT idgroupe FROM membres WHERE idutilisateur = ".$idutilisateur." AND idprojet = ".$doc->idprojet.");";
			$result2 = $this->db->query($query2); 
			if ($result2->num_rows() > 0) {
				return $result2->row();
			} else {
				return false;
			}
		}
	}

	public function recuperer_id_par_noms($noms){
		$noms = rtrim(str_replace(", ", "|", $noms),'|');
		$query  = "select * from utilisateurs where concat_ws(' ',nom,prenom) REGEXP ?;";
		$data   = array($noms);
		$result = $this->db->query($query, $data); 
		return $result->result();
	}

	/////////////////////////////////////////
	// Système de gestion des inscriptions //
	/////////////////////////////////////////

	// Activer en temps que membre
	public function activer_membre($idusr) {
		$data = array('statut'		=> 'actif',
					  'invite'		=> 0);

		return $this->db->where('idutilisateur', $idusr)
						->update($this->table, $data);
	}

	// Activer en temps que invité
	public function activer_invite($idusr) {
		$data = array('statut'		=> 'actif',
					  'invite'		=> 1);

		return $this->db->where('idutilisateur', $idusr)
						->update($this->table, $data);
	}

	// Refuser inscription
	public function refuser_inscription($idusr) {
		return $this->db->where('idutilisateur', $idusr)
				 		->delete($this->table);
	}

}

?>