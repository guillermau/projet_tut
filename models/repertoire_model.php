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

	// Retourner id du pere
	public function pere_repertoire($idproj, $idrep) {
		$data = $this->db->select("pere")
						 ->from("repertoires")
						 ->where("idprojet", $idproj)
						 ->where("idrepertoire", $idrep)
						 ->get()
						 ->row();

		return $data->pere;
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
}


?>