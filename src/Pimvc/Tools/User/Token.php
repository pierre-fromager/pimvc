<?php

/**
 * Description of Tools_User_Token
 *
 * @author pierrefromager
 */
namespace Pimvc\Tools\User;

class Token {
    
    /**
     * get
     * 
     * @param string $email
     * @param string $password
     * @return string 
     */
    public static function get($email, $password) {
        return md5($email . $password);
    }
}
