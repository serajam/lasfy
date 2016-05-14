<?php

/**
 * Base user domain class
 *
 * @author     Fedor Petryk
 *
 */
class User extends Core_Users_User
{
    /**
     *
     * get pair user id - user name
     *
     * @return array
     */
    public function getPair()
    {
        return [
            $this->userId => $this->getInitials()
        ];
    }

    public function getInitials()
    {
        return $this->name;
    }
}