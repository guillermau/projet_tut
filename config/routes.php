<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');
/*
| -------------------------------------------------------------------------
| URI ROUTING
| -------------------------------------------------------------------------
| This file lets you re-map URI requests to specific controller functions.
|
| Typically there is a one-to-one relationship between a URL string
| and its corresponding controller class/method. The segments in a
| URL normally follow this pattern:
|
|	example.com/class/method/id/
|
| In some instances, however, you may want to remap this relationship
| so that a different class/function is called than the one
| corresponding to the URL.
|
| Please see the user guide for complete details:
|
|	http://codeigniter.com/user_guide/general/routing.html
|
| -------------------------------------------------------------------------
| RESERVED ROUTES
| -------------------------------------------------------------------------
|
| There area two reserved routes:
|
|	$route['default_controller'] = 'welcome';
|
| This route indicates which controller class should be loaded if the
| URI contains no data. In the above example, the "welcome" class
| would be loaded.
|
|	$route['404_override'] = 'errors/page_missing';
|
| This route will tell the Router what URI segments to use if those provided
| in the URL cannot be matched to a valid route.
|
*/

$route['default_controller'] = "session";
$route['404_override'] = 'erreur/not_found';
$route['mes-documents'] = 'membre/documents';
$route['mes-projets'] = 'membre/projets';
$route['mon-compte'] = 'membre';
$route['mon-compte/modifier'] = 'membre/modifier';
$route['mon-compte/modifier-image'] = 'membre/modifier_image';
$route['mon-compte/modifier-mdp'] = 'membre/modifier_mdp';
$route['projet/supprimer-groupe/(:any)'] = "projet/supprimer_groupe/$1";
$route['projet/creer-groupe/(:any)'] = "projet/creer_groupe/$1";
$route['projet/modifier-groupe/(:any)'] = "projet/modifier_groupe/$1";
$route['projet/modifier-image/(:any)'] = 'projet/modifier_image/$1';
$route['projet/creer-repertoire'] = 'projet/creer_repertoire';
$route['projet/supprimer-repertoire/(:any)'] = 'projet/supprimer_repertoire/$1';
$route['document/modifier-groupe/(:any)'] = "document/modifier_groupe/$1";
$route['session/recuperation-mdp'] = "session/recup_mdp";
$route['session/initialisation-mdp/(:any)'] = "session/init_mdp/$1";
/* End of file routes.php */
/* Location: ./application/config/routes.php */