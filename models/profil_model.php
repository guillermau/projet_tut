<?php 

require_once(APPPATH.'/libraries/PasswordHash.php');

class Profil_model extends CI_Model {

	// Noms des tables
	protected $tUtilisateur = 'utilisateurs';
	private $salt = 'unjoursjaimangeunepomme';
	
	private $ci;

	public function __construct(){
		parent::__construct();
		$this->load->driver('cache', array('adapter' => 'file'));
		// Récupère les données du fichier de configuration "config/connexion.php"
		 $this->ci =& get_instance();
     	 $this->ci->config->load('connexion');
	}

	// Fonction de connexion
	// return : statut de la compte: actif, clos ou bloqué, ou false si erreur de connexion
	public function connexion($email, $mdp) {
		
		// Récupération couple email, mdp

		$result = $this->db->select('*')
						   ->from($this->tUtilisateur)
						   ->where('email', $email)
						   ->get()
						   ->row();

		// Comparaison des résultats
		// Si mdp idem
		$hasher = new PasswordHash(
			$this->ci->config->item('iterations_hash_mdp', 'connexion'), 
			$this->ci->config->item('mdp_portable', 'connexion')
		);

		if(!empty($result) && $hasher->CheckPassword($mdp, $result->mdp)) {
			// Si le compte est bloqué ou clos
			if(($result->statut == "bloque") || ($result->statut == "clos")) {
				return $result->statut;
			}

			// Créée une clé de securité pour valider la session (cookie) de l'utilisateur
			$result->cleSecurite = md5(rand().$result->idutilisateur.time());

			// Capture l'adresse ip de l'utilisateur pour des questions de securité
			$result->ip = $this->input->ip_address();

			// Stockage des données de l'utilisateur dans le cache pendant 12 heures
			$this->cache->delete($result->idutilisateur);
			$this->cache->save($result->idutilisateur, $result, 36000);

			// Mise à jours date de connexion
			$data = array('connexion' => date('Y-m-d h:i:s'));

			$this->db->where('idutilisateur', $result->idutilisateur)
					 ->update($this->tUtilisateur, $data);

			$array = array(
				"email" => $result->email,
				"idutilisateur" => $result->idutilisateur,
				"cle" => $result->cleSecurite,
				"nom" => $result->nom." ".$result->prenom,
				);

			$this->session->set_userdata($array);

			return $result->statut;
		}
		// Si mauvais mdp
		else {
			return false;
		}
	}

	public function deconnexion(){
		if($this->session->userdata("idutilisateur")){
			$this->cache->delete($this->session->userdata("idutilisateur"));	
		}
		$this->session->set_userdata(array('email' => '', 'idutilisateur' => '', "cle" => '', "nom" => ''));
		return $this->session->sess_destroy();
	}

	public function verifier_connexion(){
		// verifie si l'utilisateur possede une session
		if(!($id = $this->session->userdata("idutilisateur"))){
			return false;
		}

		// verifie si les données de l'utilisateur sont en cache
		if(!($utilisateur = $this->cache->get($id))){
			return false;
		}

		// verifie si la cle stocke en cache est la même que celle en session
		if(! $this->session->userdata("cle") || empty($utilisateur->cleSecurite) || ($utilisateur->cleSecurite != $this->session->userdata("cle")) ){
			return false;
		}

		return $utilisateur;
	}
	
	public function verifier_superAdmin() {
		// verifie si l'utilisateur possede une session
		if(!($idutilisateur = $this->session->userdata("idutilisateur"))){
			return false;
		}
		
		// récupère les infos de l'utilisateur courant
		$result = $this->recuperer_mes_donnees();
		if(!($result->superadmin)){
			return false;
		}
		return $result->superadmin;
	}

	public function generer_sos_mdp($email){
		$this->load->model("utilisateur_model");
		$utilisateur = $this->utilisateur_model->recuperer_utilisateur_email($email);
		
		if (empty($utilisateur))
			return false;
		if ($utilisateur->statut != "actif")
			return false;
		
		return $email."$".md5($this->salt.$utilisateur->email.$utilisateur->mdp.$utilisateur->connexion);
	}

	public function verifier_sos_mdp($sos_mdp){
		$this->load->model("utilisateur_model");
		$this->load->helper("email");
		$string = explode("$",$sos_mdp);
		if(!empty($string[0]) && valid_email($string[0])) {
			$utilisateur = $this->utilisateur_model->recuperer_utilisateur_email($string[0]);
			if($sos_mdp == $utilisateur->email."$".md5($this->salt.$utilisateur->email.$utilisateur->mdp.$utilisateur->connexion)){
				return true;
			}
		}
		return false;
	}

	public function modifier_donnees($email, $nom, $prenom, $adresse){
		if(!($idutilisateur = $this->session->userdata("idutilisateur"))){
			throw new Exception('Utilisateur non connecté',700);
			return false;
		}
		
		if(!($utilisateur = $this->cache->get($idutilisateur))){
			$this->load->model('utilisateur_model');
			$utilisateur = $this->utilisateur_model->recuperer_utilisateur($idutilisateur);
		} else {
			$utilisateur->email   = $email;
			$utilisateur->nom     = $nom;
			$utilisateur->prenom  = $prenom;
			$utilisateur->adresse = $adresse;
			$this->cache->delete($utilisateur->idutilisateur);
			$this->cache->save($utilisateur->idutilisateur, $utilisateur, 36000);
		}
		return $this->utilisateur_model->modifier_utilisateur($idutilisateur, $email, $nom, $prenom, false, $adresse, $utilisateur->image, $utilisateur->statut, $utilisateur->invite);
	}

	public function modifier_mdp($mdp_vieux,$mdp_nouveau){
		if(!($idutilisateur = $this->session->userdata("idutilisateur"))){
			throw new Exception('Utilisateur non connecté',700);
			return false;
		}

		$mdp = $this->db->select('mdp')
						   ->from($this->tUtilisateur)
						   ->where('idutilisateur', $idutilisateur)
						   ->get()
						   ->row()
						   ->mdp;

		$hasher = new PasswordHash(
			$this->ci->config->item('iterations_hash_mdp', 'connexion'), 
			$this->ci->config->item('mdp_portable', 'connexion')
		);

		if($hasher->CheckPassword($mdp_vieux, $mdp)) {
			$this->load->model('utilisateur_model');
			return $this->utilisateur_model->modifier_mdp_utilisateur($idutilisateur, $mdp_nouveau);
		} else {
			return false;
		}
	}

	public function recuperer_mes_donnees(){
		if(!($idutilisateur = $this->session->userdata("idutilisateur"))){
			throw new Exception('Utilisateur non connecté',700);
			return false;
		}
		if(!($utilisateur = $this->cache->get($idutilisateur))){
			$this->load->model('utilisateur_model');
			return $this->utilisateur_model->recuperer_utilisateur($idutilisateur);
		} else {
			return $utilisateur;
		}
		
	}

	public function recuperer_mes_projets(){
		if(!($idutilisateur = $this->session->userdata("idutilisateur"))){
			throw new Exception('Utilisateur non connecté',700);
			return false;
		}
		$this->load->model('utilisateur_model');
		if($this->verifier_superAdmin()){
			$this->load->model("projet_model");
			return $this->projet_model->lister_projets();
		}
		return $this->utilisateur_model->projets_utilisateur($idutilisateur);
	}

	public function recuperer_mes_documents(){
		if(!($idutilisateur = $this->session->userdata("idutilisateur"))){
			throw new Exception('Utilisateur non connecté',700);
			return false;
		}
		$this->load->model("document_model");
		return $this->document_model->documents_utilisateur($idutilisateur);
	}

	// Met a jour le cache de l'utilisateur
	public function maj_cache(){
		$idutilisateur = $this->session->userdata("idutilisateur");
		$utilisateur = $this->cache->get($idutilisateur);

		$cle = $utilisateur->cleSecurite;

		$this->cache->delete($utilisateur->idutilisateur);

		$this->load->model('utilisateur_model');

		$utilisateur = $this->utilisateur_model->recuperer_utilisateur($idutilisateur);
		$utilisateur->cleSecurite = $cle;
		$utilisateur->ip = $this->input->ip_address();
		$this->cache->save($utilisateur->idutilisateur, $utilisateur, 36000);
	}
}

?>