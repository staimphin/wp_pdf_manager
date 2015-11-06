<?php 
/** * File type: template * file name:  files_list.php * * */

?>
				<h3><?= $this->_folders[$i];?></h3>	
					<ul class="PDFList">
<?php				
				for($j=0; $j < $sub; $j++){
					$link=  get_bloginfo('template_url').'/'.$this->_base.$this->_folders[$i].'/'.$this->_files[$this->_folders[$i]][$j][strtoupper($this->_ext)];
					$tmp=  $this->_files[$this->_folders[$i]][$j][strtoupper($this->_ext)];
					$filename = explode('.',$tmp);
?>
					<li><a href="<?= $link;?>" class="ico_<?= $filename[1];?>" target="blank"><?= $filename[0];?></a></li>
<?php			
				}
?>
					</ul>