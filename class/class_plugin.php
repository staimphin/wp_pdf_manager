<?php 
/** * Plugins : * class_plugins * Register the parameters according to wordpress * */
 
 class gs_plugin {	public function __construct($name)	{
		add_action( 'admin_menu', 'admin_menu' );	}	public function addCustomHeader() 
	{
		}	private function test()	{			}}
 
 
?>