<?php
/**
 * class Auth
 * is an auth manager
 * 
 * @author Pierre Fromager <pf@pier-infor.fr>
 */
namespace Pimvc\Tools\User;

class Auth {

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

    /**
     * @see __construct
     * 
     * @param string $login
     * @param string $password 
     */
    public function __construct($login, $password, $token = '') {
        $this->app = \Pimvc\App::getInstance();
        $this->modelConfig = $this->app->getConfig()->getSettings('dbPool');
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
     * userLink returns user link
     * 
     * @param string $uname
     * @param int $uid
     * @return string 
     */
    private function userLink() {
        $link = $this->baseUrl . self::AUTH_LINK_PROFIL . $this->id;
        $linkParams = array(
            'link' => $link
            , 'value' => $this->profil
        );
        $linkOptions = array(
            'title' => self::AUTH_LINK_TITLE
        );
        $userLink = \Pimvc\Html\Element::link($linkParams, $linkOptions);
        return $userLink;
    }

    /**
     * process processes authentication
     * 
     */
    protected function process() {
        $userModel = new \Pimvc\Model\Users($this->modelConfig);
        $result = $userModel->getAuth($this->login, $this->password);
        if (!$result) {
            $this->setAllowed(false);
            $this->message = self::AUTH_UNKNOW_USER;
        } else {
            $this->setAllowed(true);
            $userModel->updateIp();
            $this->id = $result[0]['id'];
            $this->profil = $result[0]['profil'];
            $userInfo = new \stdClass();
            $userInfo->profil = $result[0]['profil'];
            $userInfo->status = $result[0]['status'];
            $userInfo->name = $result[0]['name'];
            $userInfo->email = $result[0]['email'];
            $userInfo->token = $result[0]['token'];
            $this->app->getRequest()->setSession('id', $this->id);
            $this->app->getRequest()->setSession('profil', $this->profil);
            $this->app->getRequest()->setSession('userinfo', $userInfo);
            $this->message = 'Utilisateur '
                . $this->userLink()
                . ', profil ' . $this->profil
                . ' connecté (php v' . PHP_VERSION . ').';
        }
        unset($userModel);
    }

    /**
     * processToken
     * 
     */
    private function processToken() {
        $userModel = new \Pimvc\Model\Users($this->modelConfig);
        $result = $userModel->getAuthByToken($this->token);
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
            $this->app->getRequest()->setSession('id', $this->id);
            $this->app->getRequest()->setSession('profil', $this->profil);
            $this->app->getRequest()->setSession('userinfo', $userInfo);
            $this->message = '';
        }
    }

    /**
     * setAllowed
     * 
     * @param boolean $allowed 
     */
    private function setAllowed($allowed){
        $this->isAllowed = $allowed;
    }

    /**
     * get error message
     * 
     */
    public function __toString() {
        return $this->message;
    }

}