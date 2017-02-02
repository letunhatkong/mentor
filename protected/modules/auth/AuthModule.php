<?php

class AuthModule extends CWebModule
{
    public function init()
    {
        $this->setImport(array(
            'auth.models.*',
            'auth.components.*',
        ));
    }

    // Load function before controller is called
    public function beforeControllerAction($controller, $action)
    {
        if (parent::beforeControllerAction($controller, $action)) {
            $userId = Yii::app()->user->id;
            if (isset($userId) && $action->getId() === 'login') {
                $controller->redirect('/admin');
            }
            return true;
        } else {
            return false;
        }
    }
}
