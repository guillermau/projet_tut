<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Session extends CI_Controller {

	public function __construct(){
		parent::__construct();
	}

	public function index()
	{
		$this->connexion();
	}

	// Connexion
	public function connexion() {
		if($this->session->flashdata("referer")){
			$this->session->keep_flashdata('referer');
			$referer = $this->session->flashdata("referer");
		} else {
			$referer = "mes-projets";
		}
		if($this->profil_model->verifier_connexion()){
			redirect("mes-projets");
		}
		
		$this->load->library('form_validation');

		// Règles du formulaire
		$this->form_validation->set_rules('email', 'email', 'required|valid_email');
		$this->form_validation->set_rules('mdp', 'mot de passe', 'required');

		// Affichage formulaire
		if ($this->form_validation->run() == FALSE) {
			
			$this->template->render('connexion/formulaire');
		
		}

		// Envoi du formulaire
		else {
			// Récupération usrname + pwd
			$email = $this->input->post('email');
			$mdp = $this->input->post('mdp');

			// Model de connexion (vérification et création de session)
			$statut = $this->profil_model->connexion($email, $mdp);
			switch($statut) {
				case "actif":
					redirect($referer);
					break;
				case "clos":
				case "bloque":
					$data = array( "statut" => $statut , "email" => $email );
					$this->template->render('connexion/fail', $data);
					break;
				case false:
				default:
					$data = array('erreur' => 'Email ou mot de passe inconnus.');
					$this->template->render('connexion/formulaire',$data);
			}
		}
 
	}

	// Deconnexion
	public function deconnexion() {
		$this->profil_model->deconnexion();
		redirect("");
	}

	// Récupération du MDP
	public function recup_mdp() {
		// Chargement librairie
		$this->load->library('form_validation');
		
		// Règles du formulaire
		$this->form_validation->set_rules('email', 'email', 'required|valid_email');
		
		// Affichage formulaire
		if ($this->form_validation->run() == FALSE) {
			$this->template->render('connexion/recup_mdp/formulaire_email');
		}
		
		// Envoi du formulaire
		else {
			// Récupération usrname + pwd
			$email = $this->input->post('email', TRUE);
			$sos_code = $this->profil_model->generer_sos_mdp($email);
			$user_data = $this->utilisateur_model->recuperer_utilisateur_email($email);

			// Vérification statut
			if($sos_code == false) {
				$data = array("email" => $email);
				$this->template->render('connexion/recup_mdp/fail', $data);
			}
			else {
				// Chargement library
				$this->load->library('email');
			
				// Envoi du mail
				$message =
"Bonjour ".$user_data->nom." ".$user_data->prenom.",
Vous venez de demander une récupération de votre mot de passe sur l'Entrepôt de Données Écologiques du LBBE.
Pour reintialiser votre mot de passe, merci de cliquer sur le lien suivant :
".site_url("session/initialisation-mdp")."/".urlencode($sos_code)."

Si vous n'êtes pas à l'origine de cette demande, ignorez simplement cet e-mail.

Cordialement,
L'équipe du LBBE.

--------------------------------------------------------
Ceci est un mail envoyé automatiquement. Si vous souhaitez contacter les administrateurs, merci d'utiliser le formulaire approprié.
--------------------------------------------------------";
			
				// Mail destinataire
				$this->email->from('noreply@lbbe.univ-lyon1.fr', 'Administrateurs LBBE');
				$this->email->to($email);
				$this->email->subject("Entrepôt de Données Écologiques - Récupération du mot de passe");
				$this->email->message($message);
				$resultat = $this->email->send();
			
				// Redirection
				$this->session->set_flashdata("erreur","Un email contenant le lien pour reinitialiser votre mot de passe a été envoyé.");
				redirect("session/connexion");
			}
		}
	}
	
	// Reintialisation mdp
	public function init_mdp($sos_code) {
	
		// Vérification du sos code
		$sos_code = urldecode($sos_code);
		$valide = $this->profil_model->verifier_sos_mdp($sos_code);
		
		// Si ok on continue en affichant le formulaire de changement mdp
		if($valide) {
			
			// Chargement librairie
			$this->load->library('form_validation');
			
			// Règles du formulaire
			$this->form_validation->set_rules('mdp', 'nouveau mot de passe', 'required|max_length[256]');
			$this->form_validation->set_rules('mdpconf', 'confirmation', 'required|matches[mdp]');
			
			// Execution
			if ($this->form_validation->run() == TRUE) {
				// Chargement model
				$this->load->model('utilisateur_model');
				
				// Récupération mail
				$string  = explode("$",$sos_code);
				$email   = $string[0];
				$usrData = $this->utilisateur_model->recuperer_utilisateur_email($email);
			
				// Récup.ration des paramètres
				$mdp = $this->input->post('mdp', TRUE);
				$idusr = $usrData->idutilisateur;
				
				// Modification mdp
				$result = $this->utilisateur_model->modifier_mdp_utilisateur($idusr, $mdp);
				
				// Resultat
				if($result) {
					$this->session->set_flashdata("erreur","Mot de passe réintialisé avec succès.");
					redirect("session/connexion");
				}
				else {
					$this->session->set_flashdata("erreur","Erreur, veuillez contacter les administrateurs.");
					redirect("session/connexion");
				}
			}
			
			// Execution
			else {
				$this->template->render('connexion/recup_mdp/formulaire_mdp');
			}
		}
		
		// Sinon erreur
		else {
			$this->session->set_flashdata("erreur","Le lien de récupération est corrompu. Veuillez en demander un nouveau.");
			redirect("session/connexion");
		}
	}

	// Inscription
	public function inscription() {
		$this->load->model('utilisateur_model');
		$this->load->model('upload_model');

		//vérification formulaire
		$this->load->library('form_validation');
			
		$this->form_validation->set_rules('email', 'Email', 'required|max_length[256]|valid_email|is_unique[utilisateurs.email]');
		$this->form_validation->set_rules('nom', 'Nom', 'required|max_length[100]');
		$this->form_validation->set_rules('prenom', 'Prenom', 'required|max_length[100]');
		$this->form_validation->set_rules('adresse', 'Adresse', 'required');
		$this->form_validation->set_rules('mdp', 'Nouveau mot de passe', 'required|max_length[256]');
		$this->form_validation->set_rules('mdpconf', 'Confirmation de mot de passe', 'required|matches[mdp]');
			
		//si formulaire rempli et bon
		if ($this->form_validation->run() == TRUE) {
			//création de l'utilisateur
			$id = $this->utilisateur_model->creer_utilisateur($this->input->post("email",TRUE), 
															  $this->input->post("nom",TRUE), 
															  $this->input->post("prenom",TRUE), 
															  $this->input->post("mdp",TRUE), 
															  0, //l'utilisateur n'est pas superadmin
															  $this->input->post("adresse",TRUE));
			if(empty($id)){
				$data = array('erreur' => 'Erreur dans la création de l\'utilisateur.');
				$this->template->render("connexion/creer_compte",$data);
			}
			
			// Envoi de l'image utilisateur
			$image = $this->upload_model->upload_image_profil("image", $id, false);

			//si tout a bien marché, renvoie a la message de succes
			$this->template->render("connexion/utilisateur_cree");
		}else{
			//si formulaire n'est pas bon ou pas encore rempli
			$this->template->render("connexion/creer_compte");
		} 	
	}

}