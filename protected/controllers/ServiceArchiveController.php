<?php

/**
 * Class ArchiveServiceController
 * @author Samuel Kong
 * LastUpdate Dec 22, 2015
 */
class ServiceArchiveController extends ApiController
{

    // Set layout for controller
    public $layout = '';

    public function actionUpload()
    {
        try {
            $archiveId = Yii::app()->request->getPost('archiveId');
            $userId = Yii::app()->request->getPost('userId');
            $archiveFolderPath = Yii::app()->params['archiveFolderPath'];
            $returnData = $this->saveFileToDisk($archiveFolderPath);

            if ($returnData['status'] == true && $returnData['codeStatus'] == 4 && isset($archiveId)) {
                $archiveId = (int)$archiveId;
                $model = ($archiveId == 0) ? new Archives : Archives::model()->findByPk($archiveId);
                // Set data
                $model->idUserCreate = (int)$userId;
                $model->name = $returnData['name'];
                $model->dateCreate = date("Y-m-d H:i:s");
                $model->typeArchive = 'file';

                // Edit Archive
                if ($archiveId > 0) {
                    $archiveFolder = Yii::app()->params["archiveFolderPath"];
                    $oldDoc = $model->path;
                    $newDoc = $returnData['md5FileName'];
                    $newPath = Yii::getpathOfAlias('webroot') . $archiveFolder . "/" . $newDoc;
                    $oldPath = Yii::getpathOfAlias('webroot') . $archiveFolder . "/" . $oldDoc;
                    if (!file_exists($newPath)) {
                        if (isset($oldDoc) && $oldDoc != "" && file_exists($oldPath)) {
                            unlink($oldPath);
                        }
                    }
                }
                // # Edit Archive

                $model->path = $returnData['md5FileName'];
                $model->save();
            }

            ApiHelper::sendResponse(200, '', $returnData);
        } catch (Exception $e) {
            ApiHelper::sendResponse(500, '', 'Error ' . $e);
        }
    }


    public function saveFileToDisk($archiveFolderPath = "")
    {
        $status = false;
        $md5FileName = '';
        $pathURL = '';
        $codeStatus = 0;
        $messager = 'Cannot upload file.';

        $fileName = Yii::app()->request->getPost('fileName');
        $name = $fileName;
        $fileExt = strtolower(Yii::app()->request->getPost('fileExt'));
        $fileContent = Yii::app()->request->getPost('fileContent');
        $fileRaw = Yii::app()->request->getPost('fileRaw');

        $fileUploadTemp = "";
        if (isset($fileRaw) && $fileRaw === "true") {
            if (isset($_FILES["fileUpload"]) && $_FILES["fileUpload"]["name"] != '' && $_FILES["fileUpload"]["tmp_name"] != '') {
                $fileUploadTemp = $_FILES["fileUpload"]["tmp_name"];
                $fileUpload = explode(".", $_FILES["fileUpload"]["name"]);
                reset($fileUpload);
                $fileName = current($fileUpload);
                $fileExt = strtolower(end($fileUpload));
                $fileContent = 'true';
            }
        }

        if ($fileName != '' && $fileExt != '' && $fileContent != '') {
            if ($archiveFolderPath == '') {
                $archiveFolderPath = Yii::app()->params['archiveFolderPath'];
            }

            $blackListExts = array("exe");

            if (!in_array($fileExt, $blackListExts)) {
                $md5Name = md5($fileName . date('Y-m-d H:i:s') . microtime());
                $pathNew = $archiveFolderPath . "/" . $md5Name . '.' . $fileExt;
                $filePath = Yii::getpathOfAlias('webroot') . $pathNew;
                $md5FileName = $md5Name . '.' . $fileExt;

                if (file_exists($filePath)) {
                    $codeStatus = 1;
                    $messager = "File is valid.";
                } else {
                    if (isset($fileRaw) && $fileRaw === "true") {
                        if (move_uploaded_file($fileUploadTemp, $filePath)) {
                            $status = true;
                            $codeStatus = 4;
                            $messager = "Upload file success:" . $filePath;
                        } else {
                            $codeStatus = 2;
                            $messager = "Sorry, there was an error uploading your file.";
                        }
                    } else {
                        if (file_put_contents($filePath, base64_decode($fileContent)) !== false) {
                            $status = true;
                            $pathURL = $filePath;
                            $codeStatus = 4;
                            $messager = "Upload file success:" . $filePath;
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

        return array(
            'status' => $status,
            'name' => $name,
            'md5FileName' => $md5FileName,
            'messager' => $messager,
            'codeStatus' => $codeStatus,
            'pathURL' => $pathURL
        );
    }


}