<?php 

class Aide_model extends CI_Model {

	function __construct(){
	parent::__construct();
	}
	
	public function afficher_aide() {
	
	$recherche = '';
	
	return $this->db->select('*')
					->from('aide')
					->order_by ('categorie')
					->like('contenu',$recherche)
					->get()
					->result();

	//vérification du formulaire	
	$this->load->library('form_validation');
	$this->form_validation->set_rules('description', 'Description', 'required');
	
		//si formulaire envoyé et bon
		if ($this->form_validation->run() == TRUE){	
			$this->aide_model->afficher_aide($iddoc, $this->input->post("description",TRUE), $this->input->post("recherche",TRUE));
			redirect("aide/aide");
		//si formulaire incomplet ou non envoyé	
		}else{
			$this->template->render('aide/aide', $data);
		}
		

	}
	
	

}


?>