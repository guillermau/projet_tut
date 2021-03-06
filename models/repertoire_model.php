<?php 

class Repertoire_model extends CI_Model {
	// Noms des tables
	protected $repertoires = 'repertoires';
	protected $documents   = 'documents';

	function __construct()
    {
        parent::__construct();
    }
	
    // Informations sur le répertoire
    public function infos_repertoire($idrepertoire) {
    	return $this->db->select("*")
    					->from($this->repertoires)
    					->where("idrepertoire", $idrepertoire)
    					->get()
    					->row();
    }
    
    public function nom_repertoire($idrep){
        $data = $this->db->select("*")
    					->from("repertoires")
    					->where("idrepertoire", $idrep)
    					->get()
    					->row()
                                        ;
        return "$data->nom";
    }
    
    public function idprojet_repertoire($idrep){
        $data = $this->db->select("*")
    					->from("repertoires")
    					->where("idrepertoire", $idrep)
    					->get()
    					->row();
        return $data->idprojet;
    }
    
    
    // Retourner id du pere
    public function pere_repertoire($idproj, $idrep) {
            $data = $this->db->select("*")
                                             ->from("repertoires")
                                             ->where("idprojet", $idproj)
                                             ->where("idrepertoire", $idrep)
                                             ->get()
                                             ->row();
            return $data->pere;

    }


    //Liste les parents d'un repertoire
    public function arborescence($idprojet,$idrep){
       $this->load->model("projet_model");

        $data = array();  
        if ($idrep == null){
            $data[$this->projet_model->recuperer_projet($idprojet)->nom]= $this->chemin_clic_rep($idprojet,null);
            return $data;
        }
        else{
            $idpere = $this->pere_repertoire($idprojet, $idrep);
            $data[$this->nom_repertoire($idrep)]=$this->chemin_clic_rep($idprojet, $idrep);
            while($idpere != null){
                $data[$this->nom_repertoire($idpere)]=$this->chemin_clic_rep($idprojet, $idpere);
                $idpere = $this->pere_repertoire($idprojet, $idpere);
            }
            $data[$this->projet_model->recuperer_projet($idprojet)->nom]= $this->chemin_clic_rep($idprojet,null);
            return array_reverse($data);
        }


    }
         

    // Liste de tous les répertoires d'un projet
    public function lister_repertoires_projet($idproj) {
            return $this->db->select("*")
                                            ->from("repertoires")
                                            ->where("idprojet", $idproj)
                                            ->order_by("pere")
                                            ->get()
                                            ->result();
    }


    // Lister repertoire en fonction du pere
    public function lister_repertoires($idproj, $idpere) {
            return $this->db->select("*")
                                            ->from("repertoires")
                                            ->where("idprojet", $idproj)
                                            ->where("pere", $idpere)
                                            ->get()
                                            ->result();
    }


    // Retourne le chemin physique du repertoire // Guillaume
    public function chemin_phys_rep($idrep) {
            $data = $this->db->select("*")
                                    ->from($this->repertoires)
                                    ->where("idrepertoire", $idrep)
                                    ->get()
                                    ->row();

            $chemin = site_url("uploads/projets/".$data->idprojet."/".$data->idrepertoire);

            return $chemin;
    }

    // Retourne le chemin virtuel du repertoire par rapport a la racine // Guillaume
    /*public function chemin_virt_rep($idrep,$idracine) {
        $this->load->model("projet_model");

        $data = array();  
        if ($idrep == null){
            $data = 'test';
            return $data;
        }
        else{
            $idpere = $this->pere_repertoire($idprojet, $idrep);
            $data[$this->nom_repertoire($idrep)]=$this->chemin_clic_rep($idprojet, $idrep);
            while($idpere != null){
                $data[$this->nom_repertoire($idpere)]=$this->chemin_clic_rep($idprojet, $idpere);
                $idpere = $this->pere_repertoire($idprojet, $idpere);
            }
            $data[$this->projet_model->recuperer_projet($idprojet)->nom]= $this->chemin_clic_rep($idprojet,null);
            return array_reverse($data);
        }
    }*/


    // Retourne l'adresse de l'application du repertoire // Guillaume
    public function chemin_clic_rep($idprojet,$idrep) {

        $chemin = "projet/documents/".$idprojet;
        if($idrep==null)
                return $chemin;
        else{
            $chemin = $chemin."/".$idrep;
            return $chemin;
        }	
    }

    // Telecharger repertoire // Guillaume
    // Possibilite de controle des repertoires
    public function telecharger_rep ($idrep,$idracine) { //
            $this->load->model("document_model");

            if(!is_null($idrep) && $idrep != 0){
            
            // Controle si presence de documents
            $query = $this->db->query('SELECT iddocument FROM '.$this->documents.' WHERE idrepertoire = '.$idrep);
            
            $nb_docs = $query->num_rows() ;
            $pres_docs = false;
            
            if ($nb_docs > 0){
                foreach ( $query->result_array() as $row) // Oui, ajout au zip
                {
                    $this->document_model->telecharger_zip ($row['iddocument'],$idracine);
                }
                $pres_docs = true;
            }

            $query = $this->db->query('SELECT idrepertoire FROM '.$this->repertoires.' WHERE pere = '.$idrep);
            
            
            $nb_rep = $query->num_rows();
            
            if ( $nb_rep > 0){
                foreach ( $query->result_array() as $row) // Oui, ajout au zip
                {
                    $var = $this->telecharger_rep($row['idrepertoire'],$idracine);
                    if ($var) $pres_docs = true;
                }
            }
            
            if($pres_docs)
                return true;
            }
            else {
                return false;
            }
    }

	// Créer répertoire
	public function creer_repertoire($idprojet, $nom, $idpere = null) {
		if(empty($idpere))
			$idpere = null;

		$data = array('idprojet' => $idprojet,
					  'nom'		 => $nom,
					  'pere'   => $idpere);

		$this->db->insert($this->repertoires, $data);
	}

	// Supprimer repertoire
	public function supprimer_repertoire($idrepertoire) {
		return $this->db->where("idrepertoire", $idrepertoire)
						->delete($this->repertoires);
	}

	// Vérifier existence répertoire
	public function verifier_repertoire($idrep, $idproj) {
		$data = $this->db->select("*")
						 ->from($this->repertoires)
						 ->where("idprojet", $idproj)
						 ->where("idrepertoire", $idrep)
						 ->get()
						 ->result();

		if (count($data) == 1)
			return true;
		else
			return false;
	}

	// Vérifier repertoire vide
	public function verifier_repertoire_vide($idrepertoire) {

		// ETAPE 1 : On vérifie que aucun document n'est dans le dossier
		$data = $this->db->select("iddocument")
						 ->from($this->documents)
						 ->where("idrepertoire", $idrepertoire)
						 ->get()
						 ->result();

		if (count($data) != 0) {
			return false;

		// Si non, on passe à l'étape 2
		} else {

			// ETAPE 2 : On recherche les éventuels répertoires fils
			$data = $this->db->select("idrepertoire")
							 ->from($this->repertoires)
							 ->where("pere", $idrepertoire)
							 ->get()
							 ->result();

			if (count($data) != 0) {
				return false;

			// Si non il est vide
			} else {
				return true;
			}


		}
	}
        
    // Telecharger repertoire // Guillaume
    // Possibilite de controle des repertoires
    public function vider_rep ($idrep) { //
        $this->load->model("document_model");

        if(!is_null($idrep) && $idrep != 0){

        // Controle si presence de documents
        $query = $this->db->query('SELECT iddocument FROM '.$this->documents.' WHERE idrepertoire = '.$idrep);

        if ($query->num_rows() > 0){
            foreach ( $query->result_array() as $row) // Oui, ajout au zip
            {
                $this->document_model->supprimer_document($row['iddocument']);
            }
        }

        $query = $this->db->query('SELECT idrepertoire FROM '.$this->repertoires.' WHERE pere = '.$idrep);

        if ($query->num_rows() > 0){
            foreach ( $query->result_array() as $row) // Oui, ajout au zip
            {
                    $this->vider_rep($row['idrepertoire']);
            }
        }
        $this->supprimer_repertoire($idrep);
        }
    }
        
}        
?>