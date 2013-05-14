<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Erreur extends CI_Controller {

	public function __construct(){
		parent::__construct();
	}

	// Erreur 404
	public function not_found()
	{
		$this->template->render("erreur/404");
	}
}