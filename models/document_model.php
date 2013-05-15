<?php 

class Document_model extends CI_Model {
	// Nom de la table documents
	protected $table = 'documents';
	protected $tag_document = 'tag_document';
	protected $droit_document = 'droit_document';
	protected $droit_groupe = 'droit_groupe';
	protected $images = 'images';
	protected $types = 'types';
	protected $groupes = 'groupes';

	function __construct()
    {
        parent::__construct();
    }
    
	// Tous documents d'un utilisateur
	public function documents_utilisateur($idusr) {
		$this->load->model("type_model");
		$types = $this->type_model->liste_types();

		$query = $this->db->select('*')
						->from($this->table)
						->where('idutilisateur', $idusr)
						->get();

		if ($query->num_rows() > 0) {
			$data = array();
			foreach($query->result() as $row) {
				if($row->idrepertoire == null) {
					$rep = 0;
				} else {
					$rep = $row->idrepertoire;
				}
				$row->type = $types[$row->type];
				$data[$row->idprojet][] = $row;
			}
			return $data;
		} else {
			return false;
		}
	}

	// Tous documents et répertoires d'un projet
	public function documents_projet($idprj) {
		$this->load->model("type_model");
		$types = $this->type_model->liste_types();

		$query = $this->db->select('*')
						->from($this->table)
						->where('idprojet', $idprj)
						->get();
		if ($query->num_rows() > 0) {
			$data = array();
			foreach($query->result() as $row) {
				if($row->idrepertoire == null) {
					$rep = 0;
				} else {
					$rep = $row->idrepertoire;
				}
				$row->type = $types[$row->type];
				$data[] = $row;
			}
			return $data;
		} else {
			return false;
		}
	}

	// Tous documents et répertoires d'un projet
	public function documents_projet_et_droits($idprj, $idrep, $idutilisateur) {
		$this->load->model("type_model");
		$this->load->model("utilisateur_model");
		$types = $this->type_model->liste_types();

		// EXP
		if (is_null($idrep))
			$idrep = 0;
		// FIN EXP

		$query = $this->db->select('*')
						->from($this->table)
						->where('idprojet', $idprj)
						->where('idrepertoire', $idrep)
						->get();
		if ($query->num_rows() > 0) {
			$data = array();
			foreach($query->result() as $row) {
				$row->droits = $this->utilisateur_model->verifier_droit($row->iddocument,$idutilisateur);
				
				if($row->idrepertoire == null) {
					$rep = 0;
				} else {
					$rep = $row->idrepertoire;
				}
				$row->type = $types[$row->type];
				$data[] = $row;
			}
			return $data;
		} else {
			return false;
		}
	}
	
	// Compteur de documents
	public function nombre_documents() {
		$this->db->select('iddocument')
						->from($this->table);
		return  $this->db->count_all_results()
						->get()
						->row();
	}

	// Document par identifiant
	public function recuperer_document($iddoc) {
            return $this->db->select('*')
                                    ->from($this->table)
                                    ->where('iddocument',$iddoc)
                                    ->get()
                                    ->row();

    }

	// Documents triés par: nom, date, type, responsable
	public function liste_documents_tri($tri= "alphabetique", $ordre = "ASC", $limit = 100, $offset = 0) {
		switch($tri) {
			case "alphabetique":
				$this->db->order_by("nom_original",$ordre);
			break;
			case "date":
				$this->db->order_by("creation",$ordre);
			break;
			case "type":
				$this->db->order_by("type",$ordre);
			break;
		}

		return $this->db->select('nom_original','type','creation')
						->from($this->table)
						->limit($limit,$offset)
						->get()
						->result();

	}

	// Vérifier droits du document pour un groupe
	public function verification_droit_document ($iddoc, $idgr){
		return $this->db->select('visualisation','lecture','ecriture')
						->from($this->droit_document)
						->where('iddocument',$iddoc)
						->where('idgroupe',$idgr)
						->get()
						->row();
	}

	// Lister les droits d'un document
	public function liste_droit_document ($iddoc) {
		return $this->db->select('*')
						->from($this->droit_document)
						->join('groupes', 'droit_document.idgroupe = groupes.idgroupe')
						->where('iddocument',$iddoc)
						->get()
						->result();
	}

	// Lister les droits d'un document et du projet
	public function liste_droit_projet_document ($iddoc, $idproj) {
		
		$data = $this->db->select("*")
						 ->from("groupes")
						 ->where('idprojet',$idproj)
						 ->get()
						 ->result();

		foreach ($data as $rule) {

			// Si des droits spécifiques sont attribués
			$specRule = $this->db->select("*")
								 ->from($this->droit_document)
								 ->where('iddocument',$iddoc)
								 ->where('idgroupe',$rule->idgroupe)
								 ->get();
			if ($specRule->num_rows() == 1) {
				$result = $specRule->row();

				$rule->visualisation = $result->visualisation;
				$rule->lecture		 = $result->lecture;
				$rule->ecriture		 = $result->ecriture;
			}

			// Si aucun droit spécifique n'est définit
			else {
				$specRule = $this->db->select("*")
									 ->from("droit_groupe")
									 ->where('idgroupe',$rule->idgroupe)
									 ->get()
									 ->row();

				$rule->visualisation = $specRule->lecture;
				$rule->lecture		 = $specRule->lecture;
				$rule->ecriture		 = $specRule->ecriture;
			}
		}

		return $data;
	}

	// Modifier droits du document pour un groupe
	public function modification_droit_document ($iddoc, $idgr, $visualisation, $lecture, $ecriture){

		// Si des droits spécifiques sont déjà attribués
		$specRule = $this->db->select("*")
							 ->from($this->droit_document)
							 ->where('iddocument',$iddoc)
							 ->where('idgroupe',$idgr)
							 ->get();
		if ($specRule->num_rows() == 1) {
			$data = array(
				'visualisation' => $visualisation,
				'lecture'		=> $lecture,
				'ecriture'		=> $ecriture);
				
			$this->db->where('iddocument', $iddoc)
					 ->where('idgroupe', $idgr)
		 			 ->update($this->droit_document,$data);
		}

		else {
			$data = array(
				'iddocument'	=> $iddoc,
				'idgroupe'		=> $idgr,
				'visualisation' => $visualisation,
				'lecture'		=> $lecture,
				'ecriture'		=> $ecriture);

			$this->db->insert($this->droit_document,$data);
		}

	}

	// Creer nouveau droit sur document
	public function nouveau_droit_document ($iddoc, $idgr, $visualisation, $lecture, $ecriture){
		$data = array(
			'iddocument'	=> $iddoc,
			'idgroupe'		=> $idgroupe,
			'visualisation' => $visualisation,
			'lecture'		=> $lecture,
			'ecriture'		=> $ecriture);
			
		$this->db->insert($this->droit_document,$data);
	}
	
	
	// Récuperer données des images
	public function recuperer_donnees_images ($iddoc){
		$tp = $this->db->select('type')
					->from($this->document);
		if( $tp == 'image'){
			return $this->db->select('*')
							->from($this->images)
							->where('iddocument',$iddoc)
							->get()
							->row();
		}
	}
	
	// Créer un nouveau document 
	public function creer_document($idprojet, $idutilisateur, $nom, $chemin_fichier, $idtype, $description, $idrepertoire, $tag){
		$data = array('idprojet'		=> $idprojet,
					  'idutilisateur'	=> $idutilisateur,
					  'chemin_fichier'	=> $chemin_fichier,
					  'type'			=> $idtype,
					  'creation'		=> date('Y-m-d h:i:s'),
					  'maj'				=> date('Y-m-d h:i:s'),
					  'description'		=> $description,
					  'nom_original'	=> $nom,
					  'idrepertoire'	=> $idrepertoire);
	
		$this->db->insert($this->table, $data);
		$iddoc = $this->db->insert_id();
		if( ! empty($tag)){
			$this->load->model('tag_model');
			$tags = explode ( "," , $tag );
			foreach ($tags as $t){
				$this->tag_model->ajouter_tag($t);
				$data = array('tag'	=> trim($t),
							  'iddocument' => $iddoc);

				$this->db->insert($this->tag_document, $data);
			}
			
		}
		return $iddoc;/*
		$typedoc = $this->db->select('type')
					->where('idtype',$idtype)
					->from($this->types)->get()->row()->type;
		if ($typedoc == 'image') {
			$this->recup_exif($iddoc);
			//creer_miniature($chemin_fichier);
		}*/
	}
	
	 
	// modifier un document
	public function modifier_document($iddoc, $description, $tag) {
		$data = array('description'	=> $description);

		$update = $this->db->where('iddocument',$iddoc)->update($this->table, $data);
		
		$this->load->model('tag_model');
		$this->tag_model->supprimer_tags_document($iddoc);
		if(! empty($tag)){
			$tags = explode ( "," , $tag );
			foreach ($tags as $t){
				$this->tag_model->ajouter_tag($t);
				$data = array('tag'	=> trim($t),
							  'iddocument' => $iddoc);

				$this->db->insert($this->tag_document, $data);
			} 	
		}
		return $update;
	}

	// Mise a jour, document
	public function mise_a_jour($nom, $idrepertoire, $idprojet) {
		return $this->db->where('nom_original',$nom)
						->where('idrepertoire',$idrepertoire)
						->where('idprojet',$idprojet)
						->update($this->table, array('maj' => date('Y-m-d h:i:s')));
	}

	// supprimer document // modifier (Guillaume)
	public function supprimer_document($iddoc) {
		$this->load->model('tag_model');
		$this->tag_model->supprimer_tags_document($iddoc);
	 	
		$document = $this->db->select('*')
						->from($this->table)
						->where('iddocument',$iddoc)
						->get()
						->row();
						
		$this->db->where('iddocument', $iddoc)->delete($this->images);
		$this->db->where('iddocument', $iddoc)->delete($this->droit_document);
		$this->db->where('iddocument', $iddoc)->delete($this->table);
		
		$this->load->model('upload_model');
		
		if ($document->idrepertoire == '0')
		{ $chemin = "uploads/projets/".$document->idprojet."/".$document->chemin_fichier;}
		else 
		{ $chemin = "uploads/projets/".$document->idprojet."/".$document->idrepertoire."/".$document->chemin_fichier;}
		
		$this->upload_model->supprimer_fichier($chemin);
		return $chemin;
	}
	
	// déplacer document
	public function deplacer_document($iddoc, $idnouvprojet, $idnouvrepertoire){
		$data = array('idprojet'=> $idnouvprojet,
					  'idrepertoire'	=> $idnouvrepertoire);
		$this->db->where('iddocument',$iddoc)->update($this->table, $data);
		$this->db->where('iddocument',$iddoc)->delete($this->droit_document);
		$this->load->model('upload_model');
		
		$chemin = $this->db->select('chemin_fichier')
					->where('iddocument',$iddoc)
					->from($this->document);
					
		$this->upload_model->deplacer_fichier($chemin, $idnouvprojet, $idnouvrepertoire);
		
	}
	
	// télécharger document // En cours (Guillaume)
	public function telecharger_document ($iddoc){
	$this->load->helper('download');
	
	$document = $this->db->select('*')
						->from($this->table)
						->where('iddocument',$iddoc)
						->get()
						->row();
	
	if ($document->idrepertoire == '0')
	{ $chemin = site_url("uploads/projets/".$document->idprojet."/".$document->chemin_fichier);}
	else 
	{ $chemin = site_url("uploads/projets/".$document->idprojet."/".$document->idrepertoire."/".$document->chemin_fichier);}
	
	$data = file_get_contents($chemin); // Read the file's contents
	$name = $document->chemin_fichier;

	force_download($name, $data); // Download direct
	}
	

	// Telechargement sous forme de zip // En cours (Guillaume)
	public function telecharger_zip ($iddoc,$idracine){
	$this->load->helper('download');
	$this->load->library('zip');
	
	
	$document = $this->db->select('*')
						->from($this->table)
						->where('iddocument',$iddoc)
						->get()
						->row();
	
	if ($document->idrepertoire == '0')
	{ $chemin = site_url("uploads/projets/".$document->idprojet."/".$document->chemin_fichier);}
	else 
	{ $chemin = site_url("uploads/projets/".$document->idprojet."/".$document->idrepertoire."/".$document->chemin_fichier);}
	
	$chemin_concret = $document->chemin_fichier;
	$idrep = $document->idrepertoire;
	
	if($document->idrepertoire != $idracine){
	
		do
		{
		$repertoire = $this->db->select("*")
							->from("repertoires")
							->where("idprojet", $document->idprojet)
							->where("idrepertoire", $idrep)
							->get()
							->row();
		$chemin_concret = $repertoire->nom."/".$chemin_concret;
		$idrep = $repertoire->pere;
		}
		while ( !is_null($idrep) && $idrep == $idracine);
	}
	
	$data = file_get_contents($chemin); // Read the file's contents
	//$name = $repertoire->nom."/".$document->chemin_fichier;
	
	//Avec la fonction zip
	$this->zip->add_data($chemin_concret, $data);
	
	
	
	}

	
	
	// changer répertoire du document
	public function changer_rep ($iddoc, $idnouvrepertoire){

		$document = $this->db->select('*')
							 ->from($this->table)
				  			 ->where('iddocument',$iddoc)
				  			 ->get()
				  			 ->row();

		// On vérifie si le fichier n'éxiste pas déjà
		$verif = $this->db->where('chemin_fichier', $document->chemin_fichier)
						  ->where('idrepertoire'  , $idnouvrepertoire)
						  ->where('idprojet'	  , $document->idprojet)
						  ->count_all_results($this->table);


		// Si le même document existe déjà, on bloque le déplacement
		if ($verif != 0)
			return false;
		else {
			$this->load->model('upload_model');
			$this->upload_model->deplacer_fichier($document->chemin_fichier, $document->idprojet, $document->idrepertoire, $idnouvrepertoire);

			$this->db->where('iddocument',$iddoc)
				 ->update($this->table, array('idrepertoire' => $idnouvrepertoire));

			return true;
		}
	}
	
	// récupération du exif d'une image
	public function recup_exif($iddoc){
		$chem_doc = $this->db->select('chemin_fichier')
					->where('iddocument',$iddoc)
					->from($this->document);
		$exif = exif_read_data($chem_doc, 0, true);
		$date = EXIF.DateTimeOriginal;
		$strexif = serialize($exif);
		$data = array ('iddocument' => $iddoc,
					   'exif'=> $strexif,
					   'date_prise'=> $date );
					  
		$this->db->insert($this->images, $data);
	}
}


?>