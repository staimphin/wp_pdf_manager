<?php
global $gs_file_upload;
	global $option;
	global $post;
	$PDF= new FFM($gs_file_upload,'PDF', $option);
	echo '<style>
	#pdfLink{display:none;}
	.popup {width:400px;display:block;position:absolute; top:300px; margin:0 auto;}
	</style>';
	//show
	$selection= $PDF->getSelectList($post->post_name);
	//$PDF->uploadForm();
	//echo "***=====CUSTOM=========---";
	echo '<div class="popup">************pdf_dial.php';
	echo '<select id="pdfSelector">'.$selection.'</select>';
	echo '</div>';