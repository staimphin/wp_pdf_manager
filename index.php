<?php 
 /*
Plugin Name:  PDF upload manger 
Plugin URI: 
Description:PDF management for Wordpress
Version: 0.2
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
?>
<?php 

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
    $plugin_array['pdflink'] = plugins_url().'/gs_PDF_manager/js/pdf.js' ;
    return $plugin_array;
}

function pdf_register_buttons( $buttons ) 
{
    array_push( $buttons, 'pdflink' ); 
    return $buttons;
}

// insert value to head
/* To move out*/
function pdf_js_data()
{
	global $post;
	$list=  pdfFormLite(1);
	?>
	<script type="text/javascript">
		var pdfFolder= '<?= urldecode($post->post_name);?>';
		var foundPDF = [<?= $list;?>];
	</script>
	<?php
	// pdfFormLite();
}
//replace the tag by URL
function pdf2link($atts) 
{
	$slug=urldecode( get_post_field('post_name',get_the_iD(),'raw'));
	$pType=get_post_type();
	$PDF_path=($pType=='page' || $pType=='post')?$slug:$pType.'/'.$slug;
	$URL=  get_bloginfo('template_url')."/PDF/$PDF_path/" ;
	return $URL;
}

/** * *		PDF UPLOAD AND MANAGEMENT * * */
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
	$PDF_path=($pType=='page' || $pType=='post')?$gs_file_upload:$gs_file_upload.$pType.'/';
	$PDF= new FFM($PDF_path,'PDF', $option);
	$selection= ($return==1)?$PDF->getSelectList($post->post_name,$return):$PDF->getSelectList($post->post_name);
	if($return==1) return $selection;
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