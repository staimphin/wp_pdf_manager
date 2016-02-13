<?php 
/** * File type: template * file name:  files_mng.php * * */
/*<button type="submit" name="delete" value="<?= $i;?>">全部削除</button>*///was button on the h3 title

?>
			<form method="post" action="#" enctype="multipart/form-data">
				<h3><?=$currentFolder;?></h3>	
					<ul>
<?= $LIST;?>
					</ul>
				</form>