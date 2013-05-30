<?php 

// Gestion de upload de fichiers
class Upload_model extends CI_Model{

	protected $table = "documents";
	protected $taille_profil = 50;
	protected $largeur_miniature = 200;

	//constructeur
	public function __construct(){
		parent::__construct();
		$this->load->model("document_model");
		$this->load->model("type_model");
		$ci =& get_instance();
     	$ci->config->load('profil');
		$this->taille_profil = $ci->config->item('taille_img_profil');
	}


	/* Envoyer des documents
	*  
	*  @param maj TRUE si on veut faire le maj du fichier, FALSE si on ne veut pas (on anexe un chiffre a la fin)
	*/
	public function upload($input, $idprojet, $idutilisateur, $idrepertoire,$maj)
        {
		$upload_info = array(
			"succes" => array(),
			"echec"  => array()
			);
		$this->load->library('upload');
 		if($idrepertoire != 0)
                {
 			$config['upload_path']   = APPPATH.'../../uploads/projets/'.$idprojet.'/'.$idrepertoire.'/';
 		}
                else
                {
 			$config['upload_path']   = APPPATH.'../../uploads/projets/'.$idprojet.'/';
 		}

 		if (!is_dir($config['upload_path'])) {
		    mkdir($config['upload_path']);
		}
		
		//$config['max_size']   = '3000'; max size doit être à zero
		if($maj)
                {
			$config['overwrite'] = TRUE;
		} 
                else
                {
			$config['overwrite'] = FALSE;
		}
		 
		$config['allowed_types'] = '*';
		$config['max_size'] = '0';
		$this->upload->initialize($config);
		 
		$uploaded = $this->upload->up(TRUE);
		if(! empty($uploaded['success'])) {
			foreach($uploaded['success'] as $u){
				if($maj)
                                {
					$this->document_model->mise_a_jour($u['file_name'], $idrepertoire, $idprojet);

				} else
                                {
					$upload_info["id"] = $this->document_model->creer_document($idprojet, $idutilisateur, $u['client_name'], $u['file_name'], $this->type_model->id_extension($u['file_ext']), "", $idrepertoire, "");
				}
				$upload_info["success"][] = $u;
			}
		}
		if(! empty($uploaded['error'])) {
			foreach($uploaded['error'] as $u){
				$upload_info["error"][] = $u;
			}
		}
		return $upload_info;
	}

	// Supprimer fichier

	public function supprimer_fichier($chemin){
		return @unlink($chemin);
	}

	// Deplacer fichier

	public function deplacer_fichier($nom_fichier, $nouveau_projet, $ancien_repertoire = NULL, $nouveau_repertoire = NULL){
		$chemin 	 = APPPATH.'../../uploads/projets/'.$nouveau_projet.'/';
		$nouv_chemin = APPPATH.'../../uploads/projets/'.$nouveau_projet.'/';

		if(!empty($ancien_repertoire) && $ancien_repertoire != 0) {
			$chemin .= $ancien_repertoire.'/';
		}
		$chemin .= $nom_fichier;
		
		if(!empty($nouveau_repertoire) && $nouveau_repertoire != 0) {
			$nouv_chemin .= $nouveau_repertoire.'/';
		}

 		if (!is_dir($nouv_chemin)) {
		    mkdir($nouv_chemin);
		}

		$nouv_chemin .= $nom_fichier;

		return @rename($chemin, $nouv_chemin);

	}

	// Envoyer des images profil projet
	public function upload_image_profil($input, $id,$projet = true){
		if(empty($_FILES[$input])){
			return false;
		}
		if($projet){
			$chemin = 'projet/';
		} else {
			$chemin = 'utilisateur/';
		}
		$config = array();
		$config['upload_path'] = APPPATH.'../../uploads/profil/'.$chemin;
		$config['allowed_types'] = 'gif|jpg|png|jpeg';
		$config['file_name'] = $id;
		$config['overwrite'] = true;

		$this->load->library('upload', $config);

		if ( ! $this->upload->do_upload($input))
		{
			return array("echec" => "<strong>Upload : </strong>".$this->upload->display_errors());
		}
		else
		{
			$data = $this->upload->data();
			if($data['is_image']){

				$w = $data["image_width"];
				$h = $data["image_height"];

				$config = array();
				$config['image_library'] = 'gd2';
				$config['source_image'] = $data["full_path"];
				$config['maintain_ratio'] = TRUE;
				$config['width'] = $this->taille_profil;
				$config['height'] = $this->taille_profil;

				if($w > $h) {
					$config['master_dim'] = 'height';
				} else {
					$config['master_dim'] = 'width';
				}

				$this->load->library('image_lib', $config);

				if ( ! $this->image_lib->resize()) {
					    return array("echec" => "<strong>Traitement d'image : </strong>".$this->image_lib->display_errors());
				}

				if($w != $h) {
					$config['maintain_ratio'] = FALSE;
					if($w > $h) {
						$config['x_axis'] = ( ( ( $this->taille_profil * $w ) / $h ) - $this->taille_profil ) / 2;
						$config['y_axis'] = '0';
					} else {
						$config['x_axis'] = '0';
						$config['y_axis'] = ( ( ( $this->taille_profil * $h ) / $w ) - $this->taille_profil ) / 2;
					}
					$this->image_lib->clear();
					$this->image_lib->initialize($config);

					if ( ! $this->image_lib->crop())
					{
					    return array("echec" => "<strong>Traitement d'image : </strong>".$this->image_lib->display_errors());
					}
				}
			}

			if($projet){
				$this->db->where('idprojet',$id)->update('projets',array('image'=>$data['file_name']));
			} else {
				$this->db->where('idutilisateur',$id)->update('utilisateurs',array('image'=>$data['file_name']));
			}

			return $data;
		}
	}

	// Supprimer les images de profil
	public function supprime_image_profil($id, $projet = true) {
		if($projet){
			$p = $this->db->from('projets')->where('idprojet',$id)->select('image')->get()->row();
			$this->db->where('idprojet',$id)->update('projets',array('image'=>''));
			@unlink(APPPATH.'../../uploads/profil/projet/'.$p->image);
		} else {
			$u = $this->db->from('utilisateurs')->where('idutilisateur',$id)->select('image')->get()->row();
			$this->db->where('idutilisateur',$id)->update('utilisateurs',array('image'=>''));
			@unlink(APPPATH.'../../uploads/profil/utilisateur/'.$u->image);
		}
	}

	// vérifie le type de document et fait une procédure par défaut pour le type

	// créer miniature de l'image
	public function creer_miniature($chemin) {
		$config = array();
		$config['image_library'] = 'gd2';
		$config['source_image'] = $data["full_path"];
		$config['maintain_ratio'] = TRUE;
		$config['width'] = $taille_profil;
		$config['height'] = $taille_profil;

		if($w > $h) {
			$config['master_dim'] = 'height';
		} else {
			$config['master_dim'] = 'width';
		}

		$this->load->library('image_lib', $config);

		$this->image_lib->resize();

		if($w != $h) {
			$config['maintain_ratio'] = FALSE;
			if($w > $h) {
				$config['x_axis'] = ( ( ( $taille_profil * $w ) / $h ) - $taille_profil ) / 2;
				$config['y_axis'] = '0';
			} else {
				$config['x_axis'] = '0';
				$config['y_axis'] = ( ( ( $taille_profil * $h ) / $w ) - $taille_profil ) / 2;
			}
			$this->image_lib->initialize($config);

			if ( ! $this->image_lib->crop())
			{
			    return array("echec" => $this->image_lib->display_errors());
			}
		}
	}
}