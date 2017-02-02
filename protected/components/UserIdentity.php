<?php

/**
 * Class UserIdentity
 * UserIdentity represents the data needed to identity a user.
 * It contains the authentication method that checks if the provided data can identity the user.
 * @author UTC.HuyTD
 * @author UTC.KongLtn
 */
class UserIdentity extends CUserIdentity {
    /**
     * @var object of Users model
     */
    public  $user;

    /**
     * Authenticate function is using for login, auth module
     * @return bool true or false
     */
    public function authenticate()	{
        $username = strtolower($this->username);
        $user = Users::model()->find('LOWER(username)=?', array($username));

        if ($user === null) {
            $this->errorCode = self::ERROR_USERNAME_INVALID;
            $this->errorMessage = "Incorrect username or password.";
        } else if (!$user->validatePassword($this->password)) {
            $this->errorCode = self::ERROR_PASSWORD_INVALID;
            $this->errorMessage = "Incorrect username or password.";
        } else {        
            $this->user['idUser'] = $user['idUser']?$user['idUser']:0;
            $this->user['firstName'] = $user['firstName']?$user['firstName']:" ";
            $this->user['lastName'] = $user['lastName']?$user['lastName']:" ";
            $this->user['role'] = $user['role']?$user['role']:0;
            $this->user['gender'] = $user['gender'];
            
            if($user['avatarPath']!= "" && $user['avatarPath']!= null){
                $this->user['avatarPath']= Yii::app()->params['avatarFolderPath'].'/'.$user['avatarPath'];
            }else{
                $this->user['avatarPath'] = Yii::app()->params['avatarDefault'];
            }
            $this->errorCode = self::ERROR_NONE;
        }

        return $this->errorCode == self::ERROR_NONE;
    }

    /**
     * Get user
     * @return object $this->user
     */
    public function getUser() {
        return $this->user;
    }    
}