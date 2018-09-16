<?php
/**
 * class Auth
 * is an auth manager
 *
 * @author Pierre Fromager <pf@pier-infor.fr>
 */
namespace Pimvc\Tools\User;

use \Pimvc\Model\Users as authModel;

class Auth
{
    const AUTH_UNKNOW_USER = 'Utilisateur inconnu ou mot de passe incorrect.';
    const AUTH_LINK_PROFIL = 'user/edit/id/';
    const AUTH_LINK_TITLE = 'Accéder à mon profil.';

    protected $token;
    protected $login = null;
    protected $password = null;
    protected $baseUrl = '';

    public $message = null;
    public $id = null;
    public $profil = null;
    public $datec = null;
    public $dateexp = null;
    public $status  = null;
    public $firstname  = null;
    public $lastname  = null;

    public $isAllowed = false;
    private $app;
    private $modelConfig;
    private $authModel;

    /**
     * @see __construct
     *
     * @param string $login
     * @param string $password
     */
    public function __construct($login, $password, $token = '')
    {
        $this->app = \Pimvc\App::getInstance();
        $this->modelConfig = $this->app->getConfig()->getSettings('dbPool');
        $this->authModel = new authModel($this->modelConfig);
        $this->token = $token;
        $this->message = '';
        $this->login = $login;
        $this->password = $password;
        $this->baseUrl = $this->app->getRequest()->getBaseUrl();
        if (!empty($token)) {
            $this->processToken($this->token);
        } else {
            $this->process();
        }
    }

    /**
     * process processes authentication
     *
     */
    protected function process()
    {
        $this->setAllowed(false);
        if ($result = $this->authModel->getAuth($this->login, $this->password)) {
            $this->setAllowed(true);
            $this->authModel->updateIp();
            $this->id = $result[0]['id'];
            $this->profil = $result[0]['profil'];
            $userInfo = new \stdClass();
            $userInfo->profil = $result[0]['profil'];
            $userInfo->status = $result[0]['status'];
            $userInfo->name = $result[0]['name'];
            $userInfo->email = $result[0]['email'];
            $userInfo->token = $result[0]['token'];
            $userInfo->fid = $result[0]['fid'];
            $this->setSessionProfile($this->id, $this->profil, $userInfo);
        }
    }

    /**
     * setSessionProfile
     *
     * @param int $id
     * @param string $profil
     * @param object $userInfo
     */
    private function setSessionProfile($id, $profil, $userInfo)
    {
        $this->app->getRequest()->setSession('id', $id);
        $this->app->getRequest()->setSession('profil', $profil);
        $this->app->getRequest()->setSession('userinfo', $userInfo);
    }

    /**
     * processToken
     *
     */
    private function processToken()
    {
        $result = $this->authModel->getAuthByToken($this->token);
        if (!$result) {
            $this->setAllowed(false);
            $this->message = self::AUTH_UNKNOW_USER;
        } else {
            $this->setAllowed(true);
            $this->id = $result[0]['id'];
            $this->profil = $result[0]['profil'];
            $userInfo = new \stdClass();
            $userInfo->profil = $result[0]['profil'];
            $userInfo->status = $result[0]['status'];
            $userInfo->name = $result[0]['name'];
            $userInfo->email = $result[0]['email'];
            $this->setSessionProfile($this->id, $this->profil, $userInfo);
            $this->message = '';
        }
    }

    /**
     * setAllowed
     *
     * @param boolean $allowed
     */
    private function setAllowed($allowed)
    {
        $this->isAllowed = $allowed;
    }

    /**
     * get error message
     *
     */
    public function __toString()
    {
        return $this->message;
    }
}
