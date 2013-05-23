<?php
/*-aperçu (nom,date....)
-upload
-versioning
-suppression
-modifier infos (metatags, commentaires...)
-modifier droits
*/
class Document extends CI_Controller {

private $donnees;

public function __construct(){
	parent::__construct();
	if(! $this->profil_model->verifier_connexion()) {
		if(is_ajax) {
			echo "echec: utilisateur non connecté";
		} else {
			redirect("session/connexion");
		}
	}
	$this->load->model("document_model");
	$this->load->model("projet_model");

	//récuperer les données de l'utilisateur
	$this->donnees = $this->profil_model->recuperer_mes_donnees();
}

//aperçu des infos du document
public function apercu($iddoc){
	$this->load->model('utilisateur_model');
	$this->load->model('type_model');
	$this->load->model('tag_model');

	//vérifie les droits de l'utilisateur
	$droits = $this->utilisateur_model->verifier_droit ($iddoc,$this->donnees->idutilisateur);

	if(!empty($droits) && $droits->lecture == TRUE){
		$data['doc'] = $this->document_model->recuperer_document($iddoc);
		$data['proprietaire'] = $this->utilisateur_model->recuperer_utilisateur($data['doc']->idutilisateur);
		$data['projet'] = $this->projet_model->recuperer_projet($data['doc']->idprojet);
		$data['type'] = $this->type_model->recuperer_type($data['doc']->type);

		$data['tags'] = $this->tag_model->recuperer_tags_document($data['doc']->iddocument);

		if(is_ajax()){
			$this->load->view('document/apercu',$data);
		} else {
			$this->template->render("document/apercu_doc",$data);
		}
		
	} else {
		if(is_ajax()){
			echo "Vous n'avez pas les droits pour lire ce document.";
		} else {
			redirect("mes-documents");
		}
	}
}

//uploader un document
public function upload($idprojet){
	$this->load->model("upload_model");
	$this->load->model("utilisateur_model");
        
	
	//vérifier si utilisateur est membre du projet
	$grpUtil = $this->utilisateur_model->verifier_acces_projet($idprojet, $this->donnees->idutilisateur);
	if($grpUtil == false){
		$this->session->set_flashdata('erreur', 'non membre');
		//redirect("mes-projets");
		echo "Vous n'êtes pas membre de ce projet";
	//s'il est membre vérifier formulaire	
	}else{
		$this->load->library('form_validation');
		
		$this->form_validation->set_rules('utilisateur', 'Utilisateur', 'required');
		$this->form_validation->set_rules('projet', 'Projet', 'required');

		//si formulaire bon, vérifer les droits du groupe de l'utilisateur
		if ($this->form_validation->run() == TRUE){	
			$droitsgrp = $this->projet_model->verifier_droit_groupe($grpUtil);
			if($droitsgrp->upload == TRUE){
				if(is_ajax()){
					// Correction repertoire
					$idrep = $this->input->post("repertoire",TRUE);

					if(empty($idrep))
						$idrep = 0;

					$doc = $this->upload_model->upload($this->input->post("fichier_ajax",TRUE),
												   $this->input->post("projet",TRUE),
												   $this->donnees->idutilisateur,
												   $idrep, FALSE);
					if(count($doc["echec"])){
						print_r($doc["echec"]);
					} else {
						echo "succes:".$doc["id"].":";
					}
				}
			}else{
				echo "Vous n'avez pas les droits pour envoyer des documents.";
			}
		//si formulaire invalide -> erreur	
		}else{
			echo "echec: ".validation_errors();
		}
	}
        
}

public function nlrc_upload($iddoc){
	$this->load->model("utilisateur_model");
	$this->load->model("type_model");

	$doc = $this->document_model->recuperer_document($iddoc);
	$proprietaire = $this->utilisateur_model->recuperer_utilisateur($doc->idutilisateur);
	$type = $this->type_model->recuperer_type($doc->type);
	echo '<li class="'.$type->type.'"><a href="'.site_url("uploads/projets/".$doc->idprojet."/".$doc->chemin_fichier).
	'" target="_blank" class="downlink"><img width="16" height="16" src="http://localhost/projtut/img/download.png"></a><a href="'.
	site_url("document/apercu/".$doc->iddocument).'">'.$doc->nom_original.'<div class="meta"><span data-meta="proprietaire">'.
	$proprietaire->nom.'</span><br><span data-meta="date-maj">'.date("d/m/Y h:i:s",strtotime($doc->maj)).
	'</span><span data-meta="date-maj">'.date("d/m/Y h:i:s",strtotime($doc->creation)).'</span></div></a></li>';
}

//mettre à jour un document (le remplacer)
public function versioning($iddoc, $input, $idprojet, $idrepertoire){
	
	$data = $this->document_model->recuperer_document($iddoc);
	
	$grpUtil = $this->utilisateur_model->verifier_acces_projet($idprojet, $this->donnees->idutilisateur);
	
	if($grpUtil == false){
		$this->session->set_flashdata('erreur', 'non membre');
		redirect("accueil");
	}else{
		$droits = $this->document_model->verifier_droit ($iddoc,$this->donnees->idutilisateur);
		if(!empty($droits) && $droits->ecriture == TRUE){
			$doc = $this->upload_model->upload($input,
											   $idprojet,
											   $idutilisateur,
											   $idrepertoire,
											   TRUE);
			echo "document remplacé";
		}elseif($droits->ecriture == FALSE){
			$this->session->set_flashdata('erreur', 'droits insuffisants');
			redirect("projet/documents");
		}
	}
}

//modifier les infos d'un document
public function modifier($upload = false){
	$this->load->helper(array('form', 'url'));
	
	$this->load->library('form_validation');
					
	$this->form_validation->set_rules('iddoc', 'Id Document', 'required');
	$this->form_validation->set_rules('idprojet', 'Id Projet', 'required');

	//si formulaire bon
	if ($this->form_validation->run() == TRUE){	
		$this->load->model("utilisateur_model");
	
		$data = $this->document_model->recuperer_document($this->input->post("iddoc",TRUE));
	
		$grpUtil = $this->utilisateur_model->verifier_acces_projet($this->input->post("idprojet",TRUE), $this->donnees->idutilisateur);
		
		if($grpUtil == false){
			echo "echec: non membre du projet";
		}else{
			$droits = $this->utilisateur_model->verifier_droit ($this->input->post("iddoc",TRUE), $this->donnees->idutilisateur);

			if($droits->ecriture == TRUE || ($upload && $droits->upload == TRUE)){
				$doc = $this->document_model->modifier_document($this->input->post("iddoc",TRUE),
																$this->input->post("description",TRUE),
																$this->input->post("tags",TRUE)) ;
				echo "succes";
			}else{
				echo "echec: Droits insufisants";
			}
		}
	}else{
		echo "echec: ".validation_errors();
	}
}

/////////////////////////
// GESTION DU DOCUMENT //
/////////////////////////

// Formulaire de gestion
public function gestion($iddoc){
	// On récupère la liste des droits
	$this->load->model("utilisateur_model");
	$this->load->model("repertoire_model");
	$this->load->model("tag_model");

	// Récupération des infos
	$data["document"]    = $this->document_model->recuperer_document($iddoc);
	$idprojet		     = $data["document"]->idprojet;
	$data["groupes"]     = $this->document_model->liste_droit_projet_document($iddoc, $idprojet);
	$data["tags"]	     = $this->tag_model->recuperer_tags_document($iddoc);
	$data["projet"]      = $this->projet_model->recuperer_projet($idprojet);
	$data["membres"]     = $this->projet_model->lister_membres_par_groupe($idprojet);
	$data["repertoires"] = $this->repertoire_model->lister_repertoires_projet($idprojet);

	$grpUtil = $this->utilisateur_model->verifier_acces_projet($idprojet, $this->donnees->idutilisateur);
	$droits = $this->utilisateur_model->verifier_droit($iddoc, $this->donnees->idutilisateur);

	// On vérifie
	if($grpUtil == false) {
		$this->session->set_flashdata('erreur', 'Vous n\'&ecirc;tes pas membre de ce projet.');
		redirect("projet/documents/".$idprojet);
	}
	if($droits->ecriture == false) { // FIX-IT : Droit proprietaire ?
		$this->session->set_flashdata('erreur', 'Vous n\'avez pas les droit d\'&eacute;criture sur ce fichier.');
		redirect("projet/documents/".$idprojet);
	}	

	//vérification du formulaire
	$this->load->library('form_validation');
	$this->form_validation->set_rules('description', 'Description', 'required');

	//Si une requete ajax
	if(is_ajax()){
		//si formulaire envoyé et bon
		if ($this->form_validation->run() == TRUE){	
			$this->document_model->modifier_document($iddoc, $this->input->post("description",TRUE), $this->input->post("tags",TRUE));
			echo "succes";
		//si formulaire incomplet ou non envoyé	
		}else{
			 echo validation_errors();
		}	
	} else {
		//si formulaire envoyé et bon
		if ($this->form_validation->run() == TRUE){	
			$this->document_model->modifier_document($iddoc, $this->input->post("description",TRUE), $this->input->post("tags",TRUE));
			redirect("document/gestion/".$idprojet."/".$iddoc);
		//si formulaire incomplet ou non envoyé	
		}else{
			$this->template->render('document/gestion', $data);
		}
	}	
}

// Suppression document
public function supprimer($iddoc){
	$this->load->model("utilisateur_model");
	$doc = $this->document_model->recuperer_document($iddoc);

	$droits = $this->utilisateur_model->verifier_droit($iddoc, $this->donnees->idutilisateur);
	
	if($droits->ecriture || $doc->idutilisateur == $this->donnees->idutilisateur) {
		$recup = $this->document_model->supprimer_document($iddoc);
		$this->session->set_flashdata('succes', 'Document supprim&eacute;.');
		redirect("projet/documents/".$doc->idprojet);
	} else {
		$this->session->set_flashdata('echec', 'Vous n\'avez pas les droits suffisants');
		redirect("projet/documents/".$doc->idprojet);
	}

}

// Deplacement document
public function deplacer($iddoc) {

	// Chargements models nécéssaires
	$this->load->model("utilisateur_model");

	// Récuprération des données
	$document    = $this->document_model->recuperer_document($iddoc);
	$droits      = $this->utilisateur_model->verifier_droit($iddoc, $this->donnees->idutilisateur);

	// Vérification des droits
	// Droits accordés
	if($droits->ecriture || $document->idutilisateur == $this->donnees->idutilisateur) {

		// Règles du formulaire
		$this->load->library("form_validation");
		$this->form_validation->set_rules('idnvdossier', 'Nouveau dossier', 'required|callback_verifie_dossier['.$document->idprojet.']');

		// Si formulaire correct
		if ($this->form_validation->run() == TRUE) {

			// Éxécution requette
			$result = $this->document_model->changer_rep($iddoc, $this->input->post("idnvdossier"));

			// Si formulaire envoyé via AJAX
			if(is_ajax())
				echo "success";
			else {
				if ($result)
					$this->session->set_flashdata('succes', 'Document d&eacute;plac&eacute;.');
				else
					$this->session->set_flashdata('error', 'Le document existe deja !');

				redirect("projet/documents/".$document->idprojet);
			}


		} else {

			// Si formulaire envoyé via AJAX
			if(is_ajax())
				echo validation_errors();
			else {
				$this->session->set_flashdata('echec', validation_errors());
				redirect("projet/documents/".$document->idprojet);
			}

		}	

	} else {
		$this->session->set_flashdata('echec', 'Vous n\'avez pas les droits suffisants');
		redirect("projet/documents/".$doc->idprojet);
	}
}

// Vérification dossier
public function verifie_dossier($idnvdossier, $idprojet) {
	if ($idnvdossier == 0)
		return true;

	$this->load->model("repertoire_model");
	return $this->repertoire_model->verifier_repertoire($idnvdossier, $idprojet);
}

// Télécharger un fichier // En cours (Guillaume)
public function telecharger($iddoc){
	// Récupération des infos
	$this->load->model("utilisateur_model");
	

	$doc = $this->document_model->recuperer_document($iddoc);

	$droits = $this->utilisateur_model->verifier_droit($iddoc, $this->donnees->idutilisateur);

	if($droits->ecriture || $doc->idutilisateur == $this->donnees->idutilisateur) {
		
		$this->document_model->telecharger_document ($iddoc); // temporaire

		
		$this->session->set_flashdata('succes', 'Document telecharger');
		redirect("projet/documents/".$doc->idprojet);
	} else {
		$this->session->set_flashdata('echec', 'Vous n\'avez pas les droits suffisants');
		redirect("projet/documents/".$doc->idprojet);
	}

}

public function telecharger_zip($iddoc){
	// Récupération des infos
	$this->load->model("utilisateur_model");
	$this->load->library('zip');

	$doc = $this->document_model->recuperer_document($iddoc);

	$droits = $this->utilisateur_model->verifier_droit($iddoc, $this->donnees->idutilisateur);

	if($droits->ecriture || $doc->idutilisateur == $this->donnees->idutilisateur) {
		
		$this->document_model->telecharger_zip ($iddoc,0); // temporaire

		$this->zip->download('download.zip');
		$this->session->set_flashdata('succes', 'Document telecharger');
		redirect("projet/documents/".$doc->idprojet);
	} else {
		$this->session->set_flashdata('echec', 'Vous n\'avez pas les droits suffisants');
		redirect("projet/documents/".$doc->idprojet);
	}

}



////////////////////////
// GESTION DES DROITS //
////////////////////////

//modification des droits d'un groupe
public function modifier_groupe($iddoc){
	// On récupère la liste des droits
	$this->load->model("utilisateur_model");

	// Récupération des infos
	$data["document"] = $this->document_model->recuperer_document($iddoc);
	$idprojet = $data["document"]->idprojet;

	// Si l'utilisateur est administrateur ou si il est proprietaire
	$data["utilisateur"] = $this->profil_model->recuperer_mes_donnees();
	if(!$this->utilisateur_model->verifier_admin_projet($idprojet,$data["utilisateur"]->idutilisateur) &&
			$data["document"]->idutilisateur != $data["utilisateur"]->idutilisateur) {
		echo "Vous n\'avez pas les droits sur ce document !";

	} else {
		//vérification du formulaire
		$this->load->library('form_validation');	
		$this->form_validation->set_rules('idgroupe', 'Identifiant du Groupe', 'required|callback_verifie_groupe['.$idprojet.']');


		//si formulaire envoyé et bon
		if ($this->form_validation->run() == TRUE){	
			if(!($droits = $this->input->post("droits")) || ! is_array($droits) ){
				$lecture       = "0";
				$ecriture      = "0";
				$visualisation = "0";
			} else {
				$lecture       = in_array("lecture", $droits)         ? 1 : 0;
				$ecriture      = in_array("ecriture", $droits)        ? 1 : 0;
				$visualisation = in_array("visualisation", $droits)   ? 1 : 0;
			}

			$this->document_model->modification_droit_document($iddoc, $this->input->post("idgroupe",TRUE), $visualisation,
													 				$lecture, $ecriture);
			echo "succes";
		//si formulaire non envoyé ou mal rempli
		} else {
			echo validation_errors();
		}
	}
}

// Vérification existence groupe
public function verifie_groupe($idgroupe, $idprojet)
{
	//vérifie si le groupe existe et appartient au projet
	if($this->projet_model->groupe_appartient($idgroupe,$idprojet)){
		return TRUE;
	} else {
		$this->form_validation->set_message('verifie_groupe', 'Le groupe n\'existe pas ou n\'appartient pas au projet'.$idgroupe);
		return false;
	}
}

}
