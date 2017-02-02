<?php

/**
 * Controller for Screen Message
 * @author      UTC.HuyTD
 * @date        2015/10/20
 */
class MessagesController extends Controller
{

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
     * Show list messages of user
     * @returns undefined
     */
    public function actionIndex()
    {
        $this->titleInHeaderBar = "Messages";
        $usersInviteMessages = Users::model()->getUserInvited();
        $dataMessages = InvitedMessage::model()->getAllMessagesOfUser();

        //getDataReply
        foreach ($dataMessages as $keyMessage => $rowMessage) {
            $messageReply = array();
            if ((int)$rowMessage['idMessageReply'] > 0) {

                $continue = $rowMessage['idMessageReply'];
                while ((int)$continue > 0) {
                    $push = 0;
                    foreach ($dataMessages as $rowMessageReply) {
                        if ($continue == $rowMessageReply['idMessage']) {
                            array_push($messageReply, $rowMessageReply);
                            $continue = $rowMessageReply['idMessageReply'];
                            $push++;
                        }
                    }
                    if ($push === 0) {
                        $continue = 0;
                    }
                }
            }
            $dataMessages[$keyMessage]['messageReplyList'] = $messageReply;

            //Update is read in notify
            NotifyUser::model()->updateAll(array('isRead' => 1), 'idLink like "%MESS%" AND isRead = 0 AND userId =' . Yii::app()->user->idUser);
            //End update is read in notify
        }
        //end get data Reply

        $this->render('index', array('dataMessages' => $dataMessages, 'usersInviteMessages' => $usersInviteMessages));
    }

    /**
     * Create messages and send for other user
     * @returns undefined
     */
    public function actionCreate()
    {
        if (Yii::app()->request->isAjaxRequest) {
            $this->titleInHeaderBar = "Messages";

            try {
                //Create messages
                $content = Yii::app()->request->getParam('messagesContent');
                $dataReturn = array('idMessage' => 0);
                if (trim($content) !== "") {
                    $messagesNew = new Messages();
                    $messagesNew->idUserCreate = Yii::app()->user->idUser;
                    $messagesNew->content = nl2br($content);
                    $messagesNew->dateCreate = date("Y-m-d H:i:s");
                    $messagesNew->lastUpdate = date("Y-m-d H:i:s");
                    $messageReplyId = Yii::app()->request->getParam('idMessageReply');
                    if ((int)$messageReplyId > 0) {
                        $messagesNew->idMessageReply = $messageReplyId;
                    }
                    $messagesNew->save();

                    //Add Notify
                    $notifyNew = new Notify();
                    $notifyNew->createUserId = Yii::app()->user->idUser;
                    $notifyNew->dateCreate = date("Y-m-d H:i:s");
                    $notifyNew->typeNotify = 'MESS';
                    $notifyNew->content = $messagesNew->content; //'You have a message id:'.(int)$messagesNew->idMessage;
                    $notifyNew->link = (int)$messagesNew->idMessage;
                    $notifyNew->seconds = time();
                    $notifyNew->save();
                    //End add notify
                    $idMessage = (int)$messagesNew->idMessage;
                    //send messages for other user
                    $usersInvite = Yii::app()->request->getParam('usersInvited');
                    if (is_array($usersInvite) && count($usersInvite) > 0) {
                        foreach ($usersInvite as $user) {
                            $invitedMessagesNew = new InvitedMessage();
                            $invitedMessagesNew->idMessage = $idMessage;
                            $invitedMessagesNew->idUserInvited = (int)$user;
                            $invitedMessagesNew->save();

                            //Send notify user
                            $notifyUserNew = new NotifyUser();
                            $notifyUserNew->userId = (int)$user;
                            $notifyUserNew->notifyId = (int)$notifyNew->notifyId;
                            $notifyUserNew->dateRead = date("Y-m-d H:i:s");
                            $notifyUserNew->idLink = 'MESS' . (int)$messagesNew->idMessage;
                            $notifyUserNew->save();
                            //End send notify user
                        }
                    }

                    $dataReturn = array(
                        'avaPath' => Yii::app()->user->avatarPath,
                        'content' => $messagesNew->content,
                        'invitedUser' => $usersInvite,
                        'fullNameOfCreator' => Yii::app()->user->firstName . ' ' . Yii::app()->user->lastName,
                        'notifyId' => $notifyNew->notifyId,
                        'ownerId' => Yii::app()->user->idUser,
                        'gender' => (Yii::app()->user->gender == 1) ? "his" : "her",
                        'time' => time(),
                        'redirect' => "messages#message_index_" . $idMessage,
                        'type' => 'mess',
                        'idMessage' => $idMessage
                    );
                }
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

    public function actionGetUserReplyMessage()
    {
        if (Yii::app()->request->isAjaxRequest) {
            $messagesReply = Yii::app()->request->getParam('idMessageReply');
            $userReply = InvitedMessage::model()->getUserReplyMessage($messagesReply);
            $idUser = Yii::app()->user->idUser;
            if ((int)$userReply['userReply'] === (int)$idUser) {
                echo 0;
            } else {
                echo json_encode($userReply);
            }
            exit;
        } else {
            $this->forward('/site/error');
        }
    }

    public function actionGetUserReplyAllMessage()
    {
        if (Yii::app()->request->isAjaxRequest) {
            $messagesReply = Yii::app()->request->getParam('idMessageReply');
            $usersInvited = InvitedMessage::model()->getUserReplyAllMessage($messagesReply);
            $idUser = Yii::app()->user->idUser;
            $userReply = array();
            foreach ($usersInvited as $userInvited) {
                if ((int)$userInvited['userReply'] !== (int)$idUser) {
                    array_push($userReply, $userInvited);
                }
            }
            if (count($userReply) > 0) {
                echo json_encode($userReply);
            } else {
                echo 0;
            }
            exit;
        } else {
            $this->forward('/site/error');
        }
    }

    public function actionGetMessage()
    {
        if (Yii::app()->request->isAjaxRequest) {
            $idMessage = Yii::app()->request->getParam('idMessage');
            $dataMessages = InvitedMessage::model()->getAllMessagesOfUser();
            $message = null;
            //getDataReply
            foreach ($dataMessages as $keyMessage => $rowMessage) {
                if ($rowMessage['idMessage'] == $idMessage) {
                    $messageReply = array();
                    if ((int)$rowMessage['idMessageReply'] > 0) {

                        $continue = $rowMessage['idMessageReply'];
                        while ((int)$continue > 0) {
                            $push = 0;
                            foreach ($dataMessages as $rowMessageReply) {
                                if ($continue == $rowMessageReply['idMessage']) {
                                    array_push($messageReply, $rowMessageReply);
                                    $continue = $rowMessageReply['idMessageReply'];
                                    $push++;
                                }
                            }
                            if ($push === 0) {
                                $continue = 0;
                            }
                        }
                    }
                    $dataMessages[$keyMessage]['messageReplyList'] = $messageReply;
                    $message = $dataMessages[$keyMessage];
                }
            }
            $this->renderPartial('messageitem', array(
                'message' => $message
            ));
        } else {
            $this->forward('/site/error');
        }
    }
}

