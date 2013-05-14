<?php
class Administration extends CI_Controller {
	
	public function __construct(){
		parent::__construct();
		if(!$this->profil_model->verifier_connexion()){
			$this->session->set_flashdata("erreur","Veuillez vous connecter.");
			redirect("session/connexion");
		}
		if(!$this->profil_model->verifier_superAdmin()){
			$this->session->set_flashdata("erreur","Vous n'avez pas les droits requis.");
			redirect("session/connexion");
		}
	}
	
	// Accueil administration
	public function index() {
		$this->liste_utilisateurs();
	}
	
	// Lister tous les utilisateurs
	public function liste_utilisateurs() {
		$this->load->model("utilisateur_model");

		// On liste les utilisateur en attente
		$data["utilisateurs"] = $this->utilisateur_model->recuperer_liste_utilisateurs("bloque");
		$data["nbattente"]    = count($data["utilisateurs"]);
		$data["t_sub"]        = "admin";
		$this->template->render('administration/accueil',$data);	
	}
	
	/////////////////////////////////////////
	// Système de gestion des inscriptions //
	/////////////////////////////////////////

	// Accepter membre
	public function accepter_membre($idutilisateur) {
		// Vérification id fournis
		if(is_null($idutilisateur)) {
			echo 'fail';
			return false;
		}

		// Chargement model
		$this->load->model("utilisateur_model");

		// Vérification utilisateur en attente
		$utilisateur = $this->utilisateur_model->recuperer_utilisateur($idutilisateur);

		// Si oui, on l'accepte en temps que membre
		if ($utilisateur->statut == "bloque") {
			// On active
			$result = $this->utilisateur_model->activer_membre($idutilisateur);

			if ($result) {
				echo 'succes';
				return true;
			}
			else {
				echo 'Erreur dans l\'activation du membre';
				return false;
			}
		}

		// Si non, on retourne un fail
		else {
			echo 'L\'utilisateur n\'est pas bloqué!';
			return false;
		}
	}

	// Accepter invité
	public function accepter_invite($idutilisateur) {
		// Vérification id fournis
		if(is_null($idutilisateur)) {
			echo 'fail';
			return false;
		}

		// Chargement model
		$this->load->model("utilisateur_model");

		// Vérification utilisateur en attente
		$utilisateur = $this->utilisateur_model->recuperer_utilisateur($idutilisateur);

		// Si oui, on l'accepte en temps que membre
		if ($utilisateur->statut == "bloque") {
			// On active
			$result = $this->utilisateur_model->activer_invite($idutilisateur);

			if ($result) {
				echo 'succes';
				return true;
			}
			else {
				echo 'Erreur dans l\'activation du membre';
				return false;
			}
		}

		// Si non, on retourne un fail
		else {
			echo 'L\'utilisateur n\'est pas bloqué!';
			return false;
		}
	}

	// Refuser inscription
	public function refuser_inscription($idutilisateur) {
		// Vérification id fournis
		if(is_null($idutilisateur)) {
			echo 'fail';
			return false;
		}

		// Chargement model
		$this->load->model("utilisateur_model");

		// Vérification utilisateur en attente
		$utilisateur = $this->utilisateur_model->recuperer_utilisateur($idutilisateur);

		// Si oui, on l'accepte en temps que membre
		if ($utilisateur->statut == "bloque") {
			// On active
			$result = $this->utilisateur_model->refuser_inscription($idutilisateur);

			if ($result) {
				echo 'succes';
				return true;
			}
			else {
				echo 'Erreur dans l\'activation du membre';
				return false;
			}
		}

		// Si non, on retourne un fail
		else {
			echo 'L\'utilisateur n\'est pas bloqué!';
			return false;
		}
	}


	// Modifier l'utilisateur
	public function modifier_utilisateur($idutilisateur) {
		if(!is_null($idutilisateur)) {
			$this->load->model("utilisateur_model");
			$data["utilisateur"] = $this->utilisateur_model->recuperer_utilisateur($idutilisateur);

			//vérification du formulaire
			$this->load->library('form_validation');
			
			$this->form_validation->set_rules('email', 'Email', 'required|max_length[256]|valid_email');				
			$this->form_validation->set_rules('nom', 'Nom', 'required|max_length[100]');
			$this->form_validation->set_rules('prenom', 'Prenom', 'required|max_length[100]');
			$this->form_validation->set_rules('adresse', 'Adresse', 'required');
			$this->form_validation->set_rules('statut', 'Statut', 'required');

			//si formulaire envoyé et bon
			if ($this->form_validation->run() == TRUE){	
				if ($this->input->post("superadmin", TRUE) == "on")
					$superadmin = true;
				else
					$superadmin = false;

				if ($this->input->post("invite", TRUE) == "on")
					$invite = true;
				else
					$invite = false;
					
				$this->utilisateur_model->modifier_utilisateur($idutilisateur, $this->input->post("email",TRUE), $this->input->post("nom",TRUE), 
															$this->input->post("prenom",TRUE), $superadmin, $this->input->post("adresse",TRUE),
															$data["utilisateur"]->image, $this->input->post("statut",TRUE), $invite);
															
				//$this->session->set_flashdata("succes","Données modifiées.");
				redirect("administration");
			//si formulaire incomplet ou non envoyé	
			}else{
				$this->template->render('administration/modifier_utilisateur',$data);
			}
		}
	}
	
	// Envoyer message à tous les membres
	public function contacter_utilisateurs($destCat = "all", $id = null) {
	
		// Récupération des paramètres
		$objet = $this->input->post('objet');
		$message = $this->input->post('message');
		
		// On vérifie que les paramètres sont fournis
		if (empty($destCat) OR empty($objet) OR empty($message)) {
			echo "Paramètres incomplets";
		}
	
		// On continue
		else {
			// Chargement library
			$this->load->library('email');

			// Chargement données
			$expediteur = $this->profil_model->recuperer_mes_donnees();

			// Si on veut contacter tout les utilisateurs
			if ($destCat == "all") {
				$this->load->model("utilisateur_model");

				$userList = $this->utilisateur_model->recuperer_liste_utilisateurs();

				// Creation du tableau final
				$mailList = array();
				foreach ($userList as $row) {
					array_push($mailList, $row->email);
				}
			}
			// Si on veut contacter un utilisateur en particulier
			else if ($destCat == "user") {
				$this->load->model("messagerie_model");

				$user = $this->messagerie_model->recuperer_mail_membre($id);
				$mailList = $user->email;
			}
			
			// Mail destinataire
			$this->email->from($expediteur->email, $expediteur->nom.' '.$expediteur->prenom);
			$this->email->to($mailList);
			$this->email->subject($objet);
			$this->email->message($message);
			$resultat = $this->email->send();
			
			// Page de confirmation
			if ($resultat)
				echo "Success";
			else
				echo "SMTP Fail";
		}
	}
}