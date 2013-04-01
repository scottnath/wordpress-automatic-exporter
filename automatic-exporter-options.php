<?php

function automatic_exporter_options_page(){
  //include the main class file
  require_once("admin-page-class/admin-page-class.php");
  
  $upload_dir = wp_upload_dir();
  
  /**
   * configure your admin page
   */
  $config = array(    
    'menu'           => 'settings',             //sub page to settings page
    'page_title'     => __('Automatic Exporter','apc'),       //The name of this page 
    'capability'     => 'edit_themes',         // The capability needed to view the page 
    'option_group'   => 'ae_options',       //the name of the option to create in the database
    'id'             => 'admin_page',            // meta box id, unique per page
    'fields'         => array(),            // list of fields (can be added by field arrays)
    'local_images'   => false,          // Use local or hosted images (meta box images for add/remove)
    'use_with_theme' => false          //change path if used with theme set to true, false for a plugin or anything else for a custom path(default false).
  );  
  
  /**
   * Initiate your admin page
   */
  $options_panel = new BF_Admin_Page_Class($config);
  $options_panel->OpenTabs_container('');
  
  /**
   * define your admin page tabs listing
   */
  $options_panel->TabsListing(array(
    'links' => array(
    'options_1' =>  __('Exporter Options','apc'),
    )
  ));
  
  /**
   * Open admin page first tab
   */
  $options_panel->OpenTab('options_1');

  /**
   * Add fields to your admin page first tab
   * 
   * Simple options:
   * input text, checbox, select, radio 
   * textarea
   */
  //title
  $options_panel->Title(__("Exporter Options","apc"));
  //An optionl descrption paragraph
  $options_panel->addParagraph(__("This is a simple paragraph","apc"));
  //text field
  $options_panel->addText('automatic_exporter_folder', array('name'=> __('XML Export Folder','apc'), 'std'=> $upload_dir['basedir'] . "/automatic_exporter/", 'desc' => __('Add full path to folder if other than default. Default folder: <br><strong>'.$upload_dir['basedir'] . "/automatic_exporter/</strong>",'apc')));
  //text field
  $options_panel->addText('automatic_exporter_filename', array('name'=> __('XML Export Filename','apc'), 'std'=> "section-export.xml", 'desc' => __('Add filename. Default filename: <br><strong>section-export.xml</strong>','apc')));
  //select field
  $options_panel->addSelect('automatic_exporter_post_type_select',ae_return_post_types(),array('name'=> __('Select post type ','apc'), 'std'=> array('selectkey2'), 'desc' => __('Select the post type you want to build you xml file from.','apc')));
  //checkbox field
  $options_panel->addCheckbox('automatic_exporter_expanded_files',array('name'=> __('Full file/image details ','apc'), 'std' => true, 'desc' => __('Check this box to have full URLs to images and files','apc')));
  
  /**
   * Close first tab
   */   
  $options_panel->CloseTab();
}

	// adding the function to the Wordpress init
	add_action( 'wp_loaded', 'automatic_exporter_options_page');
?>