<?php 
/**
 * File type: template
 * file name:  files_delete_chk.php
 *
 *
 */

?>
		<div class="popup">
			<div>
			<h2 class="alert">ご注意ください：<br><?= $folder;?>/<?= $file;?>を消します。</h2>
			<p><?= $folder;?>/<?= $file;?>を本当に削除してもよろしいでしょうか？</p>
			<form method="post" action="#" enctype="multipart/form-data">
				<button type="submit" name="deleteOK" value="cancel" class="green">戻る</button>
			</form>
			<form method="post" action="#" enctype="multipart/form-data">
				<button type="submit" name="deleteOK" value="ok" class="red"><?= $file;?>を削除する</button>
					<input type="hidden" name="delete" value="<?= $_POST['delete'];?>">
			</form>
			</div>
	</div>
