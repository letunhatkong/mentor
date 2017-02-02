<?php

class ServicePictureController extends ApiController
{

    // Set layout for controller
    public $layout = '';

    public function actionUpload()
    {
        try {
            $idSession = Yii::app()->request->getPost('idSession');
            $idEditComment = Yii::app()->request->getPost('idEditComment');
            $folderPath = Yii::app()->params['fileImagesFolderPath'];
            $pictureTemp = $this->saveFileToDisk($folderPath);
            //var_dump($_POST);var_dump($_FILES);var_dump($pictureTemp);exit;
            if ($pictureTemp['status'] == true && $pictureTemp['codeStatus'] == 4) {
                if ((int)$idEditComment === 0) {
                    $modelImgComment = new Comments;
                    $modelImgComment->idUserComment = Yii::app()->request->getPost('idUserComment');
                    $modelImgComment->idSession = $idSession;
                    $modelImgComment->contentMediaType = "PICTURE";
                    $modelImgComment->dateCreate = date("Y-m-d H:i:s");
                    $modelImgComment->lastUpdate = date("Y-m-d H:i:s");
                    $modelImgComment->contentMediaPath = basename($pictureTemp['path']);
                    $modelImgComment->save();
                    $typeStatus = "add";
                    $idComment = $modelImgComment->idComment;
                    $idCommentParent = $modelImgComment->idCommentParent;
                    //Add Notify
                    $notifyNew = new Notify();
                    $notifyNew->createUserId = Yii::app()->request->getPost('idUserComment');
                    $notifyNew->dateCreate = date("Y-m-d H:i:s");
                    $notifyNew->typeNotify = 'COMM';
                    $notifyNew->content = 'Add a picture comment';
                    $notifyNew->link = (int)$modelImgComment->idComment;
                    $notifyNew->seconds = time();
                    $notifyNew->save();
                    //End add notify
                    $dataIdUser = InvitedSession::model()->getIdUserCreateAndInvitedUser($modelImgComment->idSession);
                    $arrayUserInvited = $this->arrayColumn($dataIdUser, 'idUser');
                    foreach ($dataIdUser as $rowInvited) {
                        if ($rowInvited['idUser'] != Yii::app()->request->getPost('idUserComment')) {
                            //Send notify user
                            $notifyUserNew = new NotifyUser();
                            $notifyUserNew->userId = (int)$rowInvited['idUser'];
                            $notifyUserNew->notifyId = (int)$notifyNew->notifyId;
                            $notifyUserNew->dateRead = date("Y-m-d H:i:s");
                            $notifyUserNew->idLink = 'COMM' . (int)$modelImgComment->idComment;
                            $notifyUserNew->save();
                            //End send notify user 
                        }
                    }
                } else {
                    $modelImageCommentEdit = Comments::model()->findByPk((int)$idEditComment);
                    $modelImageCommentEdit->lastUpdate = date("Y-m-d H:i:s");
                    $modelImageCommentEdit->contentMediaPath = basename($pictureTemp['path']);
                    $modelImageCommentEdit->save();
                    $typeStatus = "edit";
                    $idComment = $modelImageCommentEdit->idComment;
                    $idCommentParent = $modelImageCommentEdit->idCommentParent;
                    //Add Notify
                    $notifyNew = new Notify();
                    $notifyNew->createUserId = Yii::app()->request->getPost('idUserComment');
                    $notifyNew->dateCreate = date("Y-m-d H:i:s");
                    $notifyNew->typeNotify = 'COMM';
                    $notifyNew->content = 'Edited a picture comment';
                    $notifyNew->link = (int)$modelImageCommentEdit->idComment;
                    $notifyNew->seconds = time();
                    $notifyNew->save();
                    //End add notify
                    $dataIdUser = InvitedSession::model()->getIdUserCreateAndInvitedUser($modelImageCommentEdit->idSession);
                    $arrayUserInvited = $this->arrayColumn($dataIdUser, 'idUser');
                }
                $dataReturn = array(
                    /*'avaPath' => Yii::app()->user->avatarPath,
                    'content' => "Added a picture comment",
                    'fullNameOfCreator' => Yii::app()->user->firstName . ' ' . Yii::app()->user->lastName,
                    'ownerId' =>  Yii::app()->request->getPost('idUserComment'),
                    'gender' => (Yii::app()->user->gender == 1) ? "his" : "her",
                    'time' => time(),
                    'type' => 'comm',
                    'notifyId' => $notifyNew->notifyId,
                    'redirect' => 'observation/detail/id/' . $id . "#commentRow" . $modelImgComment->idComment,                    
                    'typeComment' => "picture",
                    */
                    'status' => 'true',
                    'typeStatus' => $typeStatus,
                    'idComment' => $idComment,
                    'idSession' => $idSession,
                    'idCommentParent' => (int)$idCommentParent,
                    'sessionInvited' => $arrayUserInvited
                );
                ApiHelper::sendResponse(200, '', $dataReturn);
            } else {
                ApiHelper::sendResponse(500, '', 'Error ' . $pictureTemp['codeStatus']);
            }
        } catch (Exception $ex) {
            ApiHelper::sendResponse(500, '', 'Error ' . $ex);
        }
    }

    public function saveFileToDisk($fileImagesFolderPath = "")
    {
        $status = false;
        $path = '';
        $pathURL = '';
        $codeStatus = 0;
        $messager = 'Cannot upload file.';
        $type = "";

        $fileName = Yii::app()->request->getPost('fileName');
        $fileExt = strtolower(Yii::app()->request->getPost('fileExt'));
        $fileContent = Yii::app()->request->getPost('fileContent');
        $fileType = Yii::app()->request->getPost('fileType');
        $fileSize = Yii::app()->request->getPost('fileSize');

        $fileUploadTemp = "";
        $fileRaw = Yii::app()->request->getPost('fileRaw');
        if (isset($fileRaw) && $fileRaw === "true") {
            if (isset($_FILES["fileUpload"]) && $_FILES["fileUpload"]["name"] != '' && $_FILES["fileUpload"]["tmp_name"] != '') {
                $fileUploadTemp = $_FILES["fileUpload"]["tmp_name"];
                $fileUpload = explode(".", $_FILES["fileUpload"]["name"]);
                reset($fileUpload);
                $fileName = current($fileUpload);
                $fileExt = strtolower(end($fileUpload));
                $fileType = $_FILES["fileUpload"]["type"];
                $fileSize = $_FILES["fileUpload"]["size"];
                $fileContent = 'true';
            }
        }

        if ($fileName != '' && $fileExt != '' && $fileContent != '' && $fileType != '' && $fileSize != '') {
            if ($fileImagesFolderPath == '') {
                $fileImagesFolderPath = Yii::app()->params['fileImagesFolderPath'];
            }

            $allowedExts = array("gif", "jpg", "jpeg", "png");

            if ((($fileType == "image/gif" || $fileType == "image/jpeg"
                    || $fileType == "image/jpg" || $fileType == "image/pjpeg"
                    || $fileType == "image/x-png" || $fileType == "image/png" || $fileType == "application/octet-stream"))
                && in_array($fileExt, $allowedExts)
            ) {
                $md5Name = md5($fileName . date('Y-m-d H:i:s') . microtime());
                $pathNew = $fileImagesFolderPath . "/" . $md5Name . '.' . $fileExt;
                $filePath = Yii::getpathOfAlias('webroot') . $pathNew;

                if (file_exists($filePath)) {
                    $codeStatus = 1;
                    $messager = "File is valid.";
                } else {
                    if (isset($fileRaw) && $fileRaw === "true") {
                        if (move_uploaded_file($fileUploadTemp, $filePath)) {
                            $status = true;
                            $path = $filePath;
                            $pathURL = $pathNew;
                            $codeStatus = 4;
                            $messager = "Upload file success:" . $filePath;
                            $type = $fileType;
                        } else {
                            $codeStatus = 2;
                            $messager = "Sorry, there was an error uploading your file.";
                        }
                    } else {
                        if (file_put_contents($filePath, base64_decode($fileContent)) !== false) {
                            $status = true;
                            $path = $filePath;
                            $pathURL = $pathNew;
                            $codeStatus = 4;
                            $messager = "Upload file success:" . $filePath;
                            $type = $fileType;
                        } else {
                            $codeStatus = 2;
                            $messager = "Sorry, there was an error uploading your file.";
                        }
                    }
                }
            } else {
                $codeStatus = 3;
                $messager = "File is unvalid: not in alowed extention.";
            }
        }

        if ($status && $codeStatus == 4) {
            // Resize image if width > maxWidth
            list($width) = getimagesize($path);
            if ($width > 800 && isset($md5Name)) {
                $folderPath = Yii::getpathOfAlias('webroot') . $fileImagesFolderPath . '/';
                $newPic = $this->createThumbnailImage($folderPath, $path, 800);
                if ($newPic && $newPic != "") {
                    if (file_exists($path)) {
                        unlink($path);
                    }
                    $fileNameNew = $md5Name . '_thumbnail.' . $fileExt;
                    $path = $folderPath . $fileNameNew;
                    $pathURL = $fileImagesFolderPath . "/" . $fileNameNew;
                }
            }
        }

        return array('status' => $status, 'path' => $path,
            'messager' => $messager, 'codeStatus' => $codeStatus,
            'type' => $type, 'pathURL' => $pathURL);
    }
}