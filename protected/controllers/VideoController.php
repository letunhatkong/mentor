<?php

/**
 * Class VideoController
 * @author UTC.HuyTD
 * Last Update on Nov 19, 2015
 */
class VideoController extends Controller {
      
    public $limitVideoDuration =  0;
    public $limitVideoSize = 102400000;
    
    public function init() {  
        $dataSetting= Setting::model()->find();
        $this->limitVideoDuration=$dataSetting->allowLimitDuration ==1?$dataSetting->limitDurationValue:$dataSetting->limitDurationDefault;
    }
       
    /**
     * Ajax Function show view upload video
     * @return undefined
     */
    public function actionUpload(){
        if (Yii::app()->request->isAjaxRequest) {
            $this->renderPartial('upload');            
        }else{
            $this->forward('/site/error');
        }        
    }
    
    /**
     * Ajax Function show view edit video
     * @return undefined
     */
    public function actionEdit(){        
        if (Yii::app()->request->isAjaxRequest) {
            $idSession = Yii::app()->request->getParam('idSession');
            $this->titleInHeaderBar = "Edit Video";            
            $videoTemp = $this->saveFileVideoTempToDisk();
            if($videoTemp['status']==true && $videoTemp['codeStatus']==4){
                $idCommentEdit = Yii::app()->request->getParam('idCommentEdit');
                $commentNew = new Comments();
                if((int)$idCommentEdit === 0){                    
                    //create Comment Temp
                    $commentNew->idUserComment = Yii::app()->user->idUser;
                    $commentNew->idSession = $idSession;
                    $commentNew->contentMediaPathTemp = basename($videoTemp['path']);
                    $commentNew->contentMediaType = "VIDEO";
                    $commentNew->dateCreate = date("Y-m-d H:i:s");
                    $commentNew->lastUpdate = date("Y-m-d H:i:s");
                    $commentNew->isTemp = 1;
                }else{
                    //update comment with fileVideoFolderPathNew  
                    $comment=Comments::model()->findByPk($idCommentEdit);
                    $comment->contentMediaPathTemp = basename($videoTemp['path']);                      
                    $comment->lastUpdate = date("Y-m-d H:i:s");
                    $comment->save(); 
                }                
                
                $pathVideoTemp = $videoTemp['path'];
                $ressultCheckDuration = $this->checkDurationFileVideo($pathVideoTemp);
                if($ressultCheckDuration['status']==true && $ressultCheckDuration['codeStatus']==2){
                    //move to video primary Path
                    $fileVideoFolderPathNew = Yii::getpathOfAlias('webroot').Yii::app()->params['fileVideoFolderPath'].'/'.basename($pathVideoTemp);
                    copy($pathVideoTemp, $fileVideoFolderPathNew);
                    if(file_exists($pathVideoTemp)){
                        unlink($pathVideoTemp);
                    }
                    $typeStatus = null;
                    $idComment=null;
                    $idCommentParent=null;
                    $dataIdUser = InvitedSession::model()->getIdUserCreateAndInvitedUser($idSession);
                    $arrayUserInvited =  $this->arrayColumn($dataIdUser, 'idUser');
                    if((int)$idCommentEdit === 0){
                        $commentNew->contentMediaPathTemp = "";
                        $commentNew->contentMediaPath = basename($videoTemp['path']);
                        $commentNew->isTemp = 0;
                        $commentNew->save(); 
                        $typeStatus = 'add';
                        $idComment=$commentNew->idComment;
                        $idCommentParent = $commentNew->idCommentParent;
                        //Add Notify
                        $notifyNew = new Notify();
                        $notifyNew->createUserId =  Yii::app()->user->idUser;                 
                        $notifyNew->dateCreate = date("Y-m-d H:i:s");
                        $notifyNew->typeNotify = 'COMM';
                        $notifyNew->content = 'Added a video comment';
                        $notifyNew->link = (int)$commentNew->idComment;
                        $notifyNew->seconds = time();
                        $notifyNew->save();
                        //End add notify                        
                        foreach($dataIdUser as $rowInvited){
                            if($rowInvited['idUser']!=Yii::app()->user->idUser){
                                //Send notify user
                                $notifyUserNew = new NotifyUser();
                                $notifyUserNew->userId =  (int)$rowInvited['idUser'];                 
                                $notifyUserNew->notifyId = (int)$notifyNew->notifyId;
                                $notifyUserNew->dateRead = date("Y-m-d H:i:s");
                                $notifyUserNew->idLink = 'COMM'.(int)$commentNew->idComment;
                                $notifyUserNew->save();
                                //End send notify user 
                            }
                        }                        
                    }else{
                        //update comment with fileVideoFolderPathNew  
                        $comment=Comments::model()->findByPk($idCommentEdit);
                        $comment->contentMediaPathTemp = "";
                        $comment->contentMediaPath = basename($videoTemp['path']);
                        $comment->save(); 
                        $typeStatus = 'edit';
                        $idComment=$comment->idComment;
                        $idCommentParent = $comment->idCommentParent;
                    }
                    $dataReturn=array(
                        'avaPath'=> Yii::app()->user->avatarPath,
                        'content' => "Added a video comment.",
                        'invitedUser' =>$arrayUserInvited,
                        'fullNameOfCreator' => Yii::app()->user->firstName . ' '.Yii::app()->user->lastName,
                        'ownerId' => Yii::app()->user->idUser,
                        'gender' => (Yii::app()->user->gender == 1) ? "his":"her",
                        'time' => time(),
                        'type'=> 'comm',
                        'redirect' => 'observation/detail/id/'. $idSession ."#commentRow". $idComment,


                        'typeComment' => "video",
                        'typeAction' => $typeStatus,
                        'status'=>'true',
                        //'typeStatus'=>,
                        'idComment'=>$idComment,
                        'idSession'=>$idSession,
                        'idCommentParent'=>(int)$idCommentParent,
                        'sessionInvited'=>$arrayUserInvited
                    );                    
                    echo json_encode($dataReturn);exit;
                }else if($ressultCheckDuration['status']==false && $ressultCheckDuration['codeStatus']==1){
                    $idComment = $idCommentEdit;
                    if((int)$idCommentEdit === 0){
                        $commentNew->save();
                        $idComment = $commentNew->idComment;
                    }
                    //show edit video file 
                    //note: show error ressultCheckDuration[messager]
                    $this->renderPartial('edit',array(
                        'errorString' =>$ressultCheckDuration['messager'],
                        'duration'=>$ressultCheckDuration['duration'],
                        'type'=>$videoTemp['type'],
                        'pathURL'=>$videoTemp['pathURL'],
                        'idComment'=>$idComment
                    ));
                }            
            }else{            
                //note: show error videoTemp[messager]            
                $this->renderPartial('upload',array(
                    'errorString' =>$videoTemp['messager']
                ));
            }
        }else{
            $this->forward('/site/error');
        }
    }
    
    /**
     * Ajax Function split video
     * @return undefined
     */
    public function actionSplitVideo(){
        if (Yii::app()->request->isAjaxRequest) {
            $notifyId = 0;
            $startTimeSplit= Yii::app()->request->getParam('startTimeSplit');
            $durationSplit= Yii::app()->request->getParam('durationSplit');
            $idComment = Yii::app()->request->getParam('idComment');            
            $commentTemInfo = Comments::model()->findByPk($idComment);            
            $pathFileVideo = Yii::getpathOfAlias('webroot').Yii::app()->params['fileVideoTempFolderPath'].'/'.$commentTemInfo['contentMediaPathTemp'];
            $outputPath= Yii::getpathOfAlias('webroot').Yii::app()->params['fileVideoFolderPath'].'/'.basename($pathFileVideo);                    
            $commentTemInfoPathMediaOld = $commentTemInfo->contentMediaPath;
            $ffmpegHandler = new HandlerFFMpeg();
            $ffmpegHandler->createCommand($pathFileVideo);
            $ffmpegHandler->split($outputPath, $startTimeSplit, $durationSplit);
            $ffmpegHandler->execFfmpeg();
            
            $commentTemInfo->contentMediaPathTemp = "";
            $commentTemInfo->contentMediaPath = basename($outputPath);
            $commentTemInfo->isTemp = 0;
            $commentTemInfo->save();
            
            if(file_exists($pathFileVideo)){
                unlink($pathFileVideo);
            }
            $typeStatus=null;
            $dataIdUser = InvitedSession::model()->getIdUserCreateAndInvitedUser($commentTemInfo->idSession);
            $arrayUserInvited =  $this->arrayColumn($dataIdUser, 'idUser');
            if($commentTemInfoPathMediaOld!="" && $commentTemInfoPathMediaOld!=null){
                $typeStatus = "edit";
            }else{
                $typeStatus = "add";                
                //Add Notify
                $notifyNew = new Notify();
                $notifyNew->createUserId =  Yii::app()->user->idUser;                 
                $notifyNew->dateCreate = date("Y-m-d H:i:s");
                $notifyNew->typeNotify = 'COMM';
                $notifyNew->content = 'Added a video comment';
                $notifyNew->link = (int)$commentTemInfo->idComment;
                $notifyNew->save();
                $notifyId = $notifyNew->notifyId;
                //End add notify                        
                foreach($dataIdUser as $rowInvited){
                    if($rowInvited['idUser']!=Yii::app()->user->idUser){
                        //Send notify user
                        $notifyUserNew = new NotifyUser();
                        $notifyUserNew->userId =  (int)$rowInvited['idUser'];                 
                        $notifyUserNew->notifyId = (int)$notifyNew->notifyId;
                        $notifyUserNew->dateRead = date("Y-m-d H:i:s");
                        $notifyUserNew->idLink = 'COMM'.(int)$commentTemInfo->idComment;
                        $notifyUserNew->save();
                        //End send notify user 
                    }
                }
            }
            $dataReturn=array(
                'avaPath'=> Yii::app()->user->avatarPath,
                'content' => "Added a video comment",
                'invitedUser' =>$arrayUserInvited,
                'fullNameOfCreator' => Yii::app()->user->firstName . ' '.Yii::app()->user->lastName,
                'ownerId' => Yii::app()->user->idUser,
                'gender' => (Yii::app()->user->gender == 1) ? "his":"her",
                'time' => time(),
                'type'=> 'comm',
                'notifyId' => $notifyId,
                'redirect' => 'observation/detail/id/'. $commentTemInfo->idSession ."#commentRow". $commentTemInfo->idComment,


                'typeComment' => "video",
                'typeAction' => "add",
                'status'=>'true',
                'typeStatus'=>$typeStatus,
                'idComment'=>$commentTemInfo->idComment,
                'idSession'=>$commentTemInfo->idSession,
                'idCommentParent'=>(int)$commentTemInfo->idCommentParent,
                'sessionInvited'=>$arrayUserInvited
            );
            echo json_encode($dataReturn);exit;
        }else{
            $this->forward('/site/error');
        }
    }    
    
    /**
     * Function save video file upload to disk
     * @param string $fileVideoTempFolderPath
     * @return array
     */
    public function saveFileVideoTempToDisk($fileVideoTempFolderPath = "") {
        $status=false;
        $path='';
        $pathURL='';
        $codeStatus=0;
        $message='Cannot upload file.';
        $type = "";

        if(isset($_FILES["videoBrowserValid"]) && $_FILES["videoBrowserValid"]["name"]!='' && $_FILES["videoBrowserValid"]["tmp_name"]!=''){
            $fileUploadTemp=$_FILES["videoBrowserValid"]["tmp_name"];
            $fileUpload=explode(".", $_FILES["videoBrowserValid"]["name"]);
            reset($fileUpload);
            $fileName= current($fileUpload);
            $fileExt = strtolower(end($fileUpload));       
            $fileType=  $_FILES["videoBrowserValid"]["type"];
            $fileSize=  $_FILES["videoBrowserValid"]["size"];
            
            if($fileVideoTempFolderPath==''){
                $fileVideoTempFolderPath = Yii::app()->params['fileVideoTempFolderPath'];
            }
			
            $allowedExts = array("mp4","mov","avi","m4v","wmv");
            
            if ((($fileType == "video/mp4")||($fileType == "video/x-m4v")||($fileType == "video/quicktime")
                    ||($fileType == "video/avi") ||($fileType == "video/x-msvideo") ||($fileType == "video/x-ms-wmv")) 
                    && ($fileSize < $this->limitVideoSize)  && in_array($fileExt, $allowedExts))
            {
                $pathNew=$fileVideoTempFolderPath."/".md5($fileName.date('Y-m-d H:i:s').microtime()).'.'.$fileExt;
                $filePath= Yii::getpathOfAlias('webroot').$pathNew;
                
                if(file_exists($filePath)){ 
                    $codeStatus=1;
                    $message= "File is valid.";
                }else{
                    if (move_uploaded_file($fileUploadTemp, $filePath)) {
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
                        $message="Upload file success:".$filePath;
                        $type =$fileType;
                    } else {
                        $codeStatus=2;
                        $message="Sorry, there was an error uploading your file.";
                    }
                }              
            }else{				
                if ($fileSize > $this->limitVideoSize){
                    $codeStatus = 3;//Max size
                    $message= "File is invalid: max size.";
                }else{
                    $codeStatus = 5;//
                    $message= "File is invalid: not in allowed extension.";
                }
            }
        }
		
        return array('status'=>$status,'path'=>$path,
            'messager'=>$message,'codeStatus'=>$codeStatus,
            'type'=>$type,'pathURL'=>$pathURL);
    }
    
    /**
     * Function check duration of file video
     * @return status
     * @return codeStatus
     * @return messager
     * @return duration
     */
    public function checkDurationFileVideo($pathFileVideo="") {
        $status=false;
        $codeStatus=0;
        $message='Cannot check file.';
        $duration = 0;
        if($pathFileVideo!=="" && file_exists($pathFileVideo)){            
            $ffmpegHandler = new HandlerFFMpeg();
            $durationFile = $ffmpegHandler->createCommand($pathFileVideo)->getDuration();
            $duration = $durationFile['durationInt'];
            //check file duration with ffmpeg 
            if($this->limitVideoDuration >0 &&$durationFile['durationInt'] > $this->limitVideoDuration){
                $codeStatus=1;
                $message='Video duration exceeds the allowed limit.';
            }else{
                $status=true;        
                $codeStatus=2;
                $message='';
            }
        }
        
        return array(
            'status'=>$status,
            'messager'=>$message,
            'codeStatus'=>$codeStatus,
            'duration'=>$duration
        );
    }
}