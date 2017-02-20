<?php

/**
 * Description of user
 *
 * @author pierrefromager
 */

namespace model;

class users extends \lib\db\model\orm{
    
    const USERS_STATUS_VALID = 'valid';
    const USERS_STATUS_WAITING = 'waiting';
    
    protected $_slot = 'db2';
    protected $_name ='user';
    protected $_primary = 'id';
    protected $_alias = 'users';
    
    private $userInfoFields = array(
        'id'
        //, 'iid'
        , 'name'
        , 'email'
        , 'password'
        , 'profil'
        , 'status'
    );
    
    /**
     * __construct
     * 
     * @param array $config 
     */
    public function __construct($config = array()) {
        parent::__construct($config);
        return $this;
    }
}
