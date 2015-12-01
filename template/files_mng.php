<?php 
/** * File type: template * file name:  files_mng.php * * */
/*<button type="submit" name="delete" value="<?= $i;?>">全部削除</button>*///was button on the h3 title

?>
			<form method="post" action="#" enctype="multipart/form-data">
				<h3><?=$currentFolder;?></h3>	
					<ul>
<?php				//print_R($currentPDFList[$k] );
				for($k=0; $k< $sub; $k++){
				$isFolder=0;
					$EXT_KEY= array_keys( $currentPDFList[$k] );
					//print_r($EXT_KEY);
					//echo "*************LMNG DB: ".$EXT_KEY[0]." <br>\r\n";
					if( $EXT_KEY[0]== strtoupper($this->_ext)){
						$link=  get_bloginfo('template_url').'/'.	$pdfPath.'/'.$currentPDFList[$k][strtoupper($this->_ext)];
						$filename=  $currentPDFList[$k][strtoupper($this->_ext)];
					} else { //other extension
						// assume its a folder don't show
						$isFolder=1;
						$link=  get_bloginfo('template_url').'/'.	$pdfPath.'/'.$currentPDFList[$k][$EXT_KEY[0]];
						$filename=   $currentPDFList[$k][$EXT_KEY[0]];
					}
					/* show only if we have a file inside*/
					if($isFolder==0){
?>
					<li><?= $filename;?><button type="submit" name="delete" value="<?=  base64_encode ( 	$pdfPath ).'_'. base64_encode ( $filename );?>">ファイル削除</button></li>
<?php			}
				}
?>
					</ul>
				</form>