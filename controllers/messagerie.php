<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Messagerie extends CI_Controller {

	public function __construct(){
		parent::__construct();
		if(!$this->profil_model->verifier_connexion()){
			$this->session->set_flashdata("erreur","Veuillez vous connecter.");
			redirect("session/connexion");
		}
		$this->load->model("messagerie_model");
		$this->load->model("utilisateur_model");
	}

	public function index()
	{
		$this->connexion();
	}

	// Contacter Administrateur
	public function contacter_admin() {
	
		// Récupération des paramètres
		$objet = $this->input->post('objet');
		$message = $this->input->post('message');
		
		// On vérifie que les paramètres sont fournis
		if (empty($idDest) AND empty($objet) AND empty($message)) {
			echo "Paramètres incomplets";
		}
	
		// On continue
		else {
			// Récupération des données
			$data["utilisateur"] = $this->profil_model->recuperer_mes_donnees();
			$objectList = $this->messagerie_model->recuperer_mails_administrateurs();
			
			// Chargement library
			$this->load->library('email');

			// Creation du tableau final
			$mailList = array();
			foreach ($objectList as $row) {
				array_push($mailList, $row->email);
			}
			
			// Mail destinataire
			$this->email->from($data["utilisateur"]->email, $data["utilisateur"]->nom.' '.$data["utilisateur"]->prenom);
			$this->email->to($mailList);
			$this->email->subject($objet);
			$this->email->message($message);
			$resultat = $this->email->send();
			
			// Copie envoyeur
			$this->email->from($data["utilisateur"]->email, $data["utilisateur"]->nom.' '.$data["utilisateur"]->prenom);
			$this->email->to($data["utilisateur"]->email);
			$this->email->subject("Copie : ".$objet);
			$this->email->message($message);
			$this->email->send();
			
			// Page de confirmation
			if ($resultat)
				echo "Success";
			else
				echo "SMTP Fail";
		}
	}

	// Contacter utilisateur
	public function contacter_util() {
		$this->load->library('form_validation');
			
		$this->form_validation->set_rules('idDest', 'Destinataire', 'required|is_natural');
		$this->form_validation->set_rules('objet', 'Objet', 'required|max_length[256]');
		$this->form_validation->set_rules('message', 'Message', 'required');
		
		//si formulaire rempli et bon on continue
		if ($this->form_validation->run() == TRUE)
		{
			try {
				$idDest = $this->input->post('idDest', TRUE);
				$objet = $this->input->post('objet', TRUE);
				$message = $this->input->post('message', TRUE);

				// Récupération des données
				$data["utilisateur"] = $this->profil_model->recuperer_mes_donnees();
				$data["destinataire"] = $this->messagerie_model->recuperer_mail_membre($idDest);

				// Chargement library
				$this->load->library('email');
				
				// Mail destinataire
				$this->email->from($data["utilisateur"]->email, $data["utilisateur"]->nom.' '.$data["utilisateur"]->prenom);
				$this->email->to($data["destinataire"]->email);
				$this->email->subject($objet);
				$this->email->message($message);
				$resultat = $this->email->send();
				
				// Copie envoyeur
				$this->email->from($data["utilisateur"]->email, $data["utilisateur"]->nom.' '.$data["utilisateur"]->prenom);
				$this->email->to($data["utilisateur"]->email);
				$this->email->subject("Copie : ".$objet);
				$this->email->message($message);
				$this->email->send();
				
				// Page de confirmation
				if ($resultat)
					echo "succes";
				else
					echo "SMTP Fail";
			} catch (Exception $e) {
				echo "L'utilisateur n'existe pas";
			}
		} else {
			echo validation_errors();
		}
	}

	// Envoyer invitation
	public function envoyer_invitation() {
		
	}

	// Contacter membres projet
	public function contacter_membre_projet() {
		$this->load->library('form_validation');
			
		$this->form_validation->set_rules('idProjet', 'Projet', 'required|is_natural');
		$this->form_validation->set_rules('objet', 'Objet', 'required|max_length[256]');
		$this->form_validation->set_rules('message', 'Message', 'required');
		
		//si formulaire rempli et bon on continue
		if ($this->form_validation->run() == TRUE)
		{
			// Récupération des paramètres
			$idProjDest = $this->input->post('idProjet');
			$objet = $this->input->post('objet');
			$message = $this->input->post('message');

			try {
				// Récupération des données
				$data["utilisateur"] = $this->profil_model->recuperer_mes_donnees();
				$objectList = $this->messagerie_model->recuperer_mails_projet($idProjDest);
				
				// Creation du tableau final
				$mailList = array();
				foreach ($objectList as $row) {
					array_push($mailList, $row->email);
				}
				
				// Chargement library
				$this->load->library('email');
				
				// Mail destinataire
				$this->email->from($data["utilisateur"]->email, $data["utilisateur"]->nom.' '.$data["utilisateur"]->prenom);
				$this->email->to($mailList);
				$this->email->subject($objet);
				$this->email->message($message);
				$resultat = $this->email->send();
				
				// Copie envoyeur
				$this->email->from($data["utilisateur"]->email, $data["utilisateur"]->nom.' '.$data["utilisateur"]->prenom);
				$this->email->to($data["utilisateur"]->email);
				$this->email->subject("Copie : ".$objet);
				$this->email->message($message);
				$this->email->send();
				
				// Page de confirmation
				if ($resultat)
					echo "succes";
				else
					echo "SMTP Fail";
			} catch (Exception $e) {
				echo "Le projet n'existe pas";
			}
		}
	}

}