<?php

/**
 * Controller for Screen Observation
 * @author UTC.BaoDTQ
 * @author UTC.KongLtn
 * @lastUpdate Dec 17, 2015
 */
Class ObservationController extends Controller
{
    /**
     * Max width of image comment
     * @var int
     */
    public $maxWidthImg = 800;

    /**
     * Filter and limit user if user does not login
     * @returns undefined
     */
    public function filters()
    {
        if (Yii::app()->user->isGuest) {
            $this->redirect(Yii::app()->getBaseUrl() . "/auth/user/login");
        }
    }

    /**
     * Check if session is activated and not past then redirect to activeDetail page, else plannedDetail page
     * @returns undefined
     */
    public function actionDetail()
    {
        $idSession = Yii::app()->request->getParam('id');

        // Check permission & redirect if not permission
        $userId = Yii::app()->user->idUser;
        if (!Sessions::model()->checkPermission($userId, $idSession)) {
            $this->redirect('/observation');
        }

        //update notify session is read and comment in it is read
        NotifyUser::model()->updateAll(array('isRead' => 1), 'idLink = "SESS' . (int)$idSession . '" AND isRead = 0 AND userId =' . Yii::app()->user->idUser);
        //Get all comment of sesion
        $commentsData = Comments::model()->findAllByAttributes(array("idSession" => $idSession));
        foreach ($commentsData as $comment) {
            NotifyUser::model()->updateAll(array('isRead' => 1), 'idLink = "COMM' . (int)$comment['idComment'] . '" AND isRead = 0 AND userId =' . Yii::app()->user->idUser);
        }
        //end update notify session is read and comment in it is read        
        $session = Sessions::model()->findByPk($idSession);
        if ((int)$session['active'] === 1) {
            $this->forward('/observation/activeDetail/id' . $idSession);
        } else {
            $this->forward('/observation/plannedDetail/id' . $idSession);
        }
    }

    /**
     * Limit description of session in observation screen
     * @param $string
     * @return string description after limit to show in observation screen
     */
    function limitShow($string)
    {
        $string = strip_tags($string);
        if (strlen($string) > 120) {
            $stringCut = substr($string, 0, 120);
            $string = substr($stringCut, 0, strrpos($stringCut, ' '));
        }
        return $string;
    }

    /**
     * Show all planned and past session
     * @returns undefined
     */
    public function actionIndex()
    {
        $this->titleInHeaderBar = "Observation";
        $dataPlanned = InvitedSession::model()->getSessionInPlannedTabObservation();
        foreach ($dataPlanned as $key => $row) {
            $dataPlanned[$key]['timeElapse'] = $this->getTiming($row['dateCreate']);
            $dataPlanned[$key]['description'] = $this->limitShow($row['description']);
            $dataPlanned[$key]['numComment'] = Comments::model()->getNumComment((int)$row['idSession']);
            $dataPlanned[$key]['invitedUsers'] = InvitedSession::model()->getInfoOfInvitedUsers((int)$row['idSession']);
            $dataPlanned[$key]['archives'] = ArchiveSession::model()->getArchivesBySessionId((int)$row['idSession']);
        }
        $dataActive = InvitedSession::model()->getSessionInActiveTabObservation();
        foreach ($dataActive as $key => $row) {
            $dataActive[$key]['timeElapse'] = $this->getTiming($row['dateCreate']);
            $dataActive[$key]['description'] = $this->limitShow($row['description']);
            $dataActive[$key]['numComment'] = Comments::model()->getNumComment((int)$row['idSession']);
            $dataActive[$key]['invitedUsers'] = InvitedSession::model()->getInfoOfInvitedUsers((int)$row['idSession']);
            $dataActive[$key]['archives'] = ArchiveSession::model()->getArchivesBySessionId((int)$row['idSession']);
        }
        $dataPast = InvitedSession::model()->getSessionInPastTabObservation();
        foreach ($dataPast as $key => $row) {
            $dataPast[$key]['timeElapse'] = $this->getTiming($row['dateCreate']);
            $dataPast[$key]['description'] = $this->limitShow($row['description']);
            $dataPast[$key]['numComment'] = Comments::model()->getNumComment((int)$dataPast[$key]['idSession']);
            $dataPast[$key]['invitedUsers'] = InvitedSession::model()->getInfoOfInvitedUsers((int)$row['idSession']);
            $dataPast[$key]['archives'] = ArchiveSession::model()->getArchivesBySessionId((int)$row['idSession']);
        }
        $this->render('index', array(
            'dataPlanned' => $dataPlanned,
            'dataActive' => $dataActive,
            'dataPast' => $dataPast
        ));
    }

    /**
     * Show detail of a planned session
     * @returns undefined
     */
    public function actionPlannedDetail()
    {
        $id = Yii::app()->request->getParam('id');
        $userId = Yii::app()->user->idUser;
        if (!Sessions::model()->checkPermission($userId, $id)) {
            $this->forward('/site/permission');
        } else {
            $dataPlannedDetail = InvitedSession::model()->getDetail($id)->read();
            //$dataPlannedDetail['timeElapse'] = strtotime($dataPlannedDetail['dateCreate']);
            $dataPlannedDetail['timeElapse'] = $this->getTiming($dataPlannedDetail['dateCreate']);
            $this->showBackButtonInHeaderBar = true;
            $this->titleInHeaderBar = $dataPlannedDetail['title'];
            $documentNameDetail = ArchiveSession::model()->getDocumentName($id);

            //get data from comments table
            $dataPlannedComment = Comments::model()->getDataComment($id);
            foreach ($dataPlannedComment as $key => $row) {
                //$dataPlannedComment[$key]['timeElapse'] = strtotime($row['dateCreate']);
                $dataPlannedComment[$key]['timeElapse'] = $this->getTiming($row['dateCreate']);
                $dataPlannedComment[$key]['likedUserList'] = Likes::model()->getLikedUserList($row['idComment']);
            }
            $dataSetting = Setting::model()->find();
            $this->render('plannedDetail', array(
                'dataPlannedDetail' => $dataPlannedDetail,
                'documentNameDetail' => $documentNameDetail,
                'dataPlannedComment' => $dataPlannedComment,
                'dataSetting' => $dataSetting
            ));
        }
    }

    /**
     * Show detail of a past session
     * @returns undefined
     */
    public function actionPastDetail()
    {
        $id = Yii::app()->request->getParam('id');
        $userId = Yii::app()->user->idUser;

        // Check permission
        if (!Sessions::model()->checkPermission($userId, $id)) {
            $this->forward('/site/permission');
        } else {
            //update notify session is read and comment in it is read
            NotifyUser::model()->updateAll(array('isRead' => 1),
                'idLink = "SESS' . (int)$id . '" AND isRead = 0 AND userId =' . $userId);
            //Get all comment of session
            $commentsData = Comments::model()->findAllByAttributes(array("idSession" => $id));
            foreach ($commentsData as $comment) {
                NotifyUser::model()->updateAll(array('isRead' => 1), 'idLink = "COMM' . (int)$comment['idComment'] . '" AND isRead = 0 AND userId =' . Yii::app()->user->idUser);
            }

            $dataPastDetail = InvitedSession::model()->getDetail($id)->read();
            $dataPastDetail['timeElapse'] = $this->getTiming($dataPastDetail['dateCreate']);
            $this->showBackButtonInHeaderBar = true;
            $this->titleInHeaderBar = $dataPastDetail['title'];

            $documentNameDetail = ArchiveSession::model()->getDocumentName($id);

            //get data from comments table
            $dataPastComment = Comments::model()->getDataComment($id, "ASC");
            foreach ($dataPastComment as $key => $row) {
//                $dataPastComment[$key]['timeElapse'] = strtotime($row['dateCreate']);
                $dataPastComment[$key]['timeElapse'] = $this->getTiming($row['dateCreate']);
                //$dataPastComment[$key]['likedUserList'] = Likes::model()->getLikedUserList($row['idComment']);
            }

            $this->render('pastDetail', array(
                'dataPastDetail' => $dataPastDetail,
                'documentNameDetail' => $documentNameDetail,
                'dataPastComment' => $dataPastComment,
            ));
        }
    }

    /**
     * Show detail of an active session
     * @returns undefined
     */
    public function actionActiveDetail()
    {
        $id = Yii::app()->request->getParam('id');
        $userId = Yii::app()->user->idUser;

        if (!Sessions::model()->checkPermission($userId, $id)) {
            $this->forward('/site/permission');
        } else {
            $dataActiveDetail = InvitedSession::model()->getActiveDetail($id)->read();
            //$dataActiveDetail['timeElapse'] = strtotime($dataActiveDetail['dateCreate']);
            $dataActiveDetail['timeElapse'] = $this->getTiming($dataActiveDetail['dateCreate']);
            $dataActiveDetail['description'] = $this->limitShow($dataActiveDetail['description']);
            $this->showBackButtonInHeaderBar = true;
            $this->titleInHeaderBar = $dataActiveDetail['title'];
            $documentNameDetail = ArchiveSession::model()->getDocumentName($id);
            // Update isRead for all comment and like


            //get data from comments table
            $dataComment = Comments::model()->getDataComment($id);
            foreach ($dataComment as $key => $row) {
                //$dataComment[$key]['timeElapse'] = strtotime($row['dateCreate']);
                $dataComment[$key]['timeElapse'] = $this->getTiming($row['dateCreate']);
                $dataComment[$key]['likedUserList'] = Likes::model()->getLikedUserList($row['idComment']);
            }
            $dataSetting = Setting::model()->find();
            $this->render('activeDetail', array(
                'dataActiveDetail' => $dataActiveDetail,
                'dataComment' => $dataComment,
                'dataSetting' => $dataSetting,
                'documentNameDetail' => $documentNameDetail,
            ));
        }
    }


    /**
     * Action like and unlike
     * @returns undefined
     */
    public function actionLike()
    {
        $cmtId = Yii::app()->request->getParam('id');
        $userId = Yii::app()->user->idUser;
        $isLiked = Likes::model()->checkLiked($userId, $cmtId);
        $content = '';
        $arrayUserInvited = [];
        $notifyId = null;
        $idSession = 0;
        if (!$isLiked) {
            try {

                // Like
                $model = new Likes;
                $model->idUserLike = $userId;
                $model->idComment = $cmtId;
                $count = Likes::model()->count();

                while (Likes::model()->findByPk((int)$count) !== null) {
                    $count++;
                };
                $model->idLike = $count;
                $model->save();

                // Get Comment data
                $comment = Comments::model()->findByPk($cmtId);
                $idSession = $comment->idSession;
                if ($comment->contentMediaType == 'TEXT') {
                    $content = ' liked a comment: ' . $comment->content;
                }
                if ($comment->contentMediaType == 'PICTURE') {
                    $content = ' liked a picture comment';
                }
                if ($comment->contentMediaType == 'VIDEO') {
                    $content = ' liked a video comment';
                }

                //Add Notify
                $notify = new Notify();
                $notify->createUserId = $userId;
                $notify->dateCreate = date("Y-m-d H:i:s");
                $notify->typeNotify = 'LIKE';
                $notify->content = $content;
                $notify->link = (int)$cmtId;
                $notify->seconds = time();
                $notify->save();
                $notifyId = $notify->notifyId;
                //End add notify

                // Add Notify User
                $dataIdUser = InvitedSession::model()->getIdUserCreateAndInvitedUser($idSession);
                $arrayUserInvited = $this->arrayColumn($dataIdUser, 'idUser');
                foreach ($dataIdUser as $rowInvited) {
                    if ($rowInvited['idUser'] != $userId) {
                        $notifyUser = new NotifyUser();
                        $notifyUser->userId = (int)$rowInvited['idUser'];
                        $notifyUser->notifyId = (int)$notify->notifyId;
                        $notifyUser->dateRead = date("Y-m-d H:i:s");
                        $notifyUser->idLink = 'LIKE' . (int)$comment->idComment;
                        $notifyUser->save();
                    }
                }
                // # Add Notify User

            } catch (Exception $e) {
                echo $e->getMessage();
                exit;
            }
        } else {
            Likes::model()->deleteAllByAttributes(array("idUserLike" => $userId, "idComment" => $cmtId));
        }

        $result = array(
            'avaPath' => Yii::app()->user->avatarPath,
            'content' => $content,
            'invitedUser' => $arrayUserInvited,
            'fullNameOfCreator' => Yii::app()->user->firstName . ' ' . Yii::app()->user->lastName,
            'ownerId' => Yii::app()->user->idUser,
            'gender' => (Yii::app()->user->gender == 1) ? "his" : "her",
            'time' => time(),
            'type' => 'like',
            'notifyId' => $notifyId,

            'countLike' => Likes::model()->countByAttributes(array("idComment" => $cmtId)),
            'likeText' => $isLiked ? "Like" : "Unlike",
            'idSession' => $idSession,
            'idComment' => $cmtId,
            'redirect' => 'observation/detail/id/' . $idSession . "#commentRow" . $cmtId
        );
        echo json_encode((object)$result);
    }


    /**
     * Get Like Text or Unlike Text by userId and cmtId
     * @param integer $cmtId
     * @return string Unlike or Like
     */
    public function getLikeTxt($cmtId)
    {
        if (Yii::app()->user->isGuest) return "Like";
        $userId = Yii::app()->user->idUser;
        $isLiked = Likes::model()->checkLiked($userId, $cmtId);
        return ($isLiked) ? "Unlike" : "Like";
    }

    /**
     * Create new text comment
     * @returns undefined
     */
    public function actionCreateComment()
    {
        if (Yii::app()->request->isAjaxRequest) {
            //get data from comment-form and insert into comments table
            $id = Yii::app()->request->getParam('id');
            try {
                $modelComment = new Comments;
                $modelComment->idUserComment = Yii::app()->user->idUser;
                $modelComment->idSession = $id;
                $modelComment->content = Yii::app()->request->getParam('content');
                $modelComment->contentMediaType = "TEXT";
                $modelComment->dateCreate = date("Y-m-d H:i:s");
                $modelComment->lastUpdate = date("Y-m-d H:i:s");
                $modelComment->idCommentParent = Yii::app()->request->getParam('parent_id');
                $modelComment->save();

                //Add Notify
                $notifyNew = new Notify();
                $notifyNew->createUserId = Yii::app()->user->idUser;
                $notifyNew->dateCreate = date("Y-m-d H:i:s");
                $notifyNew->typeNotify = 'COMM';
                $notifyNew->content = "Added a comment:<br>" . Yii::app()->request->getParam('content');
                $notifyNew->link = (int)$modelComment->idComment;
                $notifyNew->seconds = time();
                $notifyNew->save();
                //End add notify

                $dataIdUser = InvitedSession::model()->getIdUserCreateAndInvitedUser($modelComment->idSession);
                $arrayUserInvited = $this->arrayColumn($dataIdUser, 'idUser');
                foreach ($dataIdUser as $rowInvited) {
                    if ($rowInvited['idUser'] != Yii::app()->user->idUser) {
                        //Send notify user
                        $notifyUserNew = new NotifyUser();
                        $notifyUserNew->userId = (int)$rowInvited['idUser'];
                        $notifyUserNew->notifyId = (int)$notifyNew->notifyId;
                        $notifyUserNew->dateRead = date("Y-m-d H:i:s");
                        $notifyUserNew->idLink = 'COMM' . (int)$modelComment->idComment;
                        $notifyUserNew->save();
                        //End send notify user 
                    }
                }
                //$ses = Sessions::model()->findByPk((int) $modelComment->idSession);
                $dataReturn = array(
                    'avaPath' => Yii::app()->user->avatarPath,
                    'content' => "Added a comment:<br>" . Yii::app()->request->getParam('content'),
                    'invitedUser' => $arrayUserInvited,
                    'fullNameOfCreator' => Yii::app()->user->firstName . ' ' . Yii::app()->user->lastName,
                    'ownerId' => Yii::app()->user->idUser,
                    'gender' => (Yii::app()->user->gender == 1) ? "his" : "her",
                    'time' => time(),
                    'type' => 'comm',
                    'notifyId' => $notifyNew->notifyId,
                    'redirect' => 'observation/detail/id/' . $id . "#commentRow" . $modelComment->idComment,

                    'status' => 'true',
                    'typeComment' => "text",
                    'typeAction' => "add",
                    'idComment' => $modelComment->idComment,
                    'idSession' => $modelComment->idSession,
                    'idCommentParent' => (int)$modelComment->idCommentParent
                    //'sessionUserName' => Users::model()->getFullName($ses->idUserCreate),
                    //'sessionInvited'=>$arrayUserInvited,
                );
                echo json_encode($dataReturn);
                exit;
            } catch (Exception $e) {
                echo $e->getMessage();
                exit;
            }
        } else {
            $this->forward('/site/error');
        }
    }

    /**
     * Edit text comment
     * @returns undefined
     */
    public function actionEditComment()
    {
        if (Yii::app()->request->isAjaxRequest) {
            $id = Yii::app()->request->getParam('idComment');
            $modelCommentEdit = Comments::model()->findByPk((int)$id);
            $modelCommentEdit->content = Yii::app()->request->getParam('content');
            $modelCommentEdit->lastUpdate = date("Y-m-d H:i:s");
            $modelCommentEdit->save();
            $dataIdUser = InvitedSession::model()->getIdUserCreateAndInvitedUser($modelCommentEdit->idSession);
            $arrayUserInvited = $this->arrayColumn($dataIdUser, 'idUser');

            //Add Notify
            $notifyNew = new Notify();
            $notifyNew->createUserId = Yii::app()->user->idUser;
            $notifyNew->dateCreate = date("Y-m-d H:i:s");
            $notifyNew->typeNotify = 'COMM';
            $notifyNew->content = "Edited a comment:<br>" . Yii::app()->request->getParam('content');
            $notifyNew->link = (int)$modelCommentEdit->idComment;
            $notifyNew->seconds = time();
            $notifyNew->save();
            //End add notify

            $dataReturn = array(
                'avaPath' => Yii::app()->user->avatarPath,
                'content' => "Edited a comment:<br>" . Yii::app()->request->getParam('content'),
                'invitedUser' => $arrayUserInvited,
                'fullNameOfCreator' => Yii::app()->user->firstName . ' ' . Yii::app()->user->lastName,
                'ownerId' => Yii::app()->user->idUser,
                'gender' => (Yii::app()->user->gender == 1) ? "his" : "her",
                'time' => time(),
                'type' => 'comm',
                'notifyId' => $notifyNew->notifyId,
                'redirect' => 'observation/detail/id/' . $modelCommentEdit->idSession . "#commentRow" . $modelCommentEdit->idComment,

                'status' => 'true',
                'typeComment' => "text",
                'typeAction' => "edit",
                'idComment' => $modelCommentEdit->idComment,
                'idSession' => $modelCommentEdit->idSession,
                'idCommentParent' => (int)$modelCommentEdit->idCommentParent
            );
            echo json_encode($dataReturn);
            exit;
        } else {
            $this->forward('/site/error');
        }
    }

    /**
     * Reload comment
     * @returns undefined
     */
    public function actionReloadComment()
    {
        if (Yii::app()->request->isAjaxRequest) {
            $idComment = Yii::app()->request->getParam('commentId');
            $dataComment = Comments::model()->getDetailComment($idComment);
            $dataComment['timeElapse'] = $this->getTiming($dataComment['dateCreate']);
            $dataComment['likedUserList'] = Likes::model()->getLikedUserList($idComment);
            $dataCommentReply = Comments::model()->getCommentReplyWithIdCommentParent($idComment);
            $dataSetting = Setting::model()->find();
            $this->renderPartial('comment', array(
                    'itemComment' => $dataComment,
                    'dataCommentReply' => $dataCommentReply,
                    'dataSetting' => $dataSetting)
            );
        } else {
            $this->forward('/site/error');
        }
    }

    /**
     * Get content of existing text comment to edit
     * @returns undefined
     */
    public function actionGetContent()
    {
        try {
            $commentId = Yii::app()->request->getParam('commentId');
            $data = Comments::model()->findByPk((int)$commentId);
            echo json_encode($data->attributes);
        } catch (Exception $ex) {
            echo $ex->getMessage();
            exit;
        }
    }

    /**
     * Get path of existing image comment to edit
     * @returns undefined
     */
    public function actionGetContentMediaPath()
    {
        try {
            $imageCommentId = Yii::app()->request->getParam('imageCommentId');
            $contentMediaPath = Comments::model()->findByPk((int)$imageCommentId);
            echo json_encode($contentMediaPath->attributes);
        } catch (Exception $ex) {
            echo $ex->getMessage();
            exit;
        }
    }

    /**
     * Delete comment
     * @returns undefined
     */
    public function actionDeleteComment()
    {
        $commentId = Yii::app()->request->getParam('commentId');
        try {
            Comments::model()->deleteByPk($commentId);
            Comments::model()->deleteAllByAttributes(array("idCommentParent" => $commentId));

            Notify::model()->deleteAll('link = ' . (int)$commentId . ' and typeNotify = "COMM"');
            NotifyUser::model()->deleteAll('idLink = "COMM' . (int)$commentId . '"');
            echo "success";
        } catch (Exception $e) {
            echo $e->getMessage();
            exit;
        }
    }

    /**
     * Get data from image comment form and save to comments table
     * @returns undefined
     */
    public function actionCreateImageComment()
    {
        $maxWidth = $this->maxWidthImg;
        $id = Yii::app()->request->getParam('idSessionCommentImage');
        try {
            if (isset($_FILES['imagePath']) && $_FILES['imagePath']["name"] != ''
                && $_FILES['imagePath']["tmp_name"] != ''
                && $_FILES['imagePath']['error'] == 0
            ) {

                $imgFile = $_FILES['imagePath'];
                $fileUpload = explode(".", $imgFile['name']);
                reset($fileUpload);
                $fileName = current($fileUpload);
                $fileExt = strtolower(end($fileUpload));
                $md5Name = md5($fileName . date('Y-m-d H:i:s') . microtime());
                $fileNameNew = $md5Name . '.' . $fileExt;

                $path = Yii::getpathOfAlias('webroot') . Yii::app()->params["fileImagesFolderPath"] . '/' . $fileNameNew;
                $folderPath = Yii::getpathOfAlias('webroot') . Yii::app()->params["fileImagesFolderPath"] . '/';

                $size = getimagesize($imgFile['tmp_name']);
                $width = $size[0];

                if (move_uploaded_file($imgFile['tmp_name'], $path)) {
                    // Resize image if width > maxWidth
                    if ($width > $maxWidth) {
                        $newPic = $this->createThumbnailImage($folderPath, $path, $maxWidth);
                        if ($newPic && $newPic != "") {
                            $fileNameNew = $md5Name . '_thumbnail.' . $fileExt;
                            if (file_exists($path)) {
                                unlink($path);
                            }
                        }
                    }

                    // Add image comment
                    $modelImgComment = new Comments;
                    $modelImgComment->idUserComment = Yii::app()->user->idUser;
                    $modelImgComment->idSession = $id;
                    $modelImgComment->contentMediaType = "PICTURE";
                    $modelImgComment->dateCreate = date("Y-m-d H:i:s");
                    $modelImgComment->lastUpdate = date("Y-m-d H:i:s");
                    $modelImgComment->contentMediaPath = $fileNameNew;
                    $modelImgComment->save();

                    //Add Notify
                    $notifyNew = new Notify();
                    $notifyNew->createUserId = Yii::app()->user->idUser;
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
                        if ($rowInvited['idUser'] != Yii::app()->user->idUser) {
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

                    $dataReturn = array(
                        'avaPath' => Yii::app()->user->avatarPath,
                        'content' => "Added a picture comment",
                        'invitedUser' => $arrayUserInvited,
                        'fullNameOfCreator' => Yii::app()->user->firstName . ' ' . Yii::app()->user->lastName,
                        'ownerId' => Yii::app()->user->idUser,
                        'gender' => (Yii::app()->user->gender == 1) ? "his" : "her",
                        'time' => time(),
                        'type' => 'comm',
                        'notifyId' => $notifyNew->notifyId,
                        'redirect' => 'observation/detail/id/' . $id . "#commentRow" . $modelImgComment->idComment,

                        'status' => 'true',
                        'typeComment' => "picture",
                        'typeAction' => "add",
                        'idComment' => $modelImgComment->idComment,
                        'idSession' => $modelImgComment->idSession,
                        'idCommentParent' => (int)$modelImgComment->idCommentParent,
                        'size' => $size
                    );
                    echo json_encode($dataReturn);
                    exit;
                }
            }
        } catch (Exception $ex) {
            echo $ex->getMessage();
            exit;
        }
        $this->redirect(Yii::app()->request->urlReferrer);
    }

    public function actionTestUploadImage()
    {
        $fileContent = Yii::app()->request->getParam('content');
        $fileImagesFolderPath = '/upload'; //Yii::app()->params['fileImagesFolderPath'];
        $fileExt = "jpg";
        $md5Name = md5(time());
        $pathNew = $fileImagesFolderPath . "/" . $md5Name . '.' . $fileExt;
        $filePath = Yii::getpathOfAlias('webroot') . $pathNew;

        file_put_contents($filePath, base64_decode($fileContent));
        echo $fileContent;


//        $maxWidth = $this->maxWidthImg;
//
//        if (isset($_FILES['imagePath']) && $_FILES['imagePath']["name"] != ''
//            && $_FILES['imagePath']["tmp_name"] != ''
//            && $_FILES['imagePath']['error'] == 0
//        ) {
//
//            $imgFile = $_FILES['imagePath'];
//            $fileUpload = explode(".", $imgFile['name']);
//            reset($fileUpload);
//            $fileName = current($fileUpload);
//            $fileExt = strtolower(end($fileUpload));
//            $md5Name = md5($fileName . date('Y-m-d H:i:s') . microtime());
//            $fileNameNew = $md5Name . '.' . $fileExt;
//
//            $path = Yii::getpathOfAlias('webroot') . Yii::app()->params["fileImagesFolderPath"] . '/' . $fileNameNew;
//            $folderPath = Yii::getpathOfAlias('webroot') . Yii::app()->params["fileImagesFolderPath"] . '/';
//
//            $size = getimagesize($imgFile['tmp_name']);
//            $width = $size[0];
//
//            if (move_uploaded_file($imgFile['tmp_name'], $path)) {
//                // Resize image if width > maxWidth
//                if ($width > $maxWidth) {
//                    $newPic = $this->createThumbnailImage($folderPath, $path, $maxWidth);
//                    if ($newPic && $newPic != "") {
//                        $fileNameNew = $md5Name . '_thumbnail.' . $fileExt;
//                        if (file_exists($path)) {
//                            unlink($path);
//                        }
//                    }
//                }
//            }
//        }

    }

    /**
     * Edit data image comment and save to comments table
     * @returns undefined
     */
    public function actionEditImageComment()
    {
        $maxWidth = $this->maxWidthImg;
        $idComment = Yii::app()->request->getParam('idCommentImage');
        try {
            $modelImageCommentEdit = Comments::model()->findByPk((int)$idComment);
            $modelImageCommentEdit->lastUpdate = date("Y-m-d H:i:s");
            $oldImage = $modelImageCommentEdit->contentMediaPath;
            if (isset($_FILES['imagePath']) && $_FILES['imagePath']["name"] != ''
                && $_FILES['imagePath']["tmp_name"] != ''
                && $_FILES['imagePath']['error'] == 0
            ) {
                $imgFileEdit = $_FILES['imagePath'];
                $size = getimagesize($imgFileEdit['tmp_name']);
                $width = $size[0];
                $fileUpload = explode(".", $imgFileEdit['name']);
                reset($fileUpload);
                $fileName = current($fileUpload);
                $fileExt = strtolower(end($fileUpload));
                $md5Name = md5($fileName . date('Y-m-d H:i:s') . microtime());
                $fileNameNew = $md5Name . '.' . $fileExt;
                $path = Yii::getpathOfAlias('webroot') . Yii::app()->params["fileImagesFolderPath"] . '/';

                if (move_uploaded_file($imgFileEdit['tmp_name'], $path . $fileNameNew)) {
                    // Resize image if width > maxWidth
                    if ($width > $maxWidth) {
                        $newPic = $this->createThumbnailImage($path, $path . $fileNameNew, $maxWidth);
                        if ($newPic && $newPic != "") {
                            if (file_exists($path . $fileNameNew)) {
                                unlink($path . $fileNameNew);
                            }
                            $fileNameNew = $md5Name . '_thumbnail.' . $fileExt;
                        }
                    }
                    // Remove old image
                    $modelImageCommentEdit->contentMediaPath = $fileNameNew;
                    if (isset($oldImage) && $oldImage !== "" && file_exists($path . $oldImage)) {
                        unlink($path . $oldImage);
                    }
                }
            }
            $modelImageCommentEdit->save();
            //Add Notify
            $notifyNew = new Notify();
            $notifyNew->createUserId = Yii::app()->user->idUser;
            $notifyNew->dateCreate = date("Y-m-d H:i:s");
            $notifyNew->typeNotify = 'COMM';
            $notifyNew->content = 'Edited a picture comment';
            $notifyNew->link = (int)$modelImageCommentEdit->idComment;
            $notifyNew->seconds = time();
            $notifyNew->save();
            //End add notify
            $dataIdUser = InvitedSession::model()->getIdUserCreateAndInvitedUser($modelImageCommentEdit->idSession);
            $arrayUserInvited = $this->arrayColumn($dataIdUser, 'idUser');
            $dataReturn = array(
                'avaPath' => Yii::app()->user->avatarPath,
                'content' => "Edited a picture comment",
                'invitedUser' => $arrayUserInvited,
                'fullNameOfCreator' => Yii::app()->user->firstName . ' ' . Yii::app()->user->lastName,
                'ownerId' => Yii::app()->user->idUser,
                'gender' => (Yii::app()->user->gender == 1) ? "his" : "her",
                'time' => time(),
                'type' => 'comm',
                'notifyId' => $notifyNew->notifyId,
                'redirect' => 'observation/detail/id/' . $modelImageCommentEdit->idSession . "#commentRow" . $modelImageCommentEdit->idComment,

                'status' => 'true',
                'typeComment' => "picture",
                'typeAction' => "edit",
                'idComment' => $modelImageCommentEdit->idComment,
                'idSession' => $modelImageCommentEdit->idSession,
                'idCommentParent' => (int)$modelImageCommentEdit->idCommentParent
            );
            echo json_encode($dataReturn);
            exit;
        } catch (Exception $ex) {
            echo $ex->getMessage();
            exit;
        }
        //$this->redirect(Yii::app()->request->urlReferrer);
    }
}