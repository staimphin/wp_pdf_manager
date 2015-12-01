<?php 
/** * File type: template * file name:  files_list.php * * */
?>
				<h3><?= $this->_folders[$i]['FOLDER'];?></h3>	
					<ul class="PDFList">
<?php				
				for($j=0; $j < $sub; $j++){
			
					$link=  get_bloginfo('template_url').'/'.$this->_folders[$i]['PATH'].$this->_folders[$i]['FOLDER'].'/'.$this->_files[$currentFolderLevel][$j][strtoupper($this->_ext)];
					$tmp=  $this->_files[$currentFolderLevel][$j]['PDF'];//siwtched for PDF: strtoupper($this->_ext)

					$filename = explode('.',$tmp);
					if($tmp!=''){
?>
					<li><a href="<?= $link;?>" class="ico_<?= $filename[1];?>" target="blank"><?= $filename[0];?></a></li>
<?php			}
				}
?>
					</ul>