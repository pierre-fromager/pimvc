<?php

/**
 * Description of Roles
 *
 * @author pierrefromager
 */

namespace Pimvc\Tools\Format\Interfaces;

interface Roles {

    const ACL_ROLE_ANONYMOUS = 'anonymous';
    const ACL_ROLE_WAITING = 'waiting';
    const ACL_ROLE_USER = 'user';
    const ACL_ROLE_PRO = 'pro';
    const ACL_ROLE_ADMIN = 'admin';

    public static function get($code);
    public static function getList();
}
