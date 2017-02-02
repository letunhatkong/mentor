<?php

/**
 * Class ProfileController
 * This is profile controller of auth module.
 * @author UTC.KongLtn
 */
class ProfileController extends Controller
{
    /**
     * Filter before load controller
     */
    public function filters(){
        if (Yii::app()->user->isGuest) {
            $this->redirect("/auth/user/login");
        }
    }

    /**
     * Edit profile of user
     * @return undefined
     */
    public function actionEdit()
    {
        $this->titleInHeaderBar = "User Profile";
        $userId = Yii::app()->user->idUser;
        $model = Users::model()->findByPk($userId);
        $this->render('edit', array('model' => $model));
    }

    public function actionEditProfile(){
        if (Yii::app()->request->isAjaxRequest) {
            $userId = Yii::app()->user->idUser;
            $model = Users::model()->findByPk($userId);

            // Edit profile and Save
            if ($model) {
                try {
                    $model->firstName = Yii::app()->request->getParam('firstName');
                    $model->lastName = Yii::app()->request->getParam('lastName');
                    $model->gender = Yii::app()->request->getParam('gender');
                    $model->phone = Yii::app()->request->getParam('phone');
                    $oldAva = $model->avatarPath;

                    if (isset($_FILES['avatarPath']) && $_FILES['avatarPath']["name"] != ''
                        && $_FILES['avatarPath']["tmp_name"] != ''
                        && $_FILES['avatarPath']['error'] == 0
                    ) {
                        $avaFile = $_FILES['avatarPath'];
                        $fileUpload = explode(".", $avaFile['name']);
                        reset($fileUpload);
                        $fileName = current($fileUpload);
                        $fileExt = strtolower(end($fileUpload));

                        $fileNameNew = md5($fileName . date('Y-m-d H:i:s') . microtime()) . '.' . $fileExt;
                        $path = Yii::getpathOfAlias('webroot') . '/upload/avatars/';
                        if (move_uploaded_file($avaFile['tmp_name'], $path . $fileNameNew)) {
                            $currentAva = $path . $fileNameNew;
                            $newAva = $this->createThumbnailImage($path, $currentAva, 50);
                            $nameOfNewAva = explode('/', $newAva);
                            $nameOfNewAva = $nameOfNewAva[count($nameOfNewAva) - 1];

                            if (isset($nameOfNewAva) && file_exists($path . $nameOfNewAva)) {
                                $model->avatarPath = $nameOfNewAva;
                                Yii::app()->user->avatarPath = Yii::app()->params['avatarFolderPath'] . '/' . $nameOfNewAva;
                                if (file_exists($currentAva)) {
                                    unlink($currentAva);
                                }
                                if (isset($oldAva) && $oldAva !== "" && file_exists($path . $oldAva)) {
                                    unlink($path . $oldAva);
                                }
                            }
                        }
                    }

                    $model->save();
                    $this->renderPartial("success");

                } catch (Exception $e) {
                    $this->forward('/site/error');
                }
            }
        }
    }

    /**
     * Edit password action
     */
    public function actionEditPass()
    {
        if (Yii::app()->request->isAjaxRequest) {
            $userId = Yii::app()->user->idUser;
            try {
                $model = Users::model()->findByPk($userId);
                $newPass = Yii::app()->request->getParam('newPass');
                $model->password = $newPass;
                $model->save();
                echo json_encode($newPass);
            } catch (Exception $e) {
                echo $e->getMessage();
                exit;
            }
        }
    }
}