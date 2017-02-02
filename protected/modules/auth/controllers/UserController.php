<?php

/**
 * Class UserController
 * This is user controller of auth module.
 * @author UTC.KongLtn
 */
class UserController extends Controller {

    public $username;
    public $password;
    public $rememberMe;
    private $identity;
    private $attributes;
    public $txtErr = "";

    /**
     * Logs in the user using the given username and password in the model.
     * @return boolean whether login is successful
     */
    public function login() {
        if ($this->identity === null) {
            $this->identity = new UserIdentity($this->attributes['username'], $this->attributes['password']);
            $this->identity->authenticate();
        }
        if ($this->identity->errorCode === UserIdentity::ERROR_NONE) {
            $duration = $this->rememberMe ? 3600 * 24 * 30 : 0; // 30 days
            Yii::app()->user->login($this->identity, $duration);
            return true;
        } else {
            return false;
        }
    }


    /**
     * Login page, show error text when fail login.
     * @return undefined
     */
    public function actionLogin() {
        $this->titleInHeaderBar = "Login";
        // collect user input data
        if (isset($_POST['LoginAuth'])) {
            $this->attributes = $_POST['LoginAuth'];
            $this->txtErr = "";
            // validate user input and redirect to the previous page if valid
            if ($this->login()) {
                if (Yii::app()->user->role === "ADMIN") {
                    $this->redirect(Yii::app()->getBaseUrl() . '/admin');
                } else {
                    $this->redirect(Yii::app()->getBaseUrl() . '/');
                }
            } else $this->txtErr = "Invalid username or password.";
        }
        $this->render('index', array("txtErr" => $this->txtErr));
    }

    /**
     * Logs out the current user and redirect to homepage.
     * @return undefined
     */
    public function actionLogout() {
        $userId = Yii::app()->user->idUser;
        $model = Users::model()->findByPk((int)$userId);
        $model->lastSeen = date("Y-m-d H:i:s");
        $model->save();
        Yii::app()->user->logout();
        $this->redirect(Yii::app()->homeUrl);
    }

    /**
     * Action error
     * @return undefined
     */
    public function actionError() {
        if ($error = Yii::app()->errorHandler->error) {
            if (Yii::app()->request->isAjaxRequest) {
                echo $error['message'];
            } else {
                $this->render('error', $error);
            }
        }
    }


}