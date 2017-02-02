<?php
class ServiceVideoController extends ApiController {

    // Set layout for controller
    public $layout = '';
    //public $limitVideoDuration =  0;
    public $limitVideoSize = 102400000;
    
    public function actionUpload()
    {
		
        try
        {   $idSession = Yii::app()->request->getPost('idSession');
            $idCommentEdit = Yii::app()->request->getPost('idCommentEdit');
            $folderPath = Yii::app()->params['fileVideoFolderPath'];            
            $videoTemp = $this->saveFileToDisk($folderPath);
			//var_dump($_POST);var_dump($_FILES);var_dump($videoTemp);exit;
            if ($videoTemp['status']==true && $videoTemp['codeStatus']==4) { 
                if((int)$idCommentEdit === 0){                    
                    //create Comment Temp
                    $commentNew = new Comments();
                    //$commentNew->idUserComment = Yii::app()->user->idUser;
                    $commentNew->idUserComment = Yii::app()->request->getPost('idUserComment');
                    $commentNew->idSession = $idSession;
                    $commentNew->contentMediaType = "VIDEO";
                    $commentNew->dateCreate = date("Y-m-d H:i:s");
                    $commentNew->lastUpdate = date("Y-m-d H:i:s");
                    $commentNew->contentMediaPathTemp = "";
                    $commentNew->contentMediaPath = basename($videoTemp['path']);
                    $commentNew->isTemp = 0;
                    $commentNew->save(); 
                    $typeStatus = 'add';
                    $idComment=$commentNew->idComment;
                    $idCommentParent = $commentNew->idCommentParent;
                }else{
                    //update comment with fileVideoFolderPathNew  
                    $comment=Comments::model()->findByPk($idCommentEdit);
                    $comment->contentMediaPathTemp = basename($videoTemp['path']);                      
                    $comment->lastUpdate = date("Y-m-d H:i:s");
                    $comment->save(); 
                    $typeStatus = 'edit';
                    $idComment=$comment->idComment;
                    $idCommentParent = $comment->idCommentParent;
                } 
                $dataIdUser = InvitedSession::model()->getIdUserCreateAndInvitedUser($idSession);
                $arrayUserInvited =  $this->arrayColumn($dataIdUser, 'idUser');
                $dataReturn=array(
                    'status'=>'true',
                    'typeStatus'=>$typeStatus,
                    'idComment'=>$idComment,
                    'idSession'=>$idSession,
                    'idCommentParent'=>(int)$idCommentParent,
                    'sessionInvited'=>$arrayUserInvited
                );
                ApiHelper::sendResponse(200, '',$dataReturn);
            }else{
                ApiHelper::sendResponse(500, '', 'Error');
            }
        }catch(Exception $ex){            
            ApiHelper::sendResponse(500, '', 'Error'.$ex);
        }
    }
    
    public function saveFileToDisk($fileVideoTempFolderPath = "") {
        $status=false;
        $path='';
        $pathURL='';
        $codeStatus=0;
        $messager='Cannot upload file.';
        $type = "";
        
        $fileName=  Yii::app()->request->getPost('fileName');
        $fileExt=  strtolower(Yii::app()->request->getPost('fileExt'));
        $fileContent=  Yii::app()->request->getPost('fileContent');
        $fileType=  Yii::app()->request->getPost('fileType');
        $fileSize=  Yii::app()->request->getPost('fileSize');
        
        $fileUploadTemp ="";        
        $fileRaw = Yii::app()->request->getPost('fileRaw');
        if(isset($fileRaw) && $fileRaw==="true"){
            if(isset($_FILES["fileUpload"]) && $_FILES["fileUpload"]["name"]!='' && $_FILES["fileUpload"]["tmp_name"]!=''){
                $fileUploadTemp=$_FILES["fileUpload"]["tmp_name"];
                $fileUpload=explode(".", $_FILES["fileUpload"]["name"]);
                reset($fileUpload);
                $fileName= current($fileUpload);
                $fileExt = strtolower(end($fileUpload));       
                $fileType=  $_FILES["fileUpload"]["type"];
                $fileSize=  $_FILES["fileUpload"]["size"];
                $fileContent = 'true';
            }
        } 
        
        if($fileName!='' && $fileExt!='' && $fileContent!='' && $fileType!='' &&$fileSize!=''){
            if($fileVideoTempFolderPath==''){
                $fileVideoTempFolderPath = Yii::app()->params['fileVideoTempFolderPath'];
            }
			
            $allowedExts = array("mp4","mov","avi","m4v","wmv");
            
            if ((($fileType == "video/mp4")||($fileType == "video/x-m4v")||($fileType == "video/quicktime")
                    ||($fileType == "video/avi") ||($fileType == "video/x-msvideo") ||($fileType == "video/x-ms-wmv")
					||($fileType == "application/octet-stream")) 
                    && ($fileSize < $this->limitVideoSize)  && in_array($fileExt, $allowedExts))
            {
                $pathNew=$fileVideoTempFolderPath."/".md5($fileName.date('Y-m-d H:i:s').microtime()).'.'.$fileExt;
                $filePath= Yii::getpathOfAlias('webroot').$pathNew;
                
                if(file_exists($filePath)){ 
                    $codeStatus=1;
                    $messager= "File is valid.";
                }else{     
                    if(isset($fileRaw) && $fileRaw==="true"){
                        if (move_uploaded_file($fileUploadTemp, $filePath)){
                            //convert to mp4
                            if(in_array($fileExt, array("mov","avi","m4v","wmv"))){                            
                                $filePathOld=$filePath;
                                $pathNew=$fileVideoTempFolderPath."/".md5($fileName.date('Y-m-d H:i:s').microtime()).'.mp4';
                                $filePath= Yii::getpathOfAlias('webroot').$pathNew;                            
                                $ffmpegHandler = new HandlerFFMpeg();
                                $ffmpegHandler->createCommand($filePathOld);
                                $ffmpegHandler->convert($filePath);
                                $ffmpegHandler->execFfmpeg();                            
                                $fileType="video/mp4";                            
                            }
                            //end convert to mp4
                            $status=true;
                            $path=$filePath; 
                            $pathURL= $pathNew;
                            $codeStatus=4;
                            $messager="Upload file success:".$filePath;     
                            $type =$fileType;
                        } else {
                            $codeStatus=2;
                            $messager="Sorry, there was an error uploading your file.";
                        }
                    }else{
                        if (file_put_contents($filePath, base64_decode($fileContent))!== false) {
                            //convert to mp4
                            if(in_array($fileExt, array("mov","avi","m4v","wmv"))){                            
                                $filePathOld=$filePath;
                                $pathNew=$fileVideoTempFolderPath."/".md5($fileName.date('Y-m-d H:i:s').microtime()).'.mp4';
                                $filePath= Yii::getpathOfAlias('webroot').$pathNew;                            
                                $ffmpegHandler = new HandlerFFMpeg();
                                $ffmpegHandler->createCommand($filePathOld);
                                $ffmpegHandler->convert($filePath);
                                $ffmpegHandler->execFfmpeg();                            
                                $fileType="video/mp4";                            
                            }
                            //end convert to mp4
                            $status=true;
                            $path=$filePath; 
                            $pathURL= $pathNew;
                            $codeStatus=4;
                            $messager="Upload file success:".$filePath;     
                            $type =$fileType;
                        } else {
                            $codeStatus=2;
                            $messager="Sorry, there was an error uploading your file.";
                        }
                    }
                }              
            }else{				
                if ($fileSize > $this->limitVideoSize){
                    $codeStatus = 3;//Max size
                    $messager= "File is unvalid: max size.";
                }else{
                    $codeStatus = 5;//
                    $messager= "File is unvalid: not in alowed extention.";
                }
            }
        }
		
        return array('status'=>$status,'path'=>$path,
            'messager'=>$messager,'codeStatus'=>$codeStatus,
            'type'=>$type,'pathURL'=>$pathURL);
    }
}