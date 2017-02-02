<?php

class SiteController extends Controller
{
    /**
     * Declares class-based actions.
     */
    public function actions()
    {
        return array(
            // captcha action renders the CAPTCHA image displayed on the contact page
            'captcha' => array(
                'class' => 'CCaptchaAction',
                'backColor' => 0xFFFFFF,
            ),
            // page action renders "static" pages stored under 'protected/views/site/pages'
            // They can be accessed via: index.php?r=site/page&view=FileName
            'page' => array(
                'class' => 'CViewAction',
            ),
        );
    }

    /**
     * This is the default 'index' action that is invoked
     * when an action is not explicitly requested by users.
     */
    public function actionIndex()
    {
        $this->titleInHeaderBar = 'Home';
        // renders the view file 'protected/views/site/index.php'
        // using the default layout 'protected/views/layouts/main.php'
        $this->render('index');
    }

    /**
     * This is the action to handle external exceptions.
     */
    public function actionError()
    {
        $this->titleInHeaderBar = 'Error';
        if ($error = Yii::app()->errorHandler->error) {
            if (Yii::app()->request->isAjaxRequest) {
                echo $error['message'];
            } else {
                $this->render('error', $error);
            }
        } else {
            $error = 404;
            $this->render('error', array('error' => $error, 'message' => 'Page not found'));
        }
    }


    /**
     * Render permission error page
     */
    public function actionPermission()
    {
        $this->titleInHeaderBar = 'Permission Error';
        $this->render('403-error');

    }

    /**
     * Delete a notification
     */
    public function actionDelNotify()
    {
        $result = array(
            'status' => false,
            'notifyId' => 0,
            'notifyUserId' => 0
        );
        if (Yii::app()->request->isAjaxRequest) {
            if (!Yii::app()->user->isGuest) {
                $notifyId = Yii::app()->request->getParam("id");
                $userId = Yii::app()->user->idUser;
                if (NotifyUser::model()->checkOwner($userId, $notifyId)) {
                    NotifyUser::model()->delNotifyOfUser($userId, $notifyId);
                    $result["status"] = true;
                    $result["notifyId"] = $notifyId;
                };
            }
        }
        echo json_encode($result);
    }

    /**
     * Delete a notification
     */
    public function actionCountNotify()
    {
        $count = 0;
        if (Yii::app()->request->isAjaxRequest) {
            if (!Yii::app()->user->isGuest) {
                $count = $this->countNotifications(Yii::app()->user->idUser);
            }
        }
        echo $count;
    }

    /**
     * Get server time and count notification
     */
    public function actionGetTime()
    {
        $data = array(
            'time' => time()
        );
        if (!Yii::app()->user->isGuest) {
            $count = $this->countNotifications(Yii::app()->user->idUser);
            $data['countNotify'] = (int)$count;
        }
        echo json_encode($data);
    }

    public function actionSetIsRead()
    {
        if (Yii::app()->request->isAjaxRequest) {
            $notifyId = Yii::app()->request->getParam('id');
            $userId = Yii::app()->user->idUser;
            $check = NotifyUser::model()->updateAll(array('isRead' => 1), 'notifyId = ' . $notifyId . ' AND userId = ' . $userId);
            echo $check;
        }
    }

    /**
     * Get user data
     */
    public function actionGetData(){
        if (Yii::app()->request->isAjaxRequest) {
            $data = array(
                'time' => time(),
                'date' => date('d.m.Y', time())
            );
            if (!Yii::app()->user->isGuest) {
                $data['fullName'] = trim(Yii::app()->user->firstName) . ' ' . trim(Yii::app()->user->lastName);
                $data['avaPath'] = Yii::app()->user->avatarPath;
            }
            echo json_encode((object)$data);
        } else echo 'null';
    }

//    public function actionTest2(){
//        $key = "m3nt0r:secretKey";
//        $token = array(
//            "iss" => "http://example.org",
//            "aud" => "http://example.com",
//            "iat" => 1356999524,
//            "nbf" => 1357000000
//        );
//        $token = json_encode($token);
//        $jwt = JWT::encode($token, $key);
//        $decoded = JWT::decode($jwt, $key, array('HS256'));
//        $decoded_array = (array) $decoded;
//        JWT::$leeway = 60; // $leeway in seconds
//        $decoded = JWT::decode($jwt, $key, array('HS256'));
//        var_dump( $decoded );

//        $id = Yii::app()->request->getParam("id");
//
//        $notifies = $this->getAllNotify($id);
//        $result = json_encode($notifies);
//        var_dump($notifies);
//    }

//    public function actionTest (){
//        $thumbFolder = Yii::getPathOfAlias('webroot').'/upload/thumbnails';
//        $img = Yii::getPathOfAlias('webroot') . '/upload/avatars/Hydrangeas.jpg' ;
//        $a = $this->createThumbnailImage($thumbFolder, $img  , 50);
//        echo $a;
//    }
}