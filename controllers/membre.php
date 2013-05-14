<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Membre extends CI_Controller {

	public function __construct(){
		parent::__construct();
		if(!$this->profil_model->verifier_connexion()){
			$this->session->set_flashdata("erreur","Veuillez vous connecter.");
			redirect("session/connexion");
		}
		$this->load->model("utilisateur_model");
	}

	// Affiche les données de l'utilisateur
	public function index()
	{
		try {
			$data["utilisateur"] = $this->profil_model->recuperer_mes_donnees();
			$data["projets"] = $this->profil_model->recuperer_mes_projets();
			$data["t_title"] = $data["utilisateur"]->nom." ".$data["utilisateur"]->prenom;
			$data["t_sub"]   = "compte";
		} catch (Exception $e) {
			if($e->getCode() == 700) {
				$this->session->set_flashdata("Veuillez vous connecter.");
				redirect("session/deconnexion");
			} 
		}
		$this->template->render("membre/mon_compte",$data);
	}

	// Affiche les projets de l'utilisateur
	public function projets()
	{
		try {
			$data["utilisateur"] = $this->profil_model->recuperer_mes_donnees();
			$data["groupes"] = $this->utilisateur_model->recuperer_groupes($data['utilisateur']->idutilisateur);
			$data["projets"] = $this->profil_model->recuperer_mes_projets();
			$data["t_title"] = "Projets de ".$data["utilisateur"]->nom." ".$data["utilisateur"]->prenom;
			$data["t_sub"]   = "projets";
		} catch (Exception $e) {
			if($e->getCode() == 700) {
				$this->session->set_flashdata("Veuillez vous connecter.");
				redirect("session/deconnexion");
			} 
		}
		$this->template->render("membre/mes_projets",$data);
	}

	// Affiche tous les documents de l'utilisateur
	public function documents()
	{
		try{
			$data["utilisateur"] = $this->profil_model->recuperer_mes_donnees();
			$data["projets"]     = $this->profil_model->recuperer_mes_projets();
			$data["documents"]   = $this->profil_model->recuperer_mes_documents();
			$data["t_title"]     = "Documents de ".$data["utilisateur"]->nom." ".$data["utilisateur"]->prenom;
			$data["t_sub"]   	 = "documents";
		} catch (Exception $e) {
			if($e->getCode() == 700) {
				$this->session->set_flashdata("Veuillez vous connecter.");
				redirect("session/deconnexion");
			} 
		}
		$this->template->render("membre/mes_documents",$data);
	}

	// Modifie les données de l'utilisateur
	public function modifier(){
		$data["utilisateur"] = $this->profil_model->recuperer_mes_donnees();
		$data["t_sub"]   	 = "compte";

		//vérification du formulaire
		$this->load->library('form_validation');
		
		$this->form_validation->set_rules('email', 'Email', 'required|max_length[256]|valid_email|is_unique[utilisateurs.email]');				
		$this->form_validation->set_rules('nom', 'Nom', 'required|max_length[100]');
		$this->form_validation->set_rules('prenom', 'Prenom', 'required|max_length[100]');
		$this->form_validation->set_rules('adresse', 'Adresse', 'required');

		//si formulaire envoyé et bon
		if ($this->form_validation->run() == TRUE){	
			$this->profil_model->modifier_donnees($this->input->post("email",TRUE), $this->input->post("nom",TRUE), $this->input->post("prenom",TRUE), $this->input->post("adresse",TRUE));
			$this->session->set_flashdata("succes","Données modifiées.");
			redirect("mon-compte");
		//si formulaire incomplet ou non envoyé	
		}else{
			$this->template->render('membre/modifier',$data);
		}
	}

	// Modifie le mot de passe de l'utilisateur
	public function modifier_mdp(){
		$data["utilisateur"] = $this->profil_model->recuperer_mes_donnees();
		$data["t_sub"]   	 = "compte";

		//vérification du formulaire
		$this->load->library('form_validation');
		
		$this->form_validation->set_rules('mdpanc', 'Ancien mot de passe', 'required|max_length[256]');
		$this->form_validation->set_rules('mdp', 'Nouveau mot de passe', 'required|max_length[256]');
		$this->form_validation->set_rules('mdpconf', 'Confirmation du nouveau mot de passe', 'required|matches[mdp]');

		//si formulaire envoyé et bon
		if ($this->form_validation->run() == TRUE){	
			if($this->profil_model->modifier_mdp($this->input->post('mdpanc',TRUE),$this->input->post('mdp',TRUE))){
				$this->profil_model->maj_cache();
				$this->session->set_flashdata("succes","Mot de passe modifié.");
				redirect("mon-compte");
			} else {
				$data["echec"] = "Mot de passe incorrect.";
				$this->template->render('membre/modifier_mdp',$data);
			}
		//si formulaire incomplet ou non envoyé	
		}else{
			$this->template->render('membre/modifier_mdp',$data);
		}
	}

	// Modifie les données de l'utilisateur
	public function modifier_image(){
		$this->load->model("upload_model");
		$data["utilisateur"] = $this->profil_model->recuperer_mes_donnees();
		$data["t_sub"]   	 = "compte";

		//upload de l'image

		$image = $this->upload_model->upload_image_profil("image", $data["utilisateur"]->idutilisateur, false);

		// Si une image à été envoyée, une array est retournée (même en cas d'echec)
		if(is_array($image)) {
			// Si echec dans l'nevoi
			if(!empty($image["echec"])){
				$data = array('echec' => $image["echec"]);
				$this->template->render("membre/modifier_image",$data);
			// si tout se passe bien
			} else {
				$this->profil_model->maj_cache();
				$this->session->set_flashdata("succes","Image modifiée.");
				redirect("mon-compte");
			}
		//si aucune image à été envoyée
		} else {
			$this->template->render('membre/modifier_image',$data);
		}
	}
}