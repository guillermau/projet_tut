<?php
/*	controleur accueil mode connecté
	-affichage des projets du membre
	-affichage des documents du membre
	-mon compte
*/
	
class Accueil extends CI_Controller {

public function __construct(){
	parent::__construct();
	if(! $this->profil_model->verifier_connexion())
		redirect("session/connexion");
}

//affichage des projets de l'utilisateur connecté
public function projets(){
	$this->load->model->("profil_model");
	$data = this->profil_model->recuperer_mes_projets();
	$this->template->render('Accueil/mesprojets',$data);

}

public function documents(){
	$this->load->model->("profil_model");

	
	$docs = $this->profil_model->recuperer_mes_documents();
	$this->template->render('Accueil/mesdocuments',$docs);
		
}

public function profil(){
	$this->load->model->("profil_model");
	$infos = $this->profil_model->recuperer_mes_donnees();
	$this->template->render('Accueil/monprofil',$docs);

}