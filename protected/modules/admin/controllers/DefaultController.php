<?php

/**
 * Class DefaultController
 * This is default controller of admin module.
 * @author UTC.KongLtn
 */
class DefaultController extends Controller
{
    /**
     * Index page of Admin
     * @return undefined
     */
    public function actionIndex()
    {
        $this->titleInHeaderBar = "Admin - Users & Sessions";
        $data = $this->getAdminData();
        $topics = Topics::model()->findAll();
        $dataSetting = Setting::model()->find();
        $this->render('index', array(
            'data' => $data,
            'dataSetting' => $dataSetting,
            'topics' => $topics
        ));
    }

    /**
     * Action Add user
     * Get data from form and new a user
     * @return undefined
     */
    public function actionAddUser()
    {
        try {
            $model = new Users;
            $model->username = Yii::app()->request->getParam('username');
            $model->password = Yii::app()->request->getParam('password');
            $model->dateCreate = date("Y-m-d H:i:s");
            $model->firstName = Yii::app()->request->getParam('firstName');
            $model->lastName = Yii::app()->request->getParam('lastName');
            $model->email = Yii::app()->request->getParam('email');
            $model->phone = Yii::app()->request->getParam('phone');
            $model->gender = Yii::app()->request->getParam('gender');

            if (isset($_FILES['uploadAvatar']) && $_FILES['uploadAvatar']["name"] != ''
                && $_FILES['uploadAvatar']["tmp_name"] != ''
                && $_FILES['uploadAvatar']['error'] == 0
            ) {
                $avaFile = $_FILES['uploadAvatar'];
                $fileUpload = explode(".", $avaFile['name']);
                reset($fileUpload);
                $fileName = current($fileUpload);
                $fileExt = strtolower(end($fileUpload));

                $fileNameNew = md5($fileName . date('Y-m-d H:i:s') . microtime()) . '.' . $fileExt;
                $path = Yii::getpathOfAlias('webroot') . Yii::app()->params["avatarFolderPath"] . '/';
                if (move_uploaded_file($avaFile['tmp_name'], $path . $fileNameNew)) {
                    $currentAva = $path . $fileNameNew;
                    $newAva = $this->createThumbnailImage($path, $currentAva, 50);
                    $nameOfNewAva = explode('/', $newAva);
                    $nameOfNewAva = $nameOfNewAva[count($nameOfNewAva) - 1];
                    if (file_exists($path . $nameOfNewAva) && file_exists($path . $fileNameNew)) {
                        $model->avatarPath = $nameOfNewAva;
                        unlink($path . $fileNameNew);
                        Yii::app()->user->avatarPath = Yii::app()->params['avatarFolderPath'] . '/' . $nameOfNewAva;
                    }
                }
            }
            $model->save();
        } catch (Exception $e) {
            echo $e->getMessage();
            exit;
        }
        $this->redirect(Yii::app()->getBaseUrl() . "/admin");
    }


    /**
     * Action Edit user.
     * Get data from form and edit a user.
     * @return undefined
     */
    public function actionEditUser()
    {
        $username = Yii::app()->request->getParam('usernameEdit');
        if (isset($username) && !is_null($username)) {
            try {
                $model = Users::model()->findByAttributes(array("username" => $username));
                $model->firstName = Yii::app()->request->getParam('firstNameEdit');
                $model->lastName = Yii::app()->request->getParam('lastNameEdit');
                $pass = Yii::app()->request->getParam('passwordEdit');
                if (isset($pass) && $pass !== "") {
                    $model->password = $pass;
                }
                $model->email = Yii::app()->request->getParam('emailEdit');
                $model->phone = Yii::app()->request->getParam('phoneEdit');
                $model->gender = Yii::app()->request->getParam('genderEdit');
                $oldAva = $model->avatarPath;

                if (isset($_FILES['uploadAvatarEdit']) && $_FILES['uploadAvatarEdit']["name"] != ''
                    && $_FILES['uploadAvatarEdit']["tmp_name"] != ''
                    && $_FILES['uploadAvatarEdit']['error'] == 0
                ) {
                    $avaFile = $_FILES['uploadAvatarEdit'];
                    $fileUpload = explode(".", $avaFile['name']);
                    reset($fileUpload);
                    $fileName = current($fileUpload);
                    $fileExt = strtolower(end($fileUpload));

                    $fileNameNew = md5($fileName . date('Y-m-d H:i:s') . microtime()) . '.' . $fileExt;
                    $path = Yii::getpathOfAlias('webroot') . Yii::app()->params["avatarFolderPath"] . '/';
                    if (move_uploaded_file($avaFile['tmp_name'], $path . $fileNameNew)) {
                        $currentAva = $path . $fileNameNew;
                        $newAva = $this->createThumbnailImage($path, $currentAva, 50);
                        if (file_exists($newAva)) {
                            $nameOfNewAva = explode('/', $newAva);
                            $nameOfNewAva = $nameOfNewAva[count($nameOfNewAva) - 1];
                            $model->avatarPath = $nameOfNewAva;
                            if (isset($oldAva) && $oldAva !== "" && file_exists($path . $oldAva)) {
                                unlink($path . $oldAva);
                            }
                            if (file_exists($path . $fileNameNew)) {
                                unlink($path . $fileNameNew);
                            }
                        }
                    }
                }
                $model->save();
            } catch (Exception $e) {
                echo $e->getMessage();
                exit;
            }
        }
        $this->redirect(Yii::app()->getBaseUrl() . "/admin");
    }


    /**
     * Action get user data by id
     * @return undefined
     */
    public function actionGetUser()
    {
        $userId = Yii::app()->request->getParam('id');
        if (isset($userId) && $userId > 0) {
            try {
                $user = Users::model()->findByPk((int)$userId);
                echo json_encode($user->attributes);
            } catch (Exception $e) {
                echo "false";
            }
        } else {
            echo "false";
        }
    }


    /**
     * Action Remove user by id
     * @return undefined
     */
    public function actionRemoveUser()
    {
        $userId = Yii::app()->request->getParam('id');
        try {
            Users::model()->deleteByPk($userId);
        } catch (Exception $e) {
            echo $e->getMessage();
            exit;
        }
        $this->redirect(Yii::app()->getBaseUrl() . "/admin");
    }


    /**
     * Action search User by username or email or firstName or lastName
     * @return undefined
     */
    public function actionSearchUser()
    {
        $this->titleInHeaderBar = "Search User";
        $this->showBackButtonInHeaderBar = true;
        $txtUser = Yii::app()->request->getParam('searchUser');
        $txtUser = preg_replace('!\s+!', ' ', $txtUser);
        $searchArr = array();
        if (isset($txtUser) && trim($txtUser) !== "") {
            $txtUser = ltrim($txtUser, " ");
            $txtUser = rtrim($txtUser, " ");
            array_push($searchArr, $txtUser);
            foreach (explode(" ", $txtUser) as $item) array_push($searchArr, $item);
        }

        $result = array();
        if (count($searchArr) > 0) {
            foreach ($searchArr as $txt) {
                $u = Users::model()->findByAttributes(array("username" => $txt));
                if (is_null($u)) $u = Users::model()->findByAttributes(array("email" => $txt));
                if (is_null($u)) $u = Users::model()->findByAttributes(array("firstName" => $txt));
                if (is_null($u)) $u = Users::model()->findByAttributes(array("lastName" => $txt));
                if (!is_null($u) && !in_array($u, $result)) {
                    array_push($result, $u);
                }
            }
        }
        $this->render('result', array('result' => $result));
    }


    /**
     * Action Remove session by id
     * @return undefined
     */
    public function actionRemoveSession()
    {
        $sessionId = Yii::app()->request->getParam('id');
        try {
            // Remove Session
            Sessions::model()->deleteByPk($sessionId);

            Notify::model()->deleteAll('link = ' . (int)$sessionId . ' and typeNotify = "SESS"');
            NotifyUser::model()->deleteAll('idLink = "SESS' . (int)$sessionId . '"');

            $commentsData = Comments::model()->findAllByAttributes(array("idSession" => $sessionId));
            foreach ($commentsData as $comment) {
                Notify::model()->deleteAll('link = ' . (int)$comment['idComment'] . ' and typeNotify = "COMM"');
                NotifyUser::model()->deleteAll('idLink = "COMM' . (int)$comment['idComment'] . '"');
            }
            // Remove all Comment of Session
            Comments::model()->deleteAllByAttributes(array("idSession" => $sessionId));

        } catch (Exception $e) {
            echo $e->getMessage();
            exit;
        }
        $this->redirect(Yii::app()->getBaseUrl() . "/admin");
    }


    /**
     * Action Remove comment by id
     * @return undefined
     */
    public function actionRemoveComment()
    {
        $commentId = Yii::app()->request->getParam('id');
        try {
            Comments::model()->deleteByPk($commentId);
            Notify::model()->deleteAll('link = ' . (int)$commentId . ' and typeNotify = "COMM"');
            NotifyUser::model()->deleteAll('idLink = "COMM' . (int)$commentId . '"');

            $commentsDataChild = Comments::model()->findAllByAttributes(array("idCommentParent" => $commentId));
            foreach ($commentsDataChild as $comment) {
                Notify::model()->deleteAll('link = ' . (int)$comment['idComment'] . ' and typeNotify = "COMM"');
                NotifyUser::model()->deleteAll('idLink = "COMM' . (int)$comment['idComment'] . '"');
            }
            Comments::model()->deleteAllByAttributes(array("idCommentParent" => $commentId));
        } catch (Exception $e) {
            echo $e->getMessage();
            exit;
        }
    }


    /**
     * Action check user exists
     * @return undefined
     */
    public function  actionExistsUser()
    {
        $username = Yii::app()->request->getParam('username');
        $email = Yii::app()->request->getParam('email');
        $checkUser = Users::model()->countByAttributes(array(
            'username' => $username
        ));
        $checkEmail = Users::model()->countByAttributes(array(
            'email' => $email
        ));
        echo ($checkEmail > 0 || $checkUser > 0) ? "true" : "false";
    }

    /**
     * Action delete document
     * @return undefined
     */
    public function actionDelDocument()
    {
        $docId = Yii::app()->request->getParam('id');
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
        $this->redirect(Yii::app()->getBaseUrl() . "/admin");
    }


    /**
     * Get admin Data (session, user, document, comment) for admin page
     * @return array $data
     */
    public function getAdminData()
    {
        $data = array();

        // Get Planned session (active)
        $data['activeSession'] = Sessions::model()->getActiveSession();
        // Get comments forEach Planned session (active)
        foreach ($data['activeSession'] as $key => $active) {
            $active["comments"] = null;
            $sessionId = (int)$active["idSession"];
            // Get all comment (not child Comments) of Session
            if (isset($sessionId) && $sessionId > 0) {
                $data['activeSession'][$key]["comments"] = Comments::model()->getTextCommentsOfSession($sessionId);
                // Get Child Comments
                foreach ($data['activeSession'][$key]["comments"] as $k => $val) {
                    $data['activeSession'][$key]["comments"][$k]["childComments"] = Comments::model()->getTextReplyByCommentParentId($val["idComment"]);
                }
            }
        }

        // Get Planned session (not active)
        $data['plannedSession'] = Sessions::model()->getPlannedSession();
        // Get comments forEach Planned session (not active)
        foreach ($data['plannedSession'] as $key => $active) {
            $active["comments"] = null;
            $sessionId = (int)$active["idSession"];
            // Get all comment (not child Comments) of Session
            if (isset($sessionId) && $sessionId > 0) {
                $data['plannedSession'][$key]["comments"] = Comments::model()->getTextCommentsOfSession($sessionId);
                // Get Child Comments
                foreach ($data['plannedSession'][$key]["comments"] as $k => $val) {
                    $data['plannedSession'][$key]["comments"][$k]["childComments"] = Comments::model()->getTextReplyByCommentParentId($val["idComment"]);
                }
            }
        }

        // Get Past session
        $data['pastSession'] = Sessions::model()->getPastSession();
        // Get comments forEach Planned session (not active)
        foreach ($data['pastSession'] as $key => $active) {
            $active["comments"] = null;
            $sessionId = (int)$active["idSession"];
            // Get all comment (not child Comments) of Session
            if (isset($sessionId) && $sessionId > 0) {
                $data['pastSession'][$key]["comments"] = Comments::model()->getTextCommentsOfSession($sessionId);
                // Get Child Comments
                foreach ($data['pastSession'][$key]["comments"] as $k => $val) {
                    $data['pastSession'][$key]["comments"][$k]["childComments"] = Comments::model()->getTextReplyByCommentParentId($val["idComment"]);
                }
            }
        }

        // Get users
        $data['users'] = Users::model()->getUsers();

        // Get sessions
        $data['sessions'] = Sessions::model()->getSessions();

        // Get archives
        $data['archives'] = Archives::model()->getArchives();

        // Get Archive_session
        $data['archiveSession'] = ArchiveSession::model()->getArchiveSessions();

        // Add sessions to archives by archive_session table
        foreach ($data['archives'] as $key => $subArchive) {
            $data['archives'][$key]['sessions'] = array();
            foreach ($data['archiveSession'] as $subArSes) {
                if ($subArSes['idArchive'] === $subArchive['idArchive']) {
                    try {
                        $sData = Sessions::model()->findByPk($subArSes['idSession']);
                        array_push($data['archives'][$key]['sessions'], $sData);
                    } catch (Exception $e) {
                        echo $e->getMessage();
                        exit;
                    }
                }
            }
        }

        // Add sessions to users
        foreach ($data['users'] as $key => $subUser) {
            $data['users'][$key]['sessions'] = array();
            foreach ($data['sessions'] as $subSession) {
                if ($subSession['idUserCreate'] === $subUser['idUser']) {
                    $sData = array(
                        'idTopic' => $subSession['idTopic'],
                        'title' => $subSession['title'],
                        'active' => $subSession['active']
                    );
                    array_push($data['users'][$key]['sessions'], $sData);
                }
            }
        }

        return $data;
    }


    /**
     * Add a topic
     * @return undefined
     */
    public function actionAddTopic()
    {
        $name = Yii::app()->request->getParam('name');
        $status = Yii::app()->request->getParam('status');

        if (isset($name) && isset($status) && !is_null($name) && !is_null($status)) {
            try {
                $newTopic = new Topics;
                $newTopic->name = $name;
                $newTopic->active = (int)$status;
                $newTopic->save();
            } catch (Exception $e) {
                echo $e->getMessage();
            }
        }
        $this->redirect(Yii::app()->getBaseUrl() . "/admin");
    }

    /**
     * Add a topic
     * @return undefined
     */
    public function actionEditTopic()
    {
        $topicId = Yii::app()->request->getParam('id');
        $status = Yii::app()->request->getParam('status');
        $name = Yii::app()->request->getParam('name');
        $result = [];
        if (isset($name) && isset($status) && isset($topicId)
            && !is_null($name) && !is_null($status) ) {
            $newTopic = Topics::model()->findByPk((int) $topicId);
            if (!is_null($newTopic)) {
                $newTopic->name = $name;
                $newTopic->active = (int)$status;
                $newTopic->save();
                $result['id'] = (int) $topicId;
                $result['name'] = $name;
                $result['status'] = (int) $status;
            }
        }
        //echo json_encode((object) $result);
        $this->redirect(Yii::app()->getBaseUrl() . "/admin");
    }

    /**
     * Change topic status
     */
    public function actionChangeTopicStatus(){
        if (Yii::app()->request->isAjaxRequest) {
            $topicId = Yii::app()->request->getParam('id');
            $topicId = (int)$topicId;
            $check = -1;

            if (isset($topicId) && !is_null($topicId)) {
                try {
                    $newTopic = Topics::model()->findByPk((int)$topicId);
                    if ($newTopic) {
                        $newTopic->active = ($newTopic->active == 1) ? 0 : 1;
                        $newTopic->save();
                        $check = $newTopic->active;
                    }
                } catch (Exception $e) {
                    echo $e->getMessage();
                }
            }
            echo $check;
        }
    }

    /**
     * Delete topic
     * @return string
     * -1 : init
     * 0 : No topic is del
     * 1 : One topic is del
     *
     */
    public function actionDelTopic(){
        if (Yii::app()->request->isAjaxRequest) {
            $topicId = Yii::app()->request->getParam('id');
            $topicId = (int)$topicId;
            $check = -1;
            if (isset($topicId)) {
                $check = Topics::model()->deleteByPk((int)$topicId);
            }
            echo $check;
        }
    }

    /**
     * Get topic data by topic id
     */
    public function actionGetTopic(){
        if (Yii::app()->request->isAjaxRequest) {
            $topicId = Yii::app()->request->getParam('id');
            $result = [];
            if (isset($topicId)) {
                $check = Topics::model()->findByPk((int)$topicId);
                if ($check) {
                    $result = $check->attributes;
                }
            }
            echo json_encode((object)$result);
        }
    }

}