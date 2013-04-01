<?php
/*
Plugin Name: Automatic Exporter
Plugin URI: http://triphere.com
Description: Plugin to automatically export an an entire set of a post-type's entries on post save
Version: .1
Author: Scott Nath
Author URI: http://triphere.com
License: GPL
Copyright: Scott Nath
*/


include_once("automatic-exporter-options.php");
include_once("exporter.php");


/*--------------------------------------------------------------------------------------
*
*	automatic_exporter_create_xml
*
*	@desc Creates a Wordpress xml file using Wordpress' wp_export function. The file is updated any time a PAGE is updated - no other content types will update the xml file.
*	@author Scott Nath
*	@uses http://codex.wordpress.org/Plugin_API/Action_Reference
*	@since March 7, 2013
* 
*-------------------------------------------------------------------------------------*/

function automatic_exporter_create_xml() {
global $post;

	if(!$post)
		return;
		
	//get the data to an array
	$ae_options = get_option('ae_options');

	if ( $ae_options['automatic_exporter_post_type_select'] != $post->post_type )
		return;
		
		
if($ae_options['automatic_exporter_folder']){

	if (!is_dir($ae_options['automatic_exporter_folder'])) {
	   mkdir($ae_options['automatic_exporter_folder']);
	}
	$automatic_exporter_folder = $ae_options['automatic_exporter_folder'];
	
}else{

	$upload_dir = wp_upload_dir();
	if (!is_dir($upload_dir['basedir'] . "/automatic_exporter/")) {
	   mkdir($upload_dir['basedir'] . "/automatic_exporter/");
	}
	$automatic_exporter_folder = $upload_dir['basedir'] . "/automatic_exporter/";
	
}

if($ae_options['automatic_exporter_filename']){

	$automatic_exporter_filename = $ae_options['automatic_exporter_filename'];
	
}else{

	$automatic_exporter_filename = "section-export.xml";
	
}


$automatic_exporter_file = ae_includeTrailingCharacter($automatic_exporter_folder, '/').$automatic_exporter_filename;


// verify if this is an auto save routine. 
	// If it is our form has not been submitted, so we dont want to do anything
	if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) 
		return;
	
	
		
	//NATH: problem is that it calls this function twice - once to add the revision, then another time to add the real post. When it makes the xml, it's not getting the latest info
	
		$args = array();
		$args['content'] = $ae_options['automatic_exporter_post_type_select'];
		ob_start();
		
		automatic_exporter_export_wp($args);
		
		$section_xml = ob_get_clean();
	
	
		$fp = fopen($automatic_exporter_file, 'w');
		fwrite($fp, $section_xml);
	
	add_action('save_post', array(&$this, __FUNCTION__));
}

add_action("wp_insert_post", "automatic_exporter_create_xml");

/**
 * Ensure that the string ends with the specified character
 *
 * @param string $string String to validate
 * @return string
 */
function ae_includeTrailingCharacter($string, $character){
	if (strlen($string) > 0) {
		if (substr($string, strlen($string) -1, 1) != $character) {
			return $string . $character;
		} else {
			return $string;
		}
	} else {
		return $character;
	}
}

function ae_return_post_types(){
	$builtin_post_types = array();
	$args=array(
	  'public'   => true,
	  '_builtin' => true
	); 
	$output = 'names'; // names or objects, note names is the default
	$operator = 'and'; // 'and' or 'or'
	$builtin_post_types=get_post_types($args,$output,$operator);
	
	$post_types = array();
	$args=array(
	  'public'   => true,
	  '_builtin' => false
	);
	$post_types=get_post_types($args,$output,$operator);
	return array_merge($post_types,$builtin_post_types);
}
?>