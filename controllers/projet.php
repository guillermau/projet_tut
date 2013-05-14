<?php
/*controleur lié au projet
-créer projet
-accueil projet (statistique du projet)
-documents du projet
-afficher membres du projet (par groupe)
-créer groupe
-gestion groupe
-ajouter membre
-gestion projet

*/
class Projet extends CI_Controller {

	public function __construct(){
		parent::__construct();
		$this->session->set_flashdata("referer",current_url());
		if(! $this->profil_model->verifier_connexion()) {
			$this->session->set_flashdata("referer",current_url());
			redirect("session/connexion");
		}
		$this->load->model("projet_model");
		$this->load->model("utilisateur_model");
	}

	//création d'un projet par l'utilisateur
	 public function creer() {
	    $this->load->model("upload_model");
		
		$utilisateur = $this->profil_model->recuperer_mes_donnees(); //utilisateur récupéré des cookies de la session
		
		//l'utilisateur ne peut créer un nouveau projet que s'il n'est pas invité
		if( $utilisateur->invite == false){
			//vérification formulaire

			$this->load->library('form_validation');
			
			$this->form_validation->set_rules('nom', 'Nom du Projet', 'required|max_length[256]');
			
			//si formulaire rempli et bon
			if ($this->form_validation->run() == TRUE)
			{
				//création du projet
				$id = $this->projet_model->creer_projet($this->input->post("nom",TRUE), 
														$this->input->post("description",TRUE), 
														$utilisateur->idutilisateur,
														$this->input->post("tags",TRUE));
				if(empty($id)){
					$data = array('erreur' => 'Erreur dans la création du projet.');
					$this->template->render("projet/creer",$data);
				}
				//upload de l'image du projet
				$image = $this->upload_model->upload_image_profil("image", $id, true);

				if(is_array($image)) {
					if(!empty($image["echec"])){
						$data = array('erreur' => $image["echec"]);
						$this->template->render("projet/creer",$data);
						return false;
					}
				}
				//si tout a bien marché, renvoie a l'accueil du formularie
				redirect("mes-projets");
			}else{
				//si formulaire n'est pas bon ou pas encore rempli
				$this->template->render("projet/creer");
			} 	
		}else{
			//si j'ai pas les droits pour être ici
			$this->session->set_flashdata('erreur', 'Droits insufisants pour créer un projet.');
			redirect("mes-projets");
		}
	}

	//page d'accueil du projet
	public function accueil($idprojet){
		$data["utilisateur"] = $this->profil_model->recuperer_mes_donnees();
		$data["projet"]      = $this->projet_model->recuperer_projet($idprojet);//récupération des infos du projet
		$data["nbdocs"]      = $this->projet_model->nombre_documents_prj($idprojet);//récupération du nombre de documents du projet
		$data["nbmembres"]   = $this->projet_model->nombre_membres_prj($idprojet);//récupération du nombre de membres du projet
		$data["nbinvites"]   = $this->projet_model->nombre_invites_prj($idprojet);//récupération du nombre de invités du projet
		$data["isadmin"]     = $this->utilisateur_model->verifier_admin_projet($idprojet,$data["utilisateur"]->idutilisateur);//si le utilisateur est admin du projet
		$data["groupes"]     = $this->projet_model->lister_groupes($idprojet);
		if(empty($data["projet"])){
			redirect("mes-projets");
		}
		$this->template->render('projet/accueil',$data);//appel au view accueil du projet

	}

	//page d'aperçu du projet pour un non membre
	public function apercu($idprojet){
		$data["utilisateur"] = $this->profil_model->recuperer_mes_donnees();
		if($this->utilisateur_model->verifier_acces_projet($idprojet,$data['utilisateur']->idutilisateur)){
			redirect("projet/accueil/".$idprojet);
		}
		$data["projet"]      = $this->projet_model->recuperer_projet($idprojet);//récupération des infos du projet
		$data["nbdocs"]      = $this->projet_model->nombre_documents_prj($idprojet);//récupération du nombre de documents du projet
		$data["nbmembres"]   = $this->projet_model->nombre_membres_prj($idprojet);//récupération du nombre de membres du projet
		$data["nbinvites"]   = $this->projet_model->nombre_invites_prj($idprojet);//récupération du nombre de invités du projet

		$this->template->render('projet/apercu',$data);//appel au view aperçu du projet

	}

	//page document du projet // Guillaume (enleve le dossier parent)
	public function documents($idprojet, $idrep = null){
		$this->load->model("document_model");
		$this->load->model("repertoire_model");
		$data["utilisateur"] = $this->profil_model->recuperer_mes_donnees();

		//vérifie si l'utilisateur à le droit d'acceder au projet
		if(($grp = $this->utilisateur_model->verifier_acces_projet($idprojet, $data["utilisateur"]->idutilisateur)) == false) {
			$this->session->set_flashdata('erreur', 'Vous n\'&ecirc;tes pas membre de ce projet.');
			redirect("mes-projets");
		}
		
		$data["droits"]     = $this->projet_model->verifier_droit_groupe($grp);
		$data["isadmin"]    = $this->utilisateur_model->verifier_admin_projet($idprojet,$data["utilisateur"]->idutilisateur);
		$data["projet"]     = $this->projet_model->recuperer_projet($idprojet);
		$data["documents"]  = $this->document_model->documents_projet_et_droits($idprojet, $idrep, $data["utilisateur"]->idutilisateur);
		$data["membres"]    = $this->projet_model->lister_membres($idprojet);
		$data["repertoires"] = array();
		/*if(!is_null($idrep)) {
			$rep          	   = new stdClass();
			$rep->nom 		   = "Parent";
			$rep->idprojet	   = $idprojet;
			$rep->idrepertoire = $this->repertoire_model->pere_repertoire($idprojet, $idrep);
			array_push($data["repertoires"], $rep);
		}*/
		$data["repertoires"] = array_merge($data["repertoires"], $this->repertoire_model->lister_repertoires($idprojet, $idrep));
		$data["idprojet"] = $idprojet;
		$data["idrep"]    = $idrep;

		// Fonction supprimer ?
		$data["emptyrep"] = $this->verifie_repertoire_vide($idrep);

		$this->template->render('projet/documents',$data);
	}

	//affichage membres du projet
	public function membres($idprojet){
		$data["utilisateur"] = $this->profil_model->recuperer_mes_donnees();
		$data["projet"]      = $this->projet_model->recuperer_projet($idprojet);
		$data["isadmin"]     = $this->utilisateur_model->verifier_admin_projet($idprojet,$data["utilisateur"]->idutilisateur);
		$data["groupes"]     = $this->projet_model->lister_groupes($idprojet);
		$data["membres"]     = $this->projet_model->lister_membres_par_groupe($idprojet);
		$this->template->render('projet/membres',$data);
	}

	//gestion projet (+ modifier projet)
	public function gestion($idprojet){
		//vérifie si l'utilisateur est dans le groupe admin du projet
		$data["utilisateur"] = $this->profil_model->recuperer_mes_donnees();
		if(! $this->utilisateur_model->verifier_admin_projet($idprojet,$data["utilisateur"]->idutilisateur)) {
			$this->session->set_flashdata('erreur', 'Vous n\'&ecirc;tes pas administrateur de ce projet.');
			redirect("projet/accueil".$idprojet);
		}
		
		$data["projet"]  = $this->projet_model->recuperer_projet($idprojet);
		$data["groupes"] = $this->projet_model->lister_groupes_droits($idprojet);
		$data["membres"] = $this->projet_model->lister_membres_par_groupe($idprojet);

		//vérification du formulaire
		$this->load->library('form_validation');
						
		$this->form_validation->set_rules('nom', 'Nom du groupe', 'required|max_length[256]');
		$this->form_validation->set_rules('description', 'Droit de lecture', 'required');

		//Si une requete ajax
		if(is_ajax()){
			//si formulaire envoyé et bon
			if ($this->form_validation->run() == TRUE){	
				//print_r($idprojet);
				$this->projet_model->modifier_projet($idprojet, $this->input->post("nom",TRUE), $this->input->post("description",TRUE), $this->input->post("tags",TRUE));
				echo "succes";
			//si formulaire incomplet ou non envoyé	
			}else{
				 echo validation_errors();
			}	
		} else {
			//si formulaire envoyé et bon
			if ($this->form_validation->run() == TRUE){	
				$this->projet_model->modifier_projet($idprojet, $this->input->post("nom",TRUE), $this->input->post("description",TRUE), $this->input->post("tags",TRUE));
				redirect("projet/gestion/".$idprojet);
			//si formulaire incomplet ou non envoyé	
			}else{
				$this->template->render('projet/gestion',$data);
			}
		}	
	}

	// Modifie l'image du projet
	public function modifier_image($idprojet){
		$this->load->model("upload_model");
		$data["projet"] = $this->projet_model->recuperer_projet($idprojet);

		//upload de l'image

		$image = $this->upload_model->upload_image_profil("image", $idprojet, true);

		// Si une image à été envoyée, une array est retournée (même en cas d'echec)
		if(is_array($image)) {
			// Si echec dans l'nevoi
			if(!empty($image["echec"])){
				$data = array('echec' => $image["echec"]);
				$this->template->render("projet/modifier_image",$data);
			// si tout se passe bien
			} else {
				redirect("projet/accueil/".$idprojet);
			}
		//si aucune image à été envoyée
		} else {
			$this->template->render('projet/modifier_image',$data);
		}
	}

	// Supprimer projet
	public function supprimer($idprojet){
		if($this->projet_model->supprimer_projet($idprojet)){
			redirect("mes-projets");
		} else {
			$this->session->set_flashdata('echec','Erreur dans la suppression');
			redirect("projet/gestion/".$idprojet);
		}
	}

	//création d'un groupe dans le projet
	public function creer_groupe($idprojet){
		//vérifie si l'utilisateur est dans le groupe admin du projet
		$data["utilisateur"] = $this->profil_model->recuperer_mes_donnees();
		if(! $this->utilisateur_model->verifier_admin_projet($idprojet,$data["utilisateur"]->idutilisateur)) {
			$this->session->set_flashdata('erreur', 'Vous n\'&ecirc;tes pas administrateur de ce projet.');
			redirect("projet/accueil".$idprojet);
		}

		//vérification du formulaire
		$this->load->library('form_validation');
						
		$this->form_validation->set_rules('type', 'Nom du groupe', 'required|max_length[45]');
		
		/*$this->form_validation->set_rules('lecture', 'Droit de lecture', 'required');
		$this->form_validation->set_rules('ecriture', 'Droit d\'écriture', 'required');
		$this->form_validation->set_rules('upload', 'Droit d\'upload', 'required');*/


		//si formulaire envoyé et bon
		if ($this->form_validation->run() == TRUE){	
			if(!($droits = $this->input->post("droits")) || ! is_array($droits) ){
				$lecture  = "0";
				$ecriture = "0";
				$upload   = "0";
			} else {
				$lecture  = in_array("lecture", $droits)  ? 1 : 0;
				$ecriture = in_array("ecriture", $droits) ? 1 : 0;
				$upload   = in_array("upload", $droits)   ? 1 : 0;
			}
			$idgroupe = $this->projet_model->creer_groupe($idprojet,
											  $this->input->post("type",TRUE),
											  $lecture,
											  $ecriture,
											  $upload);
			//redirect("projet/gestion/".$idprojet);
			if(is_numeric($idgroupe)) {
				echo "succes:".$idgroupe.":";
			}
		//si formulaire incomplet ou non envoyé	
		}else{
			echo validation_errors();
		}
	}

	//modification des droits d'un groupe
	public function modifier_groupe($idprojet){
		//vérifie si l'utilisateur est dans le groupe admin du projet
		$data["utilisateur"] = $this->profil_model->recuperer_mes_donnees();
		if(! $this->utilisateur_model->verifier_admin_projet($idprojet,$data["utilisateur"]->idutilisateur)) {
			$this->session->set_flashdata('erreur', 'Vous n\'&ecirc;tes pas administrateur de ce projet.');
			redirect("projet/accueil".$idprojet);
		}

		//vérification du formulaire
		$this->load->library('form_validation');
						
		$this->form_validation->set_rules('idgroupe', 'Identifiant du Groupe', 'required|callback_verifie_groupe['.$idprojet.']');


		//si formulaire envoyé et bon
		if ($this->form_validation->run() == TRUE){	
			if(!($droits = $this->input->post("droits")) || ! is_array($droits) ){
				$lecture  = "0";
				$ecriture = "0";
				$upload   = "0";
			} else {
				$lecture  = in_array("lecture", $droits)  ? 1 : 0;
				$ecriture = in_array("ecriture", $droits) ? 1 : 0;
				$upload   = in_array("upload", $droits)   ? 1 : 0;
			}
			$this->projet_model->modifier_droits_gr($this->input->post("idgroupe",TRUE),
													 $lecture,
													 $ecriture,
													 $upload);
			echo "succes";
		//si formulaire non envoyé ou mal rempli
		}else{
			echo validation_errors();
		}
	}

	//supprimer un groupe (AJAX)
	public function supprimer_groupe($idprojet, $idgroupe){
		//vérifie si l'utilisateur est dans le groupe admin du projet
		$data["utilisateur"] = $this->profil_model->recuperer_mes_donnees();
		if(! $this->utilisateur_model->verifier_admin_projet($idprojet,$data["utilisateur"]->idutilisateur)) {
			echo "Droits insufisants";
			return false;
		}
		if($this->projet_model->supprimer_groupe($idgroupe)) {
			echo "succes";
		} else {
			echo "Erreur dans la suppression.";
		}
	}

	//ajouter un membre dans un groupe
	public function ajouter_membre($idprojet){
		//vérifie si l'utilisateur est dans le groupe admin du projet
		$data["utilisateur"] = $this->profil_model->recuperer_mes_donnees();
		if(! $this->utilisateur_model->verifier_admin_projet($idprojet,$data["utilisateur"]->idutilisateur)) {
			$this->session->set_flashdata('erreur', 'Vous n\'&ecirc;tes pas administrateur de ce projet.');
			redirect("projet/accueil".$idprojet);
		}

		$this->load->library('form_validation');
								
		$this->form_validation->set_rules('groupe', 'Groupe', 'required|callback_verifie_groupe['.$idprojet.']');
		$this->form_validation->set_rules('utilisateurs', 'Liste de utilisateurs', 'required');

		//si formulaire envoyé et bon
		if ($this->form_validation->run() == TRUE){	
			$utilisateurs = $this->utilisateur_model->recuperer_id_par_noms($this->input->post("utilisateurs"));
			if(empty($utilisateurs)){
				redirect("projet/membres/".$idprojet);
			}
			foreach($utilisateurs as $u){
				$this->projet_model->ajouter_membre($u->idutilisateur, $idprojet,$this->input->post("groupe",TRUE));
			}
		}
		redirect("projet/membres/".$idprojet);
	}

	//supprimer un membre d'un projet (AJAX)
	public function supprimer_membre($idprojet, $idutilisateur){
		//vérifie si l'utilisateur est dans le groupe admin du projet
		$data["utilisateur"] = $this->profil_model->recuperer_mes_donnees();
		if(! $this->utilisateur_model->verifier_admin_projet($idprojet,$data["utilisateur"]->idutilisateur)) {
			echo "Vous n'étes pas admin de ce projet.";
			return false;
		}
		if($this->projet_model->supprimer_membre($idutilisateur, $idprojet)) {
			echo "succes";
		} else {
			echo "Erreur dans la suppression.";
		}
	}

	//changer un membre de groupe
	public function modifier_membre ($idprojet){

		//vérification formulaire
		$this->load->helper(array('form', 'url'));
		$this->load->library('form_validation');

		$this->form_validation->set_rules('idutilisateur', 'Membre a deplacer', 'required');
		$this->form_validation->set_rules('idancgroupe', 'Ancien groupe', 'required');
		$this->form_validation->set_rules('idnouvgroupe', 'Nouveau groupe', 'required');

		//si formulaire envoyé et bon
		if ($this->form_validation->run() == TRUE){	
			$this->project_model->modifier_groupe_mbr($this->input->post("idutilisateur",TRUE),
													  $idprojet, 
													  $this->input->post("idutilisateur",TRUE), 
													  $this->input->post("idnouvgroupe",TRUE));
			redirect("projet/gestion/".$idprojet);
		//si formulaire non envoyé ou mal rempli
		}else{
			$data = array("idprojet" => $idprojet);
			$this->template->render('projet/modifiermembre',$data);
		}
	}


	//Fonctions de callback de vérification de formulaire

	public function verifie_groupe($idgroupe, $idprojet)
	{
		//vérifie si le groupe existe et appartient au projet
		if($this->projet_model->groupe_appartient($idgroupe,$idprojet)){
			return TRUE;
		} else {
			$this->form_validation->set_message('verifie_groupe', 'Le groupe n\'existe pas ou n\'appartient pas au projet');
			return false;
		}
	}

	/////////////////////////
	// GESTION REPERTOIRES //
	/////////////////////////

	// Nouveau répertoire
	public function creer_repertoire() {

		// On définit les règle du formulaire
		$this->load->library('form_validation');
		
		$this->form_validation->set_rules('idprojet', 'projet', 'required');
		$this->form_validation->set_rules('nom', 'nom du répertoire', 'required');

		// Si le formulaire a été correctement envoyé
		if ($this->form_validation->run() == TRUE) {	

			// On récupère les variables du formulaire
			$idprojet = $this->input->post("idprojet", TRUE);
			$idpere   = $this->input->post("idpere", TRUE);
			$nom 	  = $this->input->post("nom", TRUE);

			// Etape 1 : Membre du projet ?
			$utilisateur = $this->profil_model->recuperer_mes_donnees();
			$grpUtil = $this->utilisateur_model->verifier_acces_projet($idprojet, $utilisateur->idutilisateur);

			if($grpUtil == false) {
				$this->session->set_flashdata('erreur', 'Vous n\'&ecirc;tes pas membre de ce projet');
				redirect("projet/accueil".$idprojet);
			}

			// Etape 2 : Droits du groupe ?
			$droitsgrp = $this->projet_model->verifier_droit_groupe($grpUtil);

			// Si les droits sont accordés
			if($droitsgrp->upload == TRUE) {
				$this->load->model("repertoire_model");

				$idrep = $this->repertoire_model->creer_repertoire($idprojet, $nom, $idpere);
				echo "succes";
			} else {
				echo "Vous n'avez pas les des droits requis";
			}

		// Sinon on retourne les erreurs
		} else {
			echo validation_errors();
		}
	}

	// Supprimer repertoires
	public function supprimer_repertoire($idrepertoire) {

		// Vérifie que le nom du répertoire n'est pas null
		if(is_null($idrepertoire)) {
			$this->session->set_flashdata('erreur', 'Il n\'est pas possible de supprimer la racine d\'un projet.');
			redirect("mes-projets");
		}

		// On récupère les informations de répertoire
		$this->load->model("repertoire_model");
		$repData = $this->repertoire_model->infos_repertoire($idrepertoire);

		// Vérifie que répertoire vide
		if($this->verifie_repertoire_vide($idrepertoire) == false) {
			$this->session->set_flashdata('erreur', 'Le r&eacute;pertoire n\'est pas vide.');
			redirect("projet/documents/".$repData->idprojet."/".$idrepertoire);
		}

		// Si tout est OK
		else {	
			// Etape 1 : Membre du projet ?
			$utilisateur = $this->profil_model->recuperer_mes_donnees();
			$grpUtil     = $this->utilisateur_model->verifier_acces_projet($repData->idprojet, $utilisateur->idutilisateur);

			if($grpUtil == false) {
				$this->session->set_flashdata('erreur', 'Vous n\'&ecirc;tes pas membre de ce projet');
				redirect("projet/accueil".$repData->idprojet);

			// Etape 2 : Droits du groupe ?
			} else {
				$droitsgrp = $this->projet_model->verifier_droit_groupe($grpUtil);

				// Si les droits sont accordés
				if($droitsgrp->upload == TRUE) {
					$this->load->model("repertoire_model");

					$this->repertoire_model->supprimer_repertoire($idrepertoire);

					$this->session->set_flashdata('succes', 'Dossier supprim&eacute;.');
					redirect("projet/documents/".$repData->idprojet."/".$repData->pere);
				} else {
					$this->session->set_flashdata('erreur', 'Vous n\'avez pas les droits requis.');
					redirect("projet/documents/".$repData->idprojet);
				}
			}
		}
	}

	// Verification repertoire vide
	public function verifie_repertoire_vide($idrepertoire) {

		if (is_null($idrepertoire))
			return false;

		$this->load->model("repertoire_model");
		return $this->repertoire_model->verifier_repertoire_vide($idrepertoire);

	}
	
	//Nouvelle fonction action // Guillaume
	public function action() {
		$this->load->model("repertoire_model");
		$this->load->model("document_model");
		
		//$repData = $this->repertoire_model->infos_repertoire($racine); // On recupere les donnees de la racine
		
		$ordre = $this->input->post("ordre", TRUE); // On recupere l'ordre
		$racine = $this->input->post("idrep_courant", TRUE);
		$idprojet = $this->input->post("idprojet", TRUE);
		
		//On recupere les donnes du post
		//On cherche a identifier les checkbox validé
		
		// On fait le tri entre repertoire et document
		
		
		if ($_POST['ordre'] == "Telecharger") // On telecharge
		{
		// telecharger_zip ($iddoc,$idracine) pour les documents
		// telecharger_rep ($idrep,$idracine) pour les repertoires
		;}
		else //Ici on supprime
		{
		// supprimer_document($iddoc) pour les documents
		// supprimer_repertoire_plein($idrepertoire)
		;}
		if(is_null($racine))
		redirect("projet/documents/".$idprojet."/".$racine);
		else
		redirect("projet/documents/".$idprojet."/".$racine);
		
	}
}