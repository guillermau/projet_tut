<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed'); 

/**
 * Aide à la gestion de l'affichage des views
 *
 * @author  Alexis DUBREUIL, Bernardo DE PAULA RITTMEYER, David MURAT, Geoffrey MATHIOT
 */

class Template {
	private static $CI;
	// Configurations par défaut 
	private $config = array (
			"titre_defaut" => "Entrepot de données écologiques",
			"template_view" => "template",
			"view_defaut" => "404"
		);
	
	/**
	 * Constructeur
     */
	public function __construct()
	{
		// Récupère l'instance de codeigniter
		$this->CI =& get_instance();
		// Récupère les données du fichier de configuration "config/template.php"
		$this->CI->config->load('template', TRUE);
		$configFile = $this->CI->config->item('template');
		// Met les données dans la variable de configuration
		if( !empty($configFile)){
			foreach($configFile as $k => $v){
				$this->config[$k] = $v;
			}
		}
	}

	/**
     * Méthode responsable pour l'affichage du contenu
     *
     * Ce méthode affiche la composition d'une view choisi avec le template
     * du site web.
     *
     * Les variables passées au template paramètre sont classées d'après ses prefixes:
     *
     * * t_ : sont les données passées au template
     * 	+ t_meta : sont les données de type metadata de la page
     * 
     * ** t_script : est une array avec le chemin des fichiers script à charger en plus **
     *
     * ** Tous les autres variables seront passées directement à la view **
     *
     * @param string $view le nom (ou chemin) de la view a afficher (sans .php a la fin)
     * @param array $data une liste des variables à passer à la view et le template
     *
     * @return void
     */

    public function render($view, &$data = false) {
    	// La array des variables du template
    	$tdata = array();

		// Définit li titre de la page
		if(!empty($data['t_title'])){
			$tdata['title'] = array_export($data['t_title']);
		} else {
			$tdata['title'] = $this->config['titre_defaut'];
		}

		// Définit les métadata du template
		if(!empty($data)) {
			foreach($data as $k => $d){
				//si la variable est du type métadata
				if (! strncmp($k,"t_meta_",7)) {
					$tdata[substr($k,7)] = array_export($data[$k]);
				}
			}
		}

		// Définit les script extras à charger
		
		if(!empty($data['t_script'])){
			$tdata['script'] = array_export($data['t_script']);
		}

		// Définit dans quel subpartie du site(menu horizontal) on est
		
		if(!empty($data['t_sub'])){
			$tdata['sub'] = array_export($data['t_sub']);
		} else {
			$tdata['sub'] = "null";
		}

		// Si c'est une requête AJAX, envoyer que la view
		if(is_ajax()){
			$this->CI->load->view($view,$data);
		} else {
			// Si le paramètre $view est vide, on charge la view par défaut
			// sinon on charge la view correspondante
			if(empty($view)){
				$tdata['content'] = $this->config['view_defaut'];
			} else {
				$tdata['content'] = $this->CI->load->view($view,$data,TRUE);
			}
			// Et on affiche le résultat
			$this->CI->load->view($this->config['template_view'],$tdata);
		}
	}
}

/* EOF Template.php */