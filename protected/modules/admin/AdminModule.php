<?php

class AdminModule extends CWebModule
{

    public function init()
    {
        $this->setImport(array(
            'admin.models.*',
            'admin.components.*',
        ));
    }

    // Load function before controller is called
    public function beforeControllerAction($controller, $action)
    {
        // Check role
        if (parent::beforeControllerAction($controller, $action)) {
            $controller->layout = 'layoutAdmin';

            $userId = Yii::app()->user->id;
            if (!isset($userId)) {
                $controller->redirect('/auth/user/login');
            }
            if (Yii::app()->user->role !== "ADMIN") {
                $controller->forward('/site/permission');
            }

            return true;
        } else {
            return false;
        }
    }
}
