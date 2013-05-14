<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');
/**
 * Helper avec des outils
 */

/**
 * Fonction pour traduire la fonction date au français
 *
 * @param string $format le format de la date (voir man de date())
 * @param int $time timestamp de la date a afficher (par défaut time())
 *
 * @return string la date formaté en français
 */
if ( ! function_exists('fr_date'))
{
	function fr_date($format = 'l , j de F', $time = NULL){
		if($time === NULL){
			$time = time();
		}
		$date = date('l,F,S,M',$time);
		$date = explode(',',$date);

		$day    = $date[0];
		$month  = $date[1];
		$suffix = $date[2];
		$mabrev = $date[3];

		switch($day)
		{
			case 'Monday':    $day = '\L\u\n\d\i'; 		break;
			case 'Tuesday':   $day = '\M\a\r\d\i';   	break;
			case 'Wednesday': $day = '\M\e\r\c\r\e\d\i'; 	break;
			case 'Thursday':  $day = '\J\e\u\d\i';  		break;
			case 'Friday':    $day = '\V\e\n\d\r\e\d\i';   	break;
			case 'Saturday':  $day = '\S\a\m\e\d\i';  	break;
			case 'Sunday':    $day = '\D\i\m\a\n\c\h\e'; 	break;
			default:          $day = '';	    	break;
		}

		switch($month)
		{
			case 'January':   $month = '\J\a\n\v\i\e\r';   break;
			case 'February':  $month = '\F\é\v\r\i\e\r';   break;
			case 'March':     $month = '\M\a\r\s';      break;
			case 'April':     $month = '\A\v\r\i\l';     break;
			case 'May':       $month = '\M\a\i';       break;
			case 'June':      $month = '\J\u\i\n';      break;
			case 'July':      $month = '\J\u\i\l\l\e\t';   break;
			case 'August':    $month = '\A\o\û\t';      break;
			case 'September': $month = '\S\e\p\t\e\m\b\r\e'; break;
			case 'October':   $month = '\O\c\t\o\b\r\e';   break;
			case 'November':  $month = '\N\o\v\e\m\b\r\e';  break;
			case 'December':  $month = '\D\é\c\e\m\b\r\e';  break;
			default:          $month = ''; 		    break;
		}

		if($suffix == 'st'){
			$suffix = '\e\r';
		} else {
			$suffix = '\é\m\e';
		}

		switch($mabrev)
		{
			case 'Jan': $month = '\J\a\n\v'; break;
			case 'Feb': $month = '\F\é\v\r'; break;
			case 'Mar': $month = '\M\a\r\s'; break;
			case 'Apr': $month = '\A\v\r';  break;
			case 'May': $month = '\M\a\i';  break;
			case 'Jun': $month = '\J\u\i\n'; break;
			case 'Jul': $month = '\J\u\i\l'; break;
			case 'Aug': $month = '\A\o\û\t'; break;
			case 'Sep': $month = '\S\e\p\t'; break;
			case 'Oct': $month = '\O\c\t';  break;
			case 'Nov': $month = '\N\o\v';  break;
			case 'Dec': $month = '\D\é\c';  break;
			default:    $month = ''; 	 break;
		}

		str_replace(array('l','F','M','S'),array($day,$month,$mabrev,$suffix), $format);

		return date($format, $time);
	}
}

/**
 * Fonction pour vérifier si une requête est AJAX
 *
 * @return boolean vrai si la requête est ajax, faux sinon
 */

if ( ! function_exists('is_ajax'))
{
	function is_ajax() {
		return (isset($_SERVER['HTTP_X_REQUESTED_WITH']) && ($_SERVER['HTTP_X_REQUESTED_WITH'] == 'XMLHttpRequest'));
	}
}

/**
 * Fontion qui retourne la valeur d'une entrée d'une array et supprime cette entrée
 *
 * @param mixed $element une entrée d'une array (Ex.: $a['foo'])
 *
 * @return string valeur de l'entrée $element
 */
if ( ! function_exists('array_export'))
{
	function array_export(&$element){
		$return = $element;
		unset($element);
		return $return;
	}
}


if ( ! function_exists('ajax_upload'))
{
	function ajax_upload() {
		return (isset($_SERVER['HTTP_X_FILE_NAME']) ? $_SERVER['HTTP_X_FILE_NAME'] : false);
	}
}