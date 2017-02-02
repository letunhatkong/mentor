<?php

/**
 * Controller for Archive page
 * @author      UTC.KongLtn
 * @date        2015/11/03
 */
class ArchivesController extends Controller
{

    /**
     * Redirect to auth page if user is Guest.
     * Filter function will load before each controller
     * @return undefined
     */
    public function filters()
    {
        if (Yii::app()->user->isGuest) {
            $this->redirect(Yii::app()->getBaseUrl() . "/auth/user/login");
        }
    }


    /**
     * List all archives of user
     * This is index view of archive page
     * @return  undefined
     */
    public function actionIndex()
    {
        $this->titleInHeaderBar = "Archives";
        $model = new Archives;
        $currUser = Yii::app()->user;
        $idUser = ($currUser->isGuest) ? 0 : $currUser->idUser;
        $result = $model->getAllByUserId($idUser);

        forEach ($result as $key => $val) {
            $result[$key]["elapsedTime"] = $this->getTimingTodayType($result[$key]["dateCreate"]);
        }
        $this->render('index', array('archives' => $result));
    }


    /**
     * Get data from form and Create a archives when user click submit button
     * @return undefined
     */
    public function actionCreate()
    {
        $this->titleInHeaderBar = "Create Archives";
        try {
            $archiveName = Yii::app()->request->getParam('archiveName');
            $archiveLink = Yii::app()->request->getParam('archiveLink');
            $archiveId = (int)Yii::app()->request->getParam('archiveId');

            $model = ($archiveId == 0) ? new Archives : Archives::model()->findByPk($archiveId);
            $model->name = $archiveName;
            $model->idUserCreate = Yii::app()->user->idUser;
            $model->dateCreate = date("Y-m-d H:i:s");

            // Archive File
            $archiveFolder = Yii::app()->params["archiveFolderPath"];
            if (isset($_FILES['archivePath']) && $_FILES['archivePath']["name"] != ''
                && $_FILES['archivePath']["tmp_name"] != ''
                && $_FILES['archivePath']['error'] == 0
            ) {
                $file = $_FILES['archivePath'];
                $fileUpload = explode(".", $file['name']);
                reset($fileUpload);
                $fileName = current($fileUpload);
                $fileExt = strtolower(end($fileUpload));
                $newDoc = md5($fileName . date('Y-m-d H:i:s') . microtime()) . '.' . $fileExt;

                // New a file
                if ($archiveId == 0) {
                    $path = Yii::getpathOfAlias('webroot') . $archiveFolder . '/' . $newDoc;
                    // Save file if file doesn't exists
                    if (!file_exists($path)) {
                        if (move_uploaded_file($file['tmp_name'], $path)) {
                            $model->fileName = $file['name'];
                            $model->path = $newDoc;
                            $model->link = '';
                            $model->typeArchive = "file";
                        }
                    }
                } else if ($archiveId > 0) { // Edit a file
                    $oldDoc = $model->path;
                    $newPath = Yii::getpathOfAlias('webroot') . $archiveFolder . "/" . $newDoc;
                    $oldPath = Yii::getpathOfAlias('webroot') . $archiveFolder . "/" . $oldDoc;

                    if (!file_exists($newPath)) {
                        if (move_uploaded_file($file['tmp_name'], $newPath)) {
                            $model->fileName = $file['name'];
                            $model->link = "";
                            $model->path = $newDoc;
                            $model->typeArchive = "file";
                            if (isset($oldDoc) && $oldDoc != "" && file_exists($oldPath)) {
                                unlink($oldPath);
                            }
                        }
                    }
                }
            }

            // Link
            if (isset($archiveLink) && !is_null($archiveLink) && $archiveLink != "") {
                $model->fileName = "";
                $model->link = $archiveLink;
                $model->path = "";
                $model->typeArchive = "link";
            }

            $model->save();
            $this->redirect(Yii::app()->getBaseUrl() . "/archives");
        } catch (Exception $e) {
            echo $e;
            exit;
        }
    }

    /**
     * Action delete document by archive id
     * Redirect to archives page after delete document or this document is not owned
     * @return undefined
     */
    public function actionDelDocument()
    {
        $docId = Yii::app()->request->getParam('id');
        $userId = Yii::app()->user->idUser;

        $check = Archives::model()->checkOwner($userId, $docId);
        if (!$check) {
            $this->redirect(Yii::app()->getBaseUrl() . "/archives");
        }

        if (isset($docId) && $docId > 0) {
            try {
                $docPath = Archives::model()->findByPk((int)$docId);
                if (isset($docPath) && $docPath !== null) {
                    $docPath = $docPath->path;
                }
                Archives::model()->deleteByPk((int)$docId);
                ArchiveSession::model()->deleteAllByAttributes(array(), 'idArchive = :id', array(":id" => $docId));
                $link = Yii::getPathOfAlias('webroot') . Yii::app()->params["archiveFolderPath"] . '/' . $docPath;
                if (isset($docPath) && $docPath != "" && file_exists($link)) {
                    unlink($link);
                }
            } catch (Exception $e) {
                echo $e->getMessage();
            }
        }
        $this->redirect(Yii::app()->getBaseUrl() . "/archives");
    }


    /**
     * Action show error
     * @return undefined
     */
    public function actionError()
    {
        if ($error = Yii::app()->errorHandler->error) {
            if (Yii::app()->request->isAjaxRequest) {
                echo $error['message'];
            } else {
                $this->render('error', $error);
            }
        }
    }

    /**
     * Get archive data by id
     */
    public function actionGet()
    {
        if (Yii::app()->request->isAjaxRequest) {
            $archiveId = Yii::app()->request->getParam('id');
            $result = Archives::model()->findByPk((int)$archiveId);
            if ($result)
                echo json_encode($result->attributes);
            else echo 'null';
        } else echo 'null';
    }

    /**
     * Action Download file
     * @return undefined
     */
    public function actionDownload()
    {
        $file = Yii::app()->request->getParam('file');
        $modelArchives = Archives::model()->find('path=:path', array(':path' => $file));
        header("Pragma: public");
        header("Expires: 0");
        header("Cache-Control: must-revalidate, post-check=0, pre-check=0");
        header("Content-type:application/octet-stream");
        header('Content-Disposition: attachment; filename="' . $modelArchives->fileName . '"');
        $contentArchive = Yii::getPathOfAlias('webroot') . Yii::app()->params["archiveFolderPath"] . '/' . $file;
        echo file_get_contents($contentArchive);
        exit;
    }
}