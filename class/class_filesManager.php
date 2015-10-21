<?php //class pdf
/**
 * FileManager: V1.0
 * Upload and manage PDF
 *
 *
 */
 
class FFM{
	private $_ext;
	private $_base;
	private $_path;
	private $_folderNames;
	private $_folders;
	private $_files;
	private $_autorised= array(
		'PDF'=> 1,'TXT'=>1,
	);

	public function __construct($path,$ext='',$fullPath='')
	{
		$this->_ext= strtolower($ext);
		 if($fullPath==''){
			 $fullPath= dirname(__FILE__);
		 }

		$this->_path=$fullPath."/".$path;//
		$this->_base=$path;
		
		$this->_folders=$this->listFolder();
		//sort folder
		rsort($this->_folders);
		
		$max= count($this->_folders);
		for($i=0; $i < $max; $i++){
			$this->_folderNames[$this->_folders[$i]]=1;
		}

		// handle the upload in case
		$this->upload();
	}
	
/* to move out*/
	public function CSS()
	{
		echo "<style>
		.popup{
			display: block;
			position: absolute;
			top:0;
			background: rgba(0,0,0,0.7);
			width:100%;
			height:100%;
			text-align:center;
			color: #FFF;
		}
		
		.green {background-color: #0F0;color: #FFF;}
		.red {background-color: #F00;color: #FFF;}
		
		.popup div {
			display:block;
			position:relative;
			padding: 25%;
			width:45%;
		}
		
		.popup div 	button {
			margin: 10px;
			padding: 10px;
			}
		
		h2.alert {
			color: #F00;
			font-weight:800;
			line-height:1.8em;
			}
		h3 {			margin: 10px ;}
		h3,	form ul	li {
			width:640px;
		}

		.ico_pdf {}
		
		button {
			border: none;
			float:right;
			cursor: pointer;
			
		}
		
		form ul	li:hover {background: #EEE;}
		form ul	li {
			padding:15px;
			margin: 0 10px;
		}		
		form ul	li:nth-of-type(odd){
			background: #CCC;
		} 
		</style>";
	}

	public function listFolder($PATH='')
	{
		return $this->listFiles($PATH,$output=1);
	}
	
	public function listFiles ($PATH='',$output=0)
	{
		$result=array();
		if(file_exists($this->_path.$PATH)){
			if ($handle = opendir($this->_path.$PATH) ) {
			while (false !== ($entry = readdir($handle))) {
				if ($entry != "." && $entry != ".."){
					$currentFile =$this->_path.$PATH.'/'.$entry;
					$ext_length= strlen($this->_ext);
					if($ext_length>= 2){
						//compare the ext in lower case
						if ( strtolower(substr($entry, -$ext_length) )== $this->_ext){
							if($output==0){
								$result[]= array(strtoupper($this->_ext) =>$entry);// return the result as a list [EXT][ FLE NAME]
							}
						}else{// default list everything
						// in this case there's a file with an other extension
							$tmp= explode('.',$entry);
							$fileExt=strtoupper($tmp[1]);
							switch($output){
								case 0:  
									
									if(is_file($currentFile)){
										if($this->_autorised[$fileExt]){
											//echo "** $fileExt : is file : $currentFile<br>";
											$result[]=  array(strtoupper($fileExt) =>$entry);
										}
									} else {
										$result[]= $entry;
									}	
								break;
								case 1: // folder name only
									if( !is_file($currentFile)){$result[]= $entry;};
								break;
								case 2: // file only
									if( is_file($currentFile)){$result[strtoupper($fileExt)]= $entry;};
								break;
								case 99: //No filter
									if(is_file($currentFile)){
										$result[]=  array(strtoupper($fileExt) =>$entry);
									} else {
										$result[]= $entry;
									}	
								break;
							}
							//$result[]= $entry; 
						}
					}
				}	
			} 
			closedir($handle);
			sort($result);
			return $result;
			}
			else {
				 return false;
			}
		}
	}
	// intend to be display
	public function displayFilesList($folder='')
	{
		if($folder!=''){
			$this->_path= $this->_path.$folder."/";
			$this->_folders=$this->listFolder();
		}
		$this->parseFolder();
		$max= count($this->_folders);
		
		for($i=0; $i < $max; $i++){
			$sub= count($this->_files[$this->_folders[$i]] );
			if($sub>0){
			//to put inside a template
				include(dirname(__FILE__). "/../template/files_list.php");
			}
		}
		
	}
	
	/* ===== admin screen ============*/
	public function newDir($name)
	{
		$newfolder= $this->_path.$name;
		if(file_exists($newfolder)){
			echo "Folder exist (".$newfolder.")<br>";
		}else{
			echo "Project  (".$newfolder."): DOESN'T EXIST. CREATING.";
			mkdir($newfolder,0777);
		}
	}
	
	public function delFile($name, $path='')
	{
		$todel= $this->_path.$path.$name;
		if(file_exists($todel)){
			if(unlink($todel)){
				echo "$name を削除しました!";
			} else {
			 echo "Error!";
			}
		}else{
			echo "Project  (".$todel."): DOESN'T EXIST.";
		}
	}
	
	public function upload($UPLOAD_NAME='fileupload')
	{
		if(isset($_FILES[$UPLOAD_NAME])){
		$FILE=$_FILES[$UPLOAD_NAME];
			if ($FILE["error"] > 0) { 
				echo "Error  #" . $FILE["error"] . "<br>";
			}else{
				// check if its an autorised files
				$tmp= explode('.',$FILE['name']);
				$uploadedExt=strtoupper($tmp[1]);
				if (isset($this->_autorised[$uploadedExt])){
				//moves teh file to the requiered folder
					$folder=cleanString($_POST['destination'],'txt');
					if($_POST['newFolder']!=''){
						$folder .= '/'. cleanString($_POST['newFolder'],'txt');
						if(!isset($this->_folderNames[$folder])){
							$this->newDir($folder);
						}
					}
					$destFolder=$this->_path.$folder;
					echo "will upload to :$destFolder  || tmp name =".$FILE['tmp_name'];
					$filename = $FILE['name'];
					if(is_uploaded_file($FILE['tmp_name'])){
						if (move_uploaded_file($FILE['tmp_name'],$destFolder.'/'.$filename) == TRUE){
							$this->_folders=$this->listFolder();//update files list
						}else{
							echo "ERROR UPLOADING ".$filename."!";
						}
					}else {
						echo "File not uploaded!";
					}
				} else {
					echo "Non authorised file! Upload aborted.!"; 
				}
			}
		}
	}

	public function uploadForm()
	{
		$tree= $this-> getTree();
		$max= count($tree);
		include(dirname(__FILE__). "/../template/uploadForm.php");
	}

	public function getTree()
	{
		$max= count($this->_folders);
		$tree=array();
		for($i=0; $i < $max; $i++){
			$baseFolder= $this->_folders[$i];
			$current=$this->listFolder(	$baseFolder); 
			$tree[]=  $baseFolder.'/';
			$maxSub= count($current);
			for($j=0; $j < $maxSub; $j++){
				$tree[]=  $baseFolder.'/'.$current[$j];;
			}
		}
		return $tree;
	}

	public function manageFiles()
	{
		$this->deleteMng();
		$this->parseFolder();
		$max= count($this->_folders);
	
		for($i=0; $i < $max; $i++){
			$baseFolder= $this->_folders[$i];//list of directory dedicated to PDF
			echo "<h2>親フォルダー：$baseFolder</h2>";
			$current=$this->listFolder(	$baseFolder); 
			
			//is there any file in this root?
			// a bit WET!!!! Think about a more DRY way
			$currentFolder='';//$baseFolder;
			$currentPDFList= $this->listFiles(	$baseFolder) ;
			$sub= count($currentPDFList);
			if($sub>0 && isset($currentPDFList[0]['PDF'])){
				$pdfPath= $baseFolder;
				include(dirname(__FILE__). "/../template/files_mng.php");
			}
			//check for sub folders
			$maxSub= count($current);
			for($j=0; $j < $maxSub; $j++){
				$pdfPath= $baseFolder.'/'.$current[$j];
				$currentFolder=$current[$j];
				$currentPDFList= $this->listFiles(	$pdfPath) ;
				$sub= count($currentPDFList);
				if($sub>0){include(dirname(__FILE__). "/../template/files_mng.php");}
			}
		}
		
	}
	private function deleteMng()
	{
		if(isset($_POST['delete'])){
			$data= explode('_',$_POST['delete']);
			$folder=base64_decode ( $data[0]);
			$file=base64_decode ( $data[1]);
			//confirmation screen
			if(isset($_POST['deleteOK'])){
		 		//echo " $file を削除します";
		 		$this->delFile($file, $folder.'/');
			} else {
				include(dirname(__FILE__). "/../template/files_delete_chk.php");
			}
		} 
	}
	// intend to be private
	private function parseFolder($option=0)
	{
		$max= count($this->_folders);
		for($i=0; $i < $max; $i++){
			$FILES_LIST[$this->_folders[$i]]= $this->listFiles($this->_folders[$i],$option);
		}
		$this->_files=$FILES_LIST;
	}

	public function getSelectList($folder='',$return=1)
	{
		if($folder!=''){
			$this->_path= $this->_path.urldecode($folder)."/";
			$this->_folders=$this->listFolder();
		}
		$TMP='';
		$list=$this->listFiles();
		$max = count(	$list);

		if(isset($list[0]['PDF'])){
			for($i=0; $i < $max; $i++){
				$TMP.=($return==1)?	"'".$list[$i]['PDF']."',":	'<option value="'.$this->_path.$list[$i]['PDF'].'">'.$list[$i]['PDF'].'</option>';
			}
		}

		return $TMP;
	}
	
}