<?php 
/** * File type: template * file name:  files_mng.php * * */
/*<button type="submit" name="delete" value="<?= $i;?>">全部削除</button>*///was button on the h3 title
?>
			<form method="post" action="#" enctype="multipart/form-data">
				<h3><?=$currentFolder;?></h3>	
					<ul>
<?php				
				for($k=0; $k< $sub; $k++){
					$EXT_KEY= array_keys( $currentPDFList[$k] );
					if( $EXT_KEY[0]== strtoupper($this->_ext)){
						$link=  get_bloginfo('template_url').'/'.$this->_base.	$pdfPath.'/'.$currentPDFList[$k][strtoupper($this->_ext)];
						$filename=  $currentPDFList[$k][strtoupper($this->_ext)];
					} else {
						$link=  get_bloginfo('template_url').'/'.$this->_base.	$pdfPath.'/'.$currentPDFList[$k][$EXT_KEY[0]];
						$filename=   $currentPDFList[$k][$EXT_KEY[0]];
					}
?>
					<li><?= $filename;?><button type="submit" name="delete" value="<?=  base64_encode ( 	$pdfPath ).'_'. base64_encode ( $filename );?>">ファイル削除</button></li>
<?php			
				}
?>
					</ul>
				</form>