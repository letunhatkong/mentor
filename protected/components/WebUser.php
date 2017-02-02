<?php

/**
 * Class WebUser
 * @author UTC.HuyTD
 */
class WebUser extends CWebUser {
    /**
     * Get method
     * @param string $name
     * @return mixed|null
     */
    public function __get($name) {
        if ($this->hasState('__userInfo')) {
            $user=$this->getState('__userInfo',array());
            if (isset($user[$name])) {
                return $user[$name];
            }
        }
        return parent::__get($name);
    }

    /**
     * Set method
     * @param string $name
     * @param string $value
     * @return undefined
     */
    public function __set($name,$value) {
        if ($this->hasState('__userInfo')) {
            $user=$this->getState('__userInfo',array());
            if (isset($user[$name])) {
                $user[$name]=$value;
                $this->setState('__userInfo', $user);
                return;
            }
        }
        parent::__set($name,$value);
    }

    /**
     * Login function
     * @param IUserIdentity $identity
     * @param int $duration
     * @return undefined
     */
    public function login($identity, $duration = 0) {
        $this->setState('__userInfo', $identity->getUser());
        parent::login($identity, $duration);
    }
}
