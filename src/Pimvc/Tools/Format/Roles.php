<?php
/**
 * Description of Roles
 *
 * @author pierrefromager
 */

namespace Pimvc\Tools\Format;

class Roles {
    
    const ACL_ROLE_ANONYMOUS = 'anonymous';
    const ACL_ROLE_WAITING = 'waiting';
    const ACL_ROLE_USER = 'user';
    const ACL_ROLE_PRO = 'pro';
    const ACL_ROLE_SVI = 'svi';
    const ACL_ROLE_ADMIN = 'admin';
    
    protected static $roles = array(
        'Anonymes' => self::ACL_ROLE_ANONYMOUS
        , 'En attente' => self::ACL_ROLE_WAITING
        , 'Utilisateurs' => self::ACL_ROLE_USER
        , 'Professionnels' => self::ACL_ROLE_PRO
        , 'Ivr' => self::ACL_ROLE_SVI
        , 'Administrateurs' => self::ACL_ROLE_ADMIN
    );

    /**
     * get
     * 
     * @param int $code
     * @return string 
     */
    public static function get($code) {
        return self::$roles[$code];
    }
    
    /**
     * getList
     * 
     * @return array 
     */
    public static function getList() {
        return self::$roles;
    }
}

