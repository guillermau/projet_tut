<?php
/*controleur aide
*/
class Aide extends CI_Controller {

	public function __construct(){
		parent::__construct();
	if(! $this->profil_model->verifier_connexion()) {
		redirect("session/connexion");
		}
	}
	
	public function index(){
	
	$this->load->model('aide_model');
	$data['aide']  = $this->aide_model->afficher_aide();
	$data["t_sub"]   = "aide";
	
	$this->template->render('aide/aide',$data);
	}

}