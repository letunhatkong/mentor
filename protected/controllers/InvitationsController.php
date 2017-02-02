<?php

/**
 * Controller for Invitation page
 * @author      UTC.KongLtn
 * @lastUpdate  Dec 21, 2015
 */
class InvitationsController extends Controller
{

    /**
     * Redirect to auth page if user is guest (not login)
     * @return undefined
     */
    public function filters()
    {
        if (Yii::app()->user->isGuest) {
            $this->redirect(Yii::app()->getBaseUrl() . "/auth/user/login");
        }
    }


    /**
     * Display note of current user, create a new note or edit current note when user click edit button
     * @return undefined
     */
    public function actionIndex()
    {
        $this->titleInHeaderBar = "My invitations";
        $userId = Yii::app()->user->idUser;

        $data = Notify::model()->getInvitationsById((int)$userId);
        $this->render("index", array("invitations" => $data));
    }

    /**
     * New a child invitation by json data
     */
    public function actionNew()
    {
        if (Yii::app()->request->isAjaxRequest) {
            $json = Yii::app()->request->getParam('json');
            $data = (array)json_decode($json);
            $data['isGuestId'] = Yii::app()->user->idUser;
            $data['notifyTime'] = $this->toTimeNotify(intval($data['time']));
            $this->renderPartial('item', array(
                'item' => $data
            ));
        } else {
            $this->forward('/site/error');
        }

    }
}
