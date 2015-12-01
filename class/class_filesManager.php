<?php //class pdf
/**
 * FileManager: V1.1
 * Upload and manage PDF
 *
 *
 */
 
class FFM{
	private $_ext;
	private $_root;
	private $_basepath;
	private $_base;
	private $_path;
	private $_folderNames= array();
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

		
		$this->_path=$fullPath."/".$path;//current path
		$this->_basepath=$fullPath."/";//current path
		$this->_root=explode('/',$path);//root folder
		$this->_base=$path;

		$this->_folders=$this->listFolder();
		//$this->listFolder();
		//echo "=======*** folders  **==============\r\n<br>";
		//print_r($this->_folders);
		//sort folder
		
		rsort($this->_folders);//useless
		
		
	//	print_r($this->_folderNames);
		
		$max= count($this->_folders);
		for($i=0; $i < $max; $i++){
		//echo "looking for:".$this->_folders[$i]['PATH']." || fodler list \r\n<br>";
		//print_r($this->getBaseFolder($this->_folders[$i]['PATH']));
			if(isset($this->_folders[$i]['FOLDER'])){
				$this->_folderNames[$this->_folders[$i]['FOLDER']]=1;
			}

		}
		//echo "folders name list\r\n<br>";
		//print_r($this->_folderNames);
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
		//chk specified folder or parents	
//print_r($this->_root);		
		//if($PATH=''){$PATH= $this->_root[0];}
		return $this->listFiles($PATH,$output=1);
	}

	public function getBaseFolder($path)
	{
		$tmp =explode('/',$path);
		$step=(is_file($path))?2:1;
		$dirNum = count($tmp)-$step;
		return $tmp[$dirNum];
	}
/* useless?*/
	public function getFilePathPDF($path)
	{
		//echo "<br>**PATH/ current file=: $path <br>\r\n";
		//echo "**dbg :root: $this->_root ||base: $this->_base ||path: $this->_path ||<br>\r\n recevie path: $path<br>\r\n";
		if(is_file($path)){
			$path= dirname($path).'/';
		}
		$start= mb_strpos($path, $this->_base)+ mb_strlen($this->_base)-1;
		$foundPath= preg_replace('#^/#','',mb_substr($path, $start) );

		//echo "<br>**POS: $start** IN　$path<br>\r\nFOUND path:". $foundPath."<br>\r\n<br>\r\n<br>\r\n<br>\r\n<br>\r\n";;
		return $foundPath;
	}


	public function listFiles ($PATH='',$output=0)
	{
		//echo "-BASE PATH $this->_basepath --<br>\r\nREQUESTED PATH*-> $PATH ----<br>\r\n";
		$folderList=array();
		if($PATH==''){
			$PATH=$this->_path;
		} else {$PATH=$this->_basepath.$PATH;}
		$result= array ();
		$result= array ('PATH'=>$this->_base, 'FOLDER'=>'');
		//echo "COMPUTED this -PATH*->  $this->_path ----<br>\r\n";
		//echo "COMPUTED-PATH*-> $PATH ----<br>\r\n";
		if(file_exists($PATH)){
			if ($handle = opendir($PATH) ) {
				
				
				while (false !== ($entry = readdir($handle))) {
					if ($entry != "." && $entry != ".."){
						/* READING FOLDER*/
						/*adding trailling slah if requiered*/
							$path_length= mb_strlen($PATH);
							$lastChar= mb_substr($PATH, $path_lenght-1);
							if($lastChar !='/'){
								$PATH .='/';
							}
						//echo "----HERE ($PATH) -SOMETHING ($entry) CASE--$output-\r\n";
						$currentFile =$PATH.$entry;
						$currentFolder= $this->getFilePathPDF($currentFile);
						//$currentFolder= $this->_base;
						$ext_length= strlen($this->_ext);
						if($ext_length>= 2){
							//compare the ext in lower case
							if ( strtolower(substr($entry, -$ext_length) )== $this->_ext){
									//echo "--PDFFOUND--$output-|| ext len=> $ext_length VS ".$this->_ext."| test: ". strtolower(substr($entry, -$ext_length) )."｜ $entry | $currentFolder \r\n";
									switch($output){
										case 0:
												if( !is_file($currentFile)){//shouldn't be the case
											//	$result[]= array( 'PATH'=>$currentFolder, 'FOLDER'=>$entry);
											}	else {
												$result[]= array('PDF' =>$entry, 'PATH'=>$this->_base, 'FOLDER'=>$currentFolder);// return the result as a list [EXT][ FLE NAME]
											}
										
										break;
										case 1:
											if( !is_file($currentFile)){
												$result[]=   array ('PATH'=>$this->_base, 'FOLDER'=>$currentFolder);
											}else {
												/* necesseary for listing on the root? */
											//	$result[]=   array('PDF' =>$entry,  'PATH'=>$this->_base, 'FOLDER'=>$currentFolder);
											}// break;
										break;
										case 2:	
											if( is_file($currentFile)){
												$result[]=   array('PDF' =>$entry,  'PATH'=>$this->_base, 'FOLDER'=>$currentFolder);
											}// break;
										default: break;
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
												$result[]=  array(strtoupper($fileExt) =>$entry,  'PATH'=>$this->_base, 'FOLDER'=>$currentFolder);
											}
										} else {
											//we found a folder. assuming a sub folder
											//echo "----LOOKING FOR THE FILECASE　A -add sub folder -$output-\r\n 'PATH'=>".$this->_base." 'FOLDER'=>".$currentFolder."\r\n";
											$result[]=    array('PATH'=>$this->_base,'FOLDER'=>$currentFolder.'/','L221'=>0);//   'FOLDER'=>$currentFolder   'PDF' =>'', 
										}	
									break;
									case 1: // fLOOKING　FOR FOLDER LIST
										if( !is_file($currentFile)){
											//echo "----LOOKING FOR FOLDR LIST--$output-\r\n";
											$result[]=   array('PATH'=>$this->_base, 'FOLDER'=>$currentFolder,'L227'=>0);
										}
									break;
									case 2: // file only
										if( is_file($currentFile)){
											$result[]= array(strtoupper($fileExt)=> $entry,'PATH'=> $currentFolder, 'L232'=>0);
										};
									break;
									case 99: //No filter
									//echo "----NO FILTERS-$output-\r\n";
										if(is_file($currentFile)){
											$result[]=   array(strtoupper($fileExt) =>$entry,'PATH'=>$this->_base, 'FOLDER'=>$currentFolder);
										} else {
											//echo "----LOOKING FOR THE FILECASE C--$output-\r\n";
											$result[]=   array('PDF' =>'', 'PATH'=>$currentFolder.$entry, 'FOLDER'=>$entry);
										}	
									break;
								}
								//$folderList[]=$currentFolder.$entry;
							}
						} else {
							echo "other case";
							//$result[]= $entry; 
						}
					}	
				}//end while
			closedir($handle);
			sort($result);
			} else {echo "---Can open Files!";}	
		} else {
			//echo "---no files exists!";//happends when listing the pdf for wp composer
		}
		//echo "*** function resuslt ****\r\n<br>";
		//print_r( $result );
		//$this->_folderNames= array_unique($folderList);

		return $result;
	}
	
	/* intend to be display
	SHOULD BE--OK 2015-12-01 */ 
	public function displayFilesList($folder='')
	{
		if($folder!=''){
			//echo "---CUSTOM PATH: $folder --------------";
			$this->_path= $this->_path.$folder."/";
			$this->_base= $this->_base.$folder."/";
			$this->_folders=$this->listFolder();
		}
		//echo "[DISPLAY FUNCTION]***".$this->_path." **";
		$this->parseFolder();
		//print_R($this->_files);
		$max= count($this->_folders);
		
		for($i=0; $i < $max; $i++){
			$currentFolderLevel= $this->_folders[$i]['PATH'].$this->_folders[$i]['FOLDER'];
			$sub= count($this->_files[	$currentFolderLevel] );
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
			//echo "Folder exist (".$newfolder.")<br>";
		}else{
			//echo "Project  (".$newfolder."): DOESN'T EXIST. CREATING.";
			mkdir($newfolder,0777);
		}
	}
	
	public function delFile($name, $path='')
	{
		$path=($path=='')?$this->_path: $path;
		$todel=$this->_basepath. $path.$name;
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
					$destFolder=$this->_basepath.$folder;
					//echo "will upload to :$destFolder  || tmp name =".$FILE['tmp_name'];
					$filename = $FILE['name'];
					if(is_uploaded_file($FILE['tmp_name'])){
						if (move_uploaded_file($FILE['tmp_name'],$destFolder.'/'.$filename) == TRUE){
							$this->_folders=$this->listFolder();//update files list
						}else{
							echo "ERROR UPLOADING ".$filename."!";
						}
					}else {
						echo "File not uploaded?";
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

	/* TO CHECK */
	public function getTree()
	{
		$max= count($this->_folders);
		$tree=array();
		$tree[]= '';
		for($i=0; $i < $max; $i++){
			$baseFolder= $this->_folders[$i]['PATH'];
			$current=$this->listFolder(	$baseFolder); 
			$tree[]=  $baseFolder;
			$maxSub= count($current);
			for($j=0; $j < $maxSub; $j++){
				$tree[]=  $baseFolder.$current[$j]['FOLDER'];;
			}
		}
		
		return array_unique($tree);
	}
	/* IN PROGRESS*/
	public function manageFiles()
	{
		$this->deleteMng();
		$this->parseFolder();
		$this->_folders[]=  array ('PATH'=>$this->_base, 'FOLDER'=>'', 'ROOT'=>1);// add root
		$max= count($this->_folders);
		//echo "----------------------------------------------------DEBUG ADMIN=============================<br>\r\n";
		//print_r($this->_folders);
		for($i=0; $i < $max; $i++){
			$baseFolder= $this->_folders[$i]['PATH'].$this->_folders[$i]['FOLDER'];//list of directory dedicated to PDF
			echo "<h2>親フォルダー：$baseFolder</h2>";
			$current=$this->listFolder(	$baseFolder); 
			
			//is there any file in this root?
			// a bit WET!!!! Think about a more DRY way
			$currentFolder='';//$baseFolder;
			$currentPDFList= $this->listFiles(	$baseFolder) ;
		//	echo "------PDF LIST FOR THIS FOLDER --------------- ";
		//	print_r($currentPDFList);
		//	echo "CURRENT SUBFOLDER LIST FOR THIS FOLDER --------------- ";
		//	print_r($current);
			$sub= count($currentPDFList);
			/* ==================
			
				root folder doesnt lists
			
			========================*/
		//	if( )){
				//print_R($current);
				//echo "base folder";
				//print_R($baseFolder);
				$pdfPath=$this->_folders[$i]['PATH'].$this->_folders[$i]['FOLDER'];
				include(dirname(__FILE__). "/../template/files_mng.php");
			//} else {
			if(!isset($this->_folders[$i]['ROOT'])) {
			//check for sub folders
				$maxSub= count($current);
				//echo "===FOUND $maxSub SUBFOLDER  FOR THIS FOLDER --------------- ";
				if($maxSub>0){
					for($j=0; $j < $maxSub; $j++){
						$pdfPath= $current[$j]['PATH'].$current[$j]['FOLDER'];
						$currentFolder=$current[$j]['FOLDER'];
						//echo ">>>>>>>>>>>>>CURRENT PDF PATH: $pdfPath --------------- ";
						
						$currentPDFList= $this->listFiles(	$pdfPath) ;
						//			echo "------PDF LIST FOR THISSUB FOLDER --------------- ";
					//print_r($currentPDFList);
						$sub= count($currentPDFList);
						//echo ">>>>>>>>>>>>-------SUBNAME: $currentFolder---Sub Found: $sub----- ";
						if($sub>0){include(dirname(__FILE__). "/../template/files_mng.php");}
					}
				}else{
					//$currentFolder=$current[$j]['FOLDER'];
				//	if($sub>0){include(dirname(__FILE__). "/../template/files_mng.php");}
				}
				
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
		//echo "<br>\r\n***PARSE FOLDER <br>\r\n";
		//print_r($this->_folders);
		//echo "<br>\r\n   LIST FILES START HERE:<br>\r\n";
		//echo "<br>\r\n";
		$max= count($this->_folders);
		for($i=0; $i < $max; $i++){
		//echo "($i)LOOKING　FOR FILES IN:".$this->_folders[$i]['PATH'].$this->_folders[$i]['FOLDER']."<br>\r\n";
			$FILES_LIST[$this->_folders[$i]['PATH'].$this->_folders[$i]['FOLDER']]= $this->listFiles($this->_folders[$i]['PATH'].$this->_folders[$i]['FOLDER'],$option);//.$this->_folders[$i]['PDF']
		}
		//print_R($FILES_LIST);
		$this->_files=$FILES_LIST;
	}
/* doesnt reurn a folder*/
	public function getSelectList($folder='',$return=1,$debug=0)
	{
		if($folder!=''){
			$this->_path= $this->_path.urldecode($folder)."/";
			$this->_folders=$this->listFolder();
		}

		$list=$this->listFiles();
		if($debug==1){
			echo "DBG: folder: $folder-|| Path=".$this->_path;
			$TMP=array('PDF'=>'','PATH'=>'',);
			echo "---gets elected:-";
			print_r($list);
			echo "---fodlers listd:-";
			print_r($this->_folders);
		}
		
		$max = count(	$list);

		for($i=0; $i < $max; $i++){
			if(isset($list[$i]['PDF'])){
				if($list[$i]['PDF']!=''){
								if($return==1){
						$TMP['PDF'].= "'".$list[$i]['PDF']."',";
						$TMP['PATH'].= "'".$list[$i]['PATH'].$list[$i]['FOLDER']."',";
					} else {
					$TMP['PDF'].=	'<option value="'.$this->_path.$list[$i]['PATH'].$this->_path.$list[$i]['PDF'].'">'.$list[$i]['PDF'].'</option>';
					}
				}		
			}
		}

		return $TMP;
	}
	
}