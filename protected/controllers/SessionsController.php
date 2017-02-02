<?php

/**
 * Controller for Screen Create/Edit Session
 * Class SessionsController
 * @author UTC.HuyTD
 * @author UTC.BaoDTQ
 * @author UTC.KongLtn
 * Last Update  Nov 19, 2015
 */
Class SessionsController extends Controller
{

    /**
     * Filter and limit user if user does not login
     * @return undefined
     */
    public function filters()
    {
        if (Yii::app()->user->isGuest) {
            $this->redirect(Yii::app()->getBaseUrl() . "/auth/user/login");
        }
    }

    /**
     * Filter and limit user if user don't create session can't edit session
     * @returns true if id current user is id user create current session
     */
    function checkPermissionEditWithIdUser()
    {
        $idSession = Yii::app()->request->getParam('id');
        $idUser = Yii::app()->user->idUser;
        $dataIdUserEdit = Sessions::model()->getIdUserCreate($idSession)->read();
        if ((int)$idUser != (int)$dataIdUserEdit['idUserCreate']) {
            return 'false';
        }
        return 'true';
    }

    /**
     * Get data from form and create a session
     * @return undefined
     */
    public function actionCreate()
    {
        $this->showBackButtonInHeaderBar = true;
        $this->titleInHeaderBar = "Planning";
        //Get data from user table
        $usersModel = new Users;
        $dataUsers = $usersModel->getUserInvited();
        //Get data from topic table
        $topicsModel = new Topics;
        $dataTopics = $topicsModel->findAll();
        //Get data from archive table
        $archivesModel = new Archives;
        $dataArchives = $archivesModel->getAllByUserId(Yii::app()->user->idUser);
        $this->render('create',
            array(
                'dataUsers' => $dataUsers,
                'dataTopics' => $dataTopics,
                'dataArchives' => $dataArchives
            )
        );
    }

    /**
     * Edit session from an existing session
     * @returns undefined
     */
    public function actionEdit()
    {
        if ($this->checkPermissionEditWithIdUser() == "false") {
            $this->forward('/site/error');
        }

        $this->showBackButtonInHeaderBar = true;
        $this->titleInHeaderBar = "Planning";
        //Get data from user table
        $usersModel = new Users;
        $dataUsers = $usersModel->getUserInvited();
        //Get data from topic table
        $topicsModel = new Topics;
        $dataTopics = $topicsModel->findAll();
        //Get data from archive table
        $archivesModel = new Archives;
        $dataArchives = $archivesModel->getAllByUserId(Yii::app()->user->idUser);
        //Get data from param name
        $id = Yii::app()->request->getParam('id');
        //Get data from current session 
        $dataDetailEdit = InvitedSession::model()->getDetailEdit($id)->read();
        $idUserInvited = InvitedSession::model()->getIdUserInvited($id);
        $idArchiveEdit = InvitedSession::model()->getIdArchiveEdit($id);
        $documentNameDetail = ArchiveSession::model()->getDocumentName($id);
        $this->render('edit', array(
            'dataUsers' => $dataUsers,
            'dataTopics' => $dataTopics,
            'dataArchives' => $dataArchives,
            'dataDetailEdit' => $dataDetailEdit,
            'documentNameDetail' => $documentNameDetail,
            'idUserInvited' => $idUserInvited,
            'id' => $id,
            'idArchiveEdit' => $idArchiveEdit,
        ));
    }

    /**
     * Save a session
     * @return undefined
     */
    public function actionSaveSession()
    {
        if (Yii::app()->request->isAjaxRequest) {
            //Get data from param
            $title = Yii::app()->request->getParam('title');
            $idTopic = Yii::app()->request->getParam('topic');
            $id = Yii::app()->request->getParam('id');
            $sessionId = $id;
            $userInvites = Yii::app()->request->getParam('userInvited');
            $typeAction = null;
            $position = null;
            $notifyId = 0;
            $status = 'false';
            if (trim($title) != "" && (int)$idTopic > 0) {
                $transaction = Yii::app()->db->beginTransaction();
                $modelSession = null;
                try {
                    if ((int)$id > 0) {
                        //edit session
                        if ($this->checkPermissionEditWithIdUser() == "false") {
                            $this->forward('/site/error');
                        }
                        //save data from form to sessions table
                        $modelSession = Sessions::model()->findByPk($id);
                        $modelSession->lastUpdate = date("Y-m-d H:i:s");
                        $modelSession->title = $title;
                        $modelSession->datePost = date('Y-m-d', strtotime(Yii::app()->request->getParam('date')));
                        $modelSession->idTopic = $idTopic;
                        $modelSession->description = Yii::app()->request->getParam('content');
                        $modelSession->save();

                        $invitedUserOldData = InvitedSession::model()->findAll('idSession=:idSession', array(':idSession' => $id));
                        $invitedUserOld = $this->arrayColumn($invitedUserOldData, 'idUserInvited');
                        //get old notify to update
                        $notify = Notify::model()->find('link = :link and typeNotify = "SESS"', array(':link' => $id));
                        InvitedSession::model()->deleteAll('idSession=:idSession', array(':idSession' => $id));
                        if (is_array($userInvites) && count($userInvites) > 0) {
                            foreach ($userInvites as $userInvite) {
                                //save data from form to invited_table
                                $modelInvitedSession = new InvitedSession;
                                $modelInvitedSession->idSession = $modelSession->idSession;
                                $modelInvitedSession->idUserInvited = $userInvite;
                                $modelInvitedSession->save();
                                if (!in_array($userInvite, $invitedUserOld)) {
                                    //send notify user
                                    $notifyUserNew = new NotifyUser();
                                    $notifyUserNew->userId = (int)$userInvite;
                                    $notifyUserNew->notifyId = (int)$notify->notifyId;
                                    $notifyUserNew->dateRead = date("Y-m-d H:i:s");
                                    $notifyUserNew->idLink = 'SESS' . (int)$modelSession->idSession;
                                    $notifyUserNew->save();
                                    //End send notify user
                                }
                            }
                        }
                        foreach ($invitedUserOld as $userOld) {
                            if (!in_array($userOld, $userInvites)) {
                                //delete in notify user
                                NotifyUser::model()->deleteAll('idLink = "SESS' . (int)$id . '" and idUser =' . Yii::app()->user->idUser);
                                $commentsData = Comments::model()->findAllByAttributes(array("idSession" => $id));
                                foreach ($commentsData as $comment) {
                                    NotifyUser::model()->deleteAll('idLink = "COMM' . (int)$comment['idComment'] . '" and idUser =' . Yii::app()->user->idUser);
                                }
                            }
                        }

                        ArchiveSession::model()->deleteAll('idSession=:idSession', array(':idSession' => $id));
                        $dataDocuments = Yii::app()->request->getParam('archive');
                        if (is_array($dataDocuments) && count($dataDocuments) > 0) {
                            foreach ($dataDocuments as $dataDocument) {
                                //save data from form to archive_session table
                                $modelArchiveSession = new ArchiveSession;
                                $modelArchiveSession->idSession = $modelSession->idSession;
                                $modelArchiveSession->idArchive = $dataDocument;
                                $modelArchiveSession->save();
                            }
                        }
                        //$id = $modelSession->idSession;
                        $transaction->commit();
                        $typeAction = "edit";
                        $position = "";
                        $status = 'true';
                        //end edit session
                    } else {
                        //add session
                        //save data from form to sessions table
                        $modelSession = new Sessions;
                        $modelSession->idUserCreate = Yii::app()->user->idUser;
                        $modelSession->dateCreate = date("Y-m-d H:i:s");
                        $modelSession->lastUpdate = date("Y-m-d H:i:s");
                        $modelSession->title = $title;
                        $modelSession->datePost = date('Y-m-d', strtotime(Yii::app()->request->getParam('date')));
                        $modelSession->idTopic = $idTopic;
                        $modelSession->description = Yii::app()->request->getParam('content');
                        $modelSession->save();
                        $sessionId = $modelSession->idSession;

                        //Add Notify
                        $notifyNew = new Notify();
                        $notifyNew->createUserId = Yii::app()->user->idUser;
                        $notifyNew->dateCreate = date("Y-m-d H:i:s");
                        $notifyNew->typeNotify = 'SESS';
                        $notifyNew->content = 'Invited you to a session.';
                        $notifyNew->link = (int)$modelSession->idSession;
                        $notifyNew->seconds = time();
                        $notifyNew->save();
                        $notifyId = $notifyNew->notifyId;
                        //End add notify

                        if (is_array($userInvites) && count($userInvites) > 0) {
                            foreach ($userInvites as $userInvite) {
                                //save data from form to invited_table
                                $modelInvitedSession = new InvitedSession;
                                $modelInvitedSession->idSession = $modelSession->idSession;
                                $modelInvitedSession->idUserInvited = $userInvite;
                                $modelInvitedSession->save();

                                //Send notify user
                                $notifyUserNew = new NotifyUser();
                                $notifyUserNew->userId = (int)$userInvite;
                                $notifyUserNew->notifyId = (int)$notifyNew->notifyId;
                                $notifyUserNew->dateRead = date("Y-m-d H:i:s");
                                $notifyUserNew->idLink = 'SESS' . (int)$modelSession->idSession;
                                $notifyUserNew->save();
                                //End send notify user                        
                            }
                        }

                        $dataDocuments = Yii::app()->request->getParam('archive');
                        if (is_array($dataDocuments) && count($dataDocuments) > 0) {
                            foreach ($dataDocuments as $dataDocument) {
                                //save data from form to archive_session table
                                $modelArchiveSession = new ArchiveSession;
                                $modelArchiveSession->idSession = $modelSession->idSession;
                                $modelArchiveSession->idArchive = $dataDocument;
                                $modelArchiveSession->save();
                            }
                        }
                        $transaction->commit();
                        $typeAction = "add";
                        $position = "";
                        $status = 'true';
                        //end add session
                    }
                } catch (Exception $ex) {
                    echo $ex;
                    $transaction->rollback();
                }
                $dataReturn = array(
                    'avaPath' => Yii::app()->user->avatarPath,
                    'content' => "Invited you to a session.",
                    'invitedUser' => $userInvites,
                    'fullNameOfCreator' => Yii::app()->user->firstName . ' ' . Yii::app()->user->lastName,
                    'ownerId' => Yii::app()->user->idUser,
                    'gender' => (Yii::app()->user->gender == 1) ? "his" : "her",
                    'time' => time(),
                    'type' => 'sess',
                    'notifyId' => $notifyId,
                    'redirect' => 'observation/detail/id/' . $sessionId,

                    'status' => $status,
                    'position' => $position,
                    'typeAction' => $typeAction,
                    'idSession' => $modelSession->idSession
                );
                echo json_encode($dataReturn);
                exit;
            }
        } else {
            $this->forward('/site/error');
        }
    }

    /**
     * Action Join to a session
     * @return undefined
     */
    public function actionJoinTo()
    {
        $dataReturn = array(
            "status" => false,
            "userId" => -1,
            "sessionId" => -1
        );
        if (Yii::app()->request->isAjaxRequest) {
            $sesId = Yii::app()->request->getParam('session_id');
            $userId = Yii::app()->request->getParam('user_id');
            $currentId = Yii::app()->user->idUser;
            $role = Yii::app()->user->role;
            if (($role === "ADMIN" || $currentId === $userId) && InvitedSession::model()->checkUserIsInvited($sesId, $userId)) {
                try {
                    $invitedSession = InvitedSession::model()->findByAttributes(array(
                        "idSession" => $sesId,
                        "idUserInvited" => $userId
                    ));
                    $invitedSession->isJoined = 1;
                    $invitedSession->save();
                    $dataReturn["status"] = true;
                    $dataReturn["userId"] = $userId;
                    $dataReturn["sessionId"] = $sesId;
                } catch (Exception $e) {
                    echo $e->getMessage();
                }
            }
        }
        echo json_encode($dataReturn);
        exit;
    }

    /**
     * Dismiss a invitation
     * @return undefined
     */
    public function actionDismiss()
    {
        $dataReturn = array(
            "status" => false,
            "userId" => -1,
            "sessionId" => -1
        );
        if (Yii::app()->request->isAjaxRequest) {
            $sesId = Yii::app()->request->getParam('session_id');
            $userId = Yii::app()->request->getParam('user_id');
            $currentId = Yii::app()->user->idUser;
            $role = Yii::app()->user->role;
            if (($role === "ADMIN" || $currentId === $userId) && InvitedSession::model()->checkUserIsInvited($sesId, $userId)) {
                Sessions::model()->removeMemberFromSession($sesId, $userId);
                $dataReturn["status"] = true;
                $dataReturn["userId"] = $userId;
                $dataReturn["sessionId"] = $sesId;

            } else {
                echo "false:-1";
            }
        }
        echo json_encode($dataReturn);
        exit;
    }

    /**
     * Update isRead after user click joinTo or dismiss session
     * @return undefined
     */
    public function actionAfterClickJoinOrDismiss()
    {
        if (Yii::app()->request->isAjaxRequest) {
            $notifyId = Yii::app()->request->getParam("notify_id");
            $userId = Yii::app()->user->idUser;

            NotifyUser::model()->updateAll(
                array('isRead' => 1),
                'notifyId = ' . $notifyId . ' AND isRead = 0 AND userId =' . $userId
            );
        }
    }

    /**
     * Delete a session by owner
     */
    public function actionDelete()
    {
        if (Yii::app()->request->isAjaxRequest) {
            $sessionId = Yii::app()->request->getParam('id');
            if (Sessions::model()->checkOwner($sessionId)) {
                try {
                    // Remove Session
                    Sessions::model()->deleteByPk($sessionId);

                    Notify::model()->deleteAll('link = ' . (int)$sessionId . ' and typeNotify = "SESS"');
                    NotifyUser::model()->deleteAll('idLink = "SESS' . (int)$sessionId . '"');
                    InvitedSession::model()->deleteAll('idSession = ' . $sessionId);

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
            }
            $this->redirect(Yii::app()->getBaseUrl() . "/planning");
        }
    }

}
