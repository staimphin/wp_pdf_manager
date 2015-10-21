<?php 
/**
 * File type: template
 * file name:  uploadForm.php
 *
 *
 */
//<input type="submit" name="addFolder">
//カテゴリー ==>PDFのサブフォルダー
echo $path;
?>
					<form method="post" action="#" enctype="multipart/form-data">
						<ul>
						
						<li>アップロードフォルダー<select name="destination">
<?php 
	for($i=0; $i < $max; $i++){
?>
							<option value="<?= $tree[$i];?>"><?= $tree[$i];?></option>
<?php 
}
?>
						</select></li>
						<li>フォルダー追加<input type="text" name="newFolder"></li>
						<li><input type="file" name="fileupload"></li>
						<li><button type="submit" name="upload">アップロード</button></li>
					</form>