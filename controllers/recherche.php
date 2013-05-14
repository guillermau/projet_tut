<?php
/*	controleur recherche:

-recherche projet
-recherche utilisateur
-recherche documents
*/
	
class Recherche extends CI_Controller {

public function __construct(){
	parent::__construct();
	if(! $this->profil_model->verifier_connexion()){
		redirect("session/connexion");
	}
	$this->load->model("recherche_model");
}

public function recherche (){
	
	$this->load->library('form_validation');
					
	$this->form_validation->set_rules('categorie', 'CategorieRecherche', 'required');
	
	if ($this->form_validation->run() == TRUE){	
		
		if(($this->input->post("input",TRUE))== "projet"){
		
			$data = $this->recherche_model->recherche_projet($this->input->post("nom",TRUE),
															 $this->input->post("status",TRUE),
															 $this->input->post("date_debut",TRUE),
															 $this->input->post("date_fin",TRUE),
															 $this->input->post("tag",TRUE),
															 $this->input->post("tri",TRUE), 
															 $this->input->post("ordre",TRUE), 
															 $limit = 20, $offset = 0);
			$this->template->render('recherche/resultats',$data);
	
		}elseif(($this->input->post("input",TRUE))== "utilisateur"){
			$data = $this->recherche_model->recherche_utilisateur($this->input->post("nom",TRUE),
																  $this->input->post("status",TRUE),
																  $this->input->post("prenom",TRUE),
																  $this->input->post("date_debut",TRUE),
																  $this->input->post("date_fin",TRUE),
																  $this->input->post("tri",TRUE),
																  $this->input->post("ordre",TRUE),
																  $limit = 20, $offset = 0);
			$this->template->render('recherche/resultats',$data);
		}else{
			$this->load->model("utilisateur_model");
			
			$data = $this->recherche_model->recherche_documents($this->input->post("nom",TRUE),
																$this->input->post("propriétaire",TRUE),
																$this->input->post("date_debut",TRUE),
																$this->input->post("date_fin",TRUE),
																$this->input->post("type",TRUE),
																$this->input->post("date_debut_prise",TRUE),
																$this->input->post("date_fin_prise",TRUE),
																$this->input->post("tag",TRUE),
																$this->input->post("tri",TRUE),
																$this->input->post("ordre",TRUE),
																$limit = 20, $offset = 0);
																
			$proj = $this->utilisateur_model->projets_utilisateur($this->session->userdata("idutilisateur"));
			foreach($data as $doc){
				foreach($proj as $pro){
					if ($doc->idprojet == $pro)
						$docs = $docs + $doc; 
				}
			}
		}
	}else{
		$this->session->set_flashdata('erreur', 'formulaire incomplet');
		redirect("recherche/accueil");
	}
}

	public function autocomplete_utilisateur(){
		
		if (empty($_GET['term'])) exit ;
		$q = strtolower($_GET["term"]);
		if (get_magic_quotes_gpc()) $q = stripslashes($q);
		$users = array();
		$users = $this->recherche_model->recherche_utilisateur($q, "tous", "", "", "alphabetique", "ASC", 10);
		$result = array();
		if(!empty($users)){
			foreach ($users as $utl) {
				array_push($result, array("id"=>$utl->idutilisateur, "label"=>$utl->nom." ".$utl->prenom, "value" => strip_tags($utl->nom." ".$utl->prenom)));
			}	
		}
		
		echo json_encode($result);
	}

	// Recherche tags autocomplétion
	public function autocomplete_tags(){
		$this->load->model("tag_model");

		if (empty($_GET['term'])) exit ;
		$q = strtolower($_GET["term"]);
		if (get_magic_quotes_gpc()) $q = stripslashes($q);
		$tags = array();
		$tags = $this->tag_model->rechercher_autocpl($q);
		$result = array();
		if(!empty($tags)){
			foreach ($tags as $tag) {
				array_push($result, array("value" => $tag->tag));
			}	
		}
		
		echo json_encode($result);
	}


	public function generique_autocomplete(){
		
		if (empty($_GET['term'])) exit ;
		$q = strtolower($_GET["term"]);
		if (get_magic_quotes_gpc()) $q = stripslashes($q);
		$users = array();
		$projets = array();
		$documents = array();

		$users = $this->recherche_model->recherche_utilisateur($q, "tous", "", "", "alphabetique", "ASC", 5);
		$projets = $this->recherche_model->recherche_projet($q, "tous", "", "", "", "alphabetique", "ASC", 5);
		$documents = $this->recherche_model->recherche_documents($q, "", "", "", "", "", "", "", "alphabetique", "ASC", 5);
		$result = array();
		foreach($users as $u){
			array_push($result, array("category"=>"utilisateur", "label"=>$u->nom." ".$u->prenom, "value" => site_url("utilisateur/apercu/".$u->idutilisateur)));
		}
		foreach($projets as $p){
			array_push($result, array("category"=>"projet", "label"=>$p->nom, "value" => site_url("projet/apercu/".$p->idprojet)));
		}
		$this->load->model('utilisateur_model');
		foreach($documents as $d){
			$droits = $this->utilisateur_model->verifier_droit($d->iddocument,$this->session->userdata("idutilisateur"));
			if(!empty($droits) && $droits->lecture == true){
				array_push($result, array("category"=>"documents", "label"=>$d->nom_original, "value" => site_url("document/apercu/".$d->iddocument)));
			}
		}

		
		echo json_encode($result);
	}

	public function generique(){
		
		$this->load->library('form_validation');
			
			$this->form_validation->set_rules('requete', 'Recherche', 'required');
			
			//si formulaire rempli et bon
			if ($this->form_validation->run() == TRUE)
			{

				$q = strtolower($this->input->post("requete", TRUE));

				// Creation des tableaux
				$data = array();
				$data['documents']  = array();
				$data['requete']   = $this->input->post("requete", TRUE);

				// Recherche
				$data['users']     = $this->recherche_model->recherche_utilisateur($q, "tous", "", "", "alphabetique", "ASC");
				$data['projets']   = array_merge($this->recherche_model->recherche_projet($q, "tous", "", "", "", "alphabetique", "ASC"),
										$this->recherche_model->recherche_projet("", "tous", "", "", $q, "alphabetique", "ASC"));
				$documents  	   = array_merge($this->recherche_model->recherche_documents($q, "", "", "", "", "", "", "", "alphabetique", "ASC"),
										$this->recherche_model->recherche_documents("", "", "", "", "", "", "", $q, "alphabetique", "ASC"));

				// Application des droits sur les documents
				if(!empty($documents)){
					$this->load->model('type_model');
					$this->load->model('utilisateur_model');
					$data['type'] = $this->type_model->liste_types();
					foreach($documents as $d){
						$droits = $this->utilisateur_model->verifier_droit($d->iddocument,$this->session->userdata("idutilisateur"));
						if(!empty($droits) && $droits->lecture == true){
							array_push($data["documents"], $d);
						}
					}
				}

				$this->template->render("recherche/generique",$data);
			} else {
				$this->template->render("recherche/generique");
			}
	}

	// Formulaire de recherche avance
	public function avance() {
		// Models suppléentaitres
		$this->load->model("type_model");

		// Validation formulaire
		$this->load->library('form_validation');				
		$this->form_validation->set_rules('categorie', 'Catégorie', 'required');
		
		// Si le formulaire est envoyé et est correct
		if ($this->form_validation->run() == TRUE) {

			// Si on cherche un projet
			if ($this->input->post("categorie",TRUE) == "projet") {

				$data["projets"] = $this->recherche_model->recherche_projet($this->input->post("nom",TRUE),
																 $this->input->post("status",TRUE),
																 $this->input->post("date_debut",TRUE),
																 $this->input->post("date_fin",TRUE),
																 $this->input->post("tag",TRUE),
																 $this->input->post("tri",TRUE), 
																 $this->input->post("ordre",TRUE));
				$this->template->render('recherche/generique',$data);
			// Si on recherche un utilisateur
			} else if ($this->input->post("categorie",TRUE) == "utilisateur") {

				$data["users"] = $this->recherche_model->recherche_utilisateur($this->input->post("nom",TRUE),
																	  $this->input->post("status",TRUE),
																	  $this->input->post("date_debut",TRUE),
																	  $this->input->post("date_fin",TRUE),
																	  $this->input->post("tri",TRUE),
																	  $this->input->post("ordre",TRUE));
				$this->template->render('recherche/generique',$data);
			}

			// Si on cherche un document
			else {
				$this->load->model("utilisateur_model");
				
				$data["documents"] = $this->recherche_model->recherche_documents($this->input->post("nom",TRUE),
																	$this->input->post("propriétaire",TRUE),
																	$this->input->post("date_debut",TRUE),
																	$this->input->post("date_fin",TRUE),
																	$this->input->post("type",TRUE),
																	$this->input->post("date_debut_prise",TRUE),
																	$this->input->post("date_fin_prise",TRUE),
																	$this->input->post("tag",TRUE),
																	$this->input->post("tri",TRUE),
																	$this->input->post("ordre",TRUE));
				
				$data['type'] = $this->type_model->liste_types();						
				$proj = $this->utilisateur_model->projets_utilisateur($this->session->userdata("idutilisateur"));
				foreach($data["documents"] as $doc){
					foreach($proj as $pro){
						if ($doc->idprojet == $pro)
							$docs = $docs + $doc; 
					}
				}

				$this->template->render('recherche/generique',$data);
			}

		}

		// Sinon
		else {
			$this->template->render("recherche/formulaire");
		}
	}
}