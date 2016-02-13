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

		$max= count($this->_folders);
		for($i=0; $i < $max; $i++){
			if(isset($this->_folders[$i]['FOLDER'])){
				$this->_folderNames[$this->_folders[$i]['FOLDER']]=1;
			}

		}
		$this->upload();
	}

	public function listFolder($PATH='')
	{
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
		if(is_file($path)){
			$path= dirname($path).'/';
		}
		$start= mb_strpos($path, $this->_base)+ mb_strlen($this->_base)-1;
		$foundPath= preg_replace('#^/#','',mb_substr($path, $start) );
		return $foundPath;
	}


	public function listFiles ($PATH='',$output=0)
	{
		$result= array();//define root
		$folderList=array();
		$PATH=($PATH=='')?$this->_path: $this->_basepath.$PATH;

		if(file_exists($PATH)){
			if ($handle = opendir($PATH) ) {
				while (false !== ($entry = readdir($handle))) {

					if ($entry != "." && $entry != ".."){
						/* READING FOLDER*/
						/*adding trailling slah if requiered*/
						$path_length= mb_strlen($PATH);
						$lastChar= mb_substr($PATH, $path_lenght-1);
						$PATH .=($lastChar !='/')?'/':'';

						$currentFile =$PATH.$entry;
						$currentFolder= $this->getFilePathPDF($currentFile);
						$folderList[]= $currentFolder;
						$ext_length= strlen($this->_ext);
						
						if($ext_length>= 2){
							//compare the ext in lower case
							if ( strtolower(substr($entry, -$ext_length) )== $this->_ext){
								switch($output){
									case 0:
											if( !is_file($currentFile)){//shouldn't be the case
											//echo "----NOt A FILE--- $output **";
										}	else {
											$result[]= array('PDF' =>$entry, 'PATH'=>$this->_base, 'FOLDER'=>$currentFolder);// return the result as a list [EXT][ FLE NAME]
										}
									break;
									case 1:
										if( !is_file($currentFile)){
											$result[]=   array ('PATH'=>$this->_base, 'FOLDER'=>$currentFolder);
										}else {
											/* necesseary for listing on the root? */
											$result[]=   array('PDF' =>$entry,  'PATH'=>$this->_base, 'FOLDER'=>$currentFolder);
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
												$result[]=  array(strtoupper($fileExt) =>$entry,  'PATH'=>$this->_base, 'FOLDER'=>$currentFolder);
											}
										} else {
											//we found a folder. assuming a sub folder
											$result[]=    array('PATH'=>$this->_base,'FOLDER'=>$currentFolder.'/','L221'=>0);//   'FOLDER'=>$currentFolder   'PDF' =>'', 
										}	
									break;
									case 1: // LOOKING　FOR FOLDER LIST
										if( !is_file($currentFile)){
											$result[]=   array('PATH'=>$this->_base, 'FOLDER'=>$currentFolder,'L227'=>0);
										}
									break;
									case 2: // file only
										if( is_file($currentFile)){
											$result[]= array(strtoupper($fileExt)=> $entry,'PATH'=> $currentFolder, 'L232'=>0);
										};
									break;
									case 99: //No filter
										if(is_file($currentFile)){
											$result[]=   array(strtoupper($fileExt) =>$entry,'PATH'=>$this->_base, 'FOLDER'=>$currentFolder);
										} else {
											$result[]=   array('PDF' =>'', 'PATH'=>$currentFolder.$entry, 'FOLDER'=>$entry);
										}	
									break;
								}
								//$folderList[]=$currentFolder.$entry;
							}
						} else {
							//echo "other case";
							//$result[]= $entry; 
						}
					}	
				}//end while
			closedir($handle);
			} else {
				//echo "---Can open Files!";
			}	
		} else {
			//echo "---no files exists!";//happends when listing the pdf for wp composer
		}

		$this->_folderNames= array_unique($folderList);

		return (count($result)>0)?$result: array ('PATH'=>$this->_base, 'FOLDER'=>'');//define root;
	}
	
	/* intend to be display
	SHOULD BE--OK 2015-12-01 */ 
	public function displayFilesList($folder='',$downloadoption=0)
	{
		if($folder!=''){
			$this->_path= $this->_path.$folder."/";
			$this->_base= $this->_base.$folder."/";
			$this->_folders=$this->listFolder();
		}

		$this->parseFolder();
		$projectName=strtolower(get_current_theme() );// $SASS_BASE."project/
		$base= dirname(__FILE__);
		$keyword="plugins";
		$base= substr($base,0 , strpos($base,$keyword));
		$destination= str_replace('/var/www/html/','', $base)."themes/$projectName/".$this->_path;
		
		$max=(isset($this->_folders[0]))? count($this->_folders):1;
		
		for($i=0; $i < $max; $i++){

			$currentFolderLevel= $this->_folders[$i]['PATH'].$this->_folders[$i]['FOLDER'];
			$PATH=(isset($this->_folders[$i]['PATH']))? $this->_folders[$i]['PATH']: $this->_folders['PATH'];
			$FOLDER= (isset($this->_folders[$i]['FOLDER']))? $this->_folders[$i]['FOLDER']: $this->_folders['FOLDER'];
			$sub= count($this->_files[	$currentFolderLevel] );

			if($sub>0){
				include(dirname(__FILE__). "/../template/files_list.php");	//to put inside a template
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
		//print_r($this->_folders);
		$tree=array();
		$tree[]= '';
		for($i=0; $i < $max; $i++){
			$baseFolder= (isset($this->_folders[$i]['PATH']))?$this->_folders[$i]['PATH']:$this->_folders[$i];
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
	//	print_r($this->_folders);// FOLDER LIST
		for($i=0; $i < $max; $i++){
			$ENTRY=$this->_folders[$i];
			if(is_array($ENTRY)){

				$PATH= (isset($ENTRY['PATH']))?$ENTRY['PATH']:$ENTRY;
				$FOLDER=(isset($ENTRY['FOLDER']))?$ENTRY['FOLDER']:'';
				$baseFolder= $PATH.$FOLDER;//list of directory dedicated to PDF
				
				echo "<h2>親フォルダー：$baseFolder</h2>";
				
				$currentFolder='';// reset?
				
				$current=$this->listFolder(	$baseFolder); 
				$maxSub= count($current);
				$currentPDFList= $this->listFiles(	$FOLDER) ;
				$sub= count($currentPDFList);
	
				//check list the file
				$LIST ='';
				if($maxSub>0){
					
					for($j=0; $j < $maxSub; $j++){
						$PDF=(isset($current[$j]['PDF']))?$current[$j]['PDF']:'';
						if($PDF!=''){//Files
							$pdfPath= $current[$j]['PATH'].$current[$j]['FOLDER'];
							$currentFolder=$current[$j]['FOLDER'];

							$isFolder=0;
							$EXT_KEY= explode('.',$PDF);
						//	print_r($EXT_KEY);
							$extPos=count($EXT_KEY)-1;
							$filename= $EXT_KEY[0];
							if( $EXT_KEY[$extPos]== strtoupper($this->_ext)){
								$link=  get_bloginfo('template_url').'/'.	$pdfPath.'/'.$filename.'.'.strtoupper($this->_ext);
							//	$filename= $EXT_KEY[0];
							} else {
								$link=  get_bloginfo('template_url').'/'.	$pdfPath.'/'.$filename.'.'.$EXT_KEY[$extPos];
							}

							if($isFolder==0){
								$LIST .=' 	<li>'.$filename.'<button type="submit" name="delete" value="'.  base64_encode ( 	$pdfPath ).'_'. base64_encode ( $filename ).'">ファイル削除</button></li>';
							}
						}
					}
					include(dirname(__FILE__). "/../template/files_mng.php");
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

	private function parseFolder($option=0)
	{
		$max= count($this->_folders);
		for($i=0; $i < $max; $i++){
			$PATH= (isset($this->_folders[$i]['PATH']))?$this->_folders[$i]['PATH']:$this->_folders[$i];
			$FOLDER=(isset($this->_folders[$i]['PATH']))?$this->_folders[$i]['FOLDER']:'';
			$FILES_LIST[$PATH.$FOLDER]= $this->listFiles($PATH.$FOLDER,$option);//.$this->_folders[$i]['PDF']
		}
		$this->_files=$FILES_LIST;
	}
/* doesnt return a folder?*/
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