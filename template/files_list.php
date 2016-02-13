<?php 
/** * File type: template * file name:  files_list.php * * */
 $title= ($FOLDER!='')? '<h3>'.$FOLDER.'</h3>':'' ;
 echo $title; 
// print_r($this->_folders);
 //print_r($_SERVER);

?>
					<ul class="PDFList">
<?php				
			for($j=0; $j < $sub; $j++){
				if( isset($this->_files[$currentFolderLevel][$j] ) ){
				
					//$link=  get_bloginfo('template_url').'/'.$this->_folders[$i]['PATH'].$this->_folders[$i]['FOLDER'].'/'.$this->_files[$currentFolderLevel][$j][strtoupper($this->_ext)];
					$link=  get_bloginfo('template_url').'/'.$PATH.$FOLDER.$this->_files[$currentFolderLevel][$j][strtoupper($this->_ext)];
					$tmp=  $this->_files[$currentFolderLevel][$j]['PDF'];//siwtched for PDF: strtoupper($this->_ext)
					$filename = explode('.',$tmp);
					
					$thumbnail=(file_exists( $destination.'tmb_'.$filename[0].".jpg") )? '<img src="'.  $destination.'tmb_'.$filename[0].'.jpg" alt="'.$filename[0] .'">' :'';	
					echo "****". $destination.'tmb_'.$filename[0].".jpg".'<img src="'.  $destination.'tmb_'.$filename[0].'.jpg" alt="'.$filename[0] .'">'; //dirname();
				//	exec('gs -dSAFER -dBATCH -sDEVICE=jpeg -dTextAlphaBits=4 -dGraphicsAlphaBits=4 -r300 -sOutputFile=whatever.jpg input.pdf');
					$DOWNLOAD=($downloadoption==1)? ' download="'.$filename[0] .'"':'';
					if($tmp!=''){
?>
					<li><a href="<?= $link;?>" class="ico_<?= $filename[1];?>" target="blank"<?= $DOWNLOAD?;>><?= $thumbnail. $filename[0];?></a></li>
<?php			}
				}
			}
?>
					</ul>