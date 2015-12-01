<?php 
 /*
Plugin Name:  PDF upload manger 
Plugin URI: 
Description:PDF management for Wordpress
Version: 0.3
Author: Gregory Staimphin
Author email: gregory.staimphin@free.fr
Author URI: 
License: GPLv2
*/
/*  Copyright 2015  STAIMPHIN GREGORY  (email : g_staimphin@sogo-printing.com)

    This program is free software; you can redistribute it and/or modify
    it under the terms of the GNU General Public License, version 2, as 
    published by the Free Software Foundation.

    This program is distributed in the hope that it will be useful,
    but WITHOUT ANY WARRANTY; without even the implied warranty of
    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
    GNU General Public License for more details.

    You should have received a copy of the GNU General Public License
    along with this program; if not, write to the Free Software
    Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA
*/
require "class/class_filesManager.php";

add_action( 'admin_menu', 'GS_PDF_admin_menu' );
add_action( 'admin_head', 'pdf_buttons' );
//call the value maker JS
add_action('admin_head', 'pdf_js_data');
add_action( 'wp_ajax_pdf_insert_dialog', 'pdf_insert_dialog' );

$meta_box = array(
    'id' => 'PDF manager',
    'title' => 'PDF ManagementSystem',
    'page' => 'page',
    'context' => 'advanced',
    'priority' => 'high',
);	

global $gs_file_upload; // base directory for PDF
global $option; // base directory for theme
$option=getCurrentThemePath();	
// base url / theme 

//move into class!
// check if folder exist
if(file_exists($option."/PDF")){
	$gs_file_upload="PDF/";
} else if (file_exists($option."/pdf")){
	$gs_file_upload="pdf/";
} else {
//create folder 
	//echo "** make dir :$option/PDF ";
	if(mkdir($option."/PDF")){
		$gs_file_upload="PDF/";
	} else {
		echo "ERROR!";
	}
}
/** *	plugin specific * */
/* function: directory path */
function getBaseFolder($path)
{
	$tmp =explode('/',$path);
	$dirNum = count($tmp)-1;
	return $tmp[$dirNum];
}

function cleanString($string, $type='')
{
	return $string;
}
	
function getCurrentThemePath()
{
	$path_parts = pathinfo(__FILE__);
	$thisDir = getBaseFolder($path_parts['dirname']);
	$themeDir=get_template();
	//replace the folder
	$path_parts['dirname']=str_replace('plugins/'.$thisDir,'themes/'. $themeDir, $path_parts['dirname']);
	return $path_parts['dirname'];
}
/** * *  PDF: POST EDITOR OPTIONS * * */
/*==== plugins edition options ======*/
function pdf_insert_dialog()
{
	global $gs_file_upload;
	global $option;
	global $post;
	$path=$gs_file_upload.'/'.$post->post_name;
	$PDF= new FFM($path,'PDF', $option);
	$selection= $PDF->getSelectList('',1);
	$tree= $PDF-> getTree();
	$max= count($tree);
	echo 	$selection;
	die(include dirname(__FILE__).'/template/uploadForm.php');
}

// add PDF convertion
add_shortcode('pdf_url', 'pdf2link');/*  dont work*/

function pdf_buttons() 
{
    add_filter( "mce_external_plugins", "pdf_add_buttons" );
    add_filter( 'mce_buttons', 'pdf_register_buttons' );
}

function pdf_add_buttons( $plugin_array ) 
{
	$thisDir = getBaseFolder(__DIR__);
    $plugin_array['pdflink'] = plugins_url().'/'.$thisDir.'/js/pdf.js' ;
    return $plugin_array;
}

function pdf_register_buttons( $buttons ) 
{
    array_push( $buttons, 'pdflink' ); 
    return $buttons;
}

// insert value to head
/* To move out
* used in wp post editor
*/
function pdf_js_data()
{
	global $post;
	$pdfData= pdfFormLite(1);
	$list= $pdfData['PDF'];
	$urls= $pdfData['URL'];
	?>
	<script type="text/javascript">
		var pdfFolder= '<?= urldecode($post->post_name);?>';
		var foundPDF = [<?= $list;?>];
		var foundUrlPDF = [<?= $urls;?>];
	</script>
	<?php
	// pdfFormLite();
}

/*replace the tag by URL
* pdf base should be variable
* pdf path?
* used in ??
*/
function pdf2link($atts) 
{
	$slug=urldecode( get_post_field('post_name',get_the_iD(),'raw'));
	//$pType=get_post_type();
	//$PDF_path=($pType=='page' || $pType=='post')?$slug:$pType.'/'.$slug;
	//$URL=  get_bloginfo('template_url')."/PDF/$PDF_path/" ;
	$URL=  get_bloginfo('template_url')."/$gs_file_upload" ;//gs_file_upload=> pdf/ OR PDF/
	//check where is the file:
	//if(file_exists())
	print_r($atts);
	return $URL;
}

/** * *		PDF UPLOAD AND MANAGEMENT * */
/*======= plugins functions ==============*/
function GS_PDF_admin_menu()
{
	global $meta_box;
	add_meta_box( 'pdfLink', 'PDF選択','pdfFormLite','page');
	add_menu_page('PDF管理', 'PDF一覧', 'manage_options', 'pdf-management.php', 'gs_pdf_page' , plugin_dir_url( __FILE__ ) . '','13.14');		
}

function pdfFormLite($return=0){
	global $gs_file_upload;
	global $option;
	global $post;
	
	$pType=$post->post_type;
	$pName=$post->post_name;
	$PDF_path= $gs_file_upload.$pType.'/';

	//echo "*INDEX*--Post TYPE=-$pType--PDF PATH-$PDF_path--|| UPLOAD path:$gs_file_upload---\r\n<br>";
	/* retrieve files infos from root and folder*/
	
	$PDF_ROOT= new FFM($gs_file_upload,'PDF', $option);
	$ROOT_LIST= $PDF_ROOT->getSelectList('',$return);
	//echo "*INDEX* root list: <br >\r\n";
	//print_r($ROOT_LIST);	
	
/*
	$PDF= new FFM($PDF_path,'PDF', $option);
	$selection= $PDF->getSelectList('',$return,1);
	*/
	//echo "*INDEX* post type list:$pType <br >\r\n";
	$postNameList= $PDF_ROOT->getSelectList($pType,$return);
	//	print_r($postNameList);
	//echo "*INDEX* TYPE/name :$pType.'/'.$pName <br >\r\n";
	$selection= $PDF_ROOT->getSelectList($pType.'/'.$pName,$return);

	//print_r($selection);
	$urls="'test'";
	if($return==1) return array(
		'PDF'=>$selection['PDF'].$postNameList['PDF'].$ROOT_LIST['PDF'],
		'URL'=>$selection['PATH'].$selection['FOLDER'].$postNameList['PATH'].$postNameList['FOLDER'].$ROOT_LIST['PATH'].$ROOT_LIST['FOLDER']);
}

function gs_pdf_page()
{
	global $gs_file_upload;
	global $option;

	$PDF= new FFM($gs_file_upload,'PDF', $option);
	//show
	$PDF->css();
	$PDF->uploadForm();
	//Show the list in edit mode
	$PDF-> manageFiles();
}