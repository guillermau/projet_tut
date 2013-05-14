<?php 
if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Utilisateur extends CI_Controller {
	
	public function __construct()
	{
		parent::__construct();
	}

	// Affiche un aperçu d'un utilisateur
	public function apercu($idutilisateur){
		if($idutilisateur == $this->session->userdata("idutilisateur")) {
			redirect("mon-compte");
		}
		$this->load->model("utilisateur_model");
		$data['utilisateur'] = $this->utilisateur_model->recuperer_utilisateur($idutilisateur);
		$data['projets'] = $this->utilisateur_model->projets_utilisateur($idutilisateur);

		$mesprojets = $this->profil_model->recuperer_mes_projets();

		if(!empty($mesprojets)) {
			$data['mesprojets'] = array();
			foreach($mesprojets as $mp) {
				$data['mesprojets'][] = $mp->idprojet;
			}
		}

		$this->template->render("utilisateur/apercu",$data);
	}

}

?>