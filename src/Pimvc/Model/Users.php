<?php

/**
 * Description of Pimvc\Model\Users
 *
 * @author Pierre Fromager <pf@pier-infor.fr>
 */
namespace Pimvc\Model;

class Users extends \Pimvc\Db\Model\Orm {
    
    const USERS_STATUS_VALID = 'valid';
    const USERS_STATUS_WAITING = 'waiting';
    
    protected $_name ='user';
    protected $_primary = 'id';
    protected $_alias = 'users';
    protected $_adapter = self::MODEL_ADAPTER_MYSQL;
    
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
    
    /**
     * getAuth
     * 
     * @param string $login
     * @param string $password
     * @return array 
     */
    public function getAuth($login, $password) {
        $what = array(
            'id', 'email', 'login', 'password'
            , 'name', 'profil', 'status', 'token'
        );
        $where = array(
            'login' => $login
            , 'password' => $password
            , 'status#in' => "('valid','waiting')" // Allow both waiting and valid users to login
        );
        $this->cleanRowset();
        $this->find($what, $where);
        return $this->getRowsetAsArray();
    }
    
    /**
     * getPro
     * 
     * @return array 
     */
    public function getPro() {
        $what = array(
            'id', 'fid', 'name', 'photo'
            , 'adresse', 'gsm', 'email'
            , 'cp', 'ville', 'site'
        );
        $where = array(
            'profil' => 'pro'
            , 'fid#!=' => 0
        );
        $order = array('fid' => 'asc');
        $this->cleanRowset();
        $this->find($what, $where, $order);
        $results = $this->getRowsetAsArray();
        return $results;
    }
    
    /**
     * getStatus
     * 
     * @return array 
     */
    public function getStatus() {
        return array(
            'waiting' => 'En attente'
            , 'valid' => 'Validé'
            , 'disable' => 'Désactivé'
        );
    }

    /**
     * getAll
     * 
     * @return array 
     */
    public function getAll() {
        $this->find(array('*'),array('id'=>'%'),array('id'=>'asc'));
        return $this->getRowsetAsArray();
    }


    /**
     * getById
     * 
     * @param int $id
     * @param array $what
     * @return \Model_Domain_Users  
     */
    public function getById($id, $what = array('*')) {
        $this->cleanRowset();
        $this->find($what, array('id' => $id));
        return $this->_current;
    }
 
    /**
     * getWaitings
     * 
     * @return array 
     */
    public function getWaitings() {
        return $this->getByStatus(self::USERS_STATUS_WAITING);
    }
    
    /**
     * getValids
     * 
     * @return array 
     */
    public function getValids($what) {
        return $this->getByStatus(
            self::USERS_STATUS_VALID
            , $what
        );
    }
    
    /**
     * getByStatus
     * 
     * @param string $status
     * @return array 
     */
    private function getByStatus($status, $what = array('*')) {
        $where = array('status' => $status);
        $this->find($what, $where);
        return $this->getRowsetAsArray();
    }
    
    
    /**
     * count
     * 
     * @return int 
     */
    public function count() {
        $this->find(array('id'));
        return count($this->getRowsetAsArray());
    }
    
    /**
     * getList
     * 
     * @param string $name
     * @return array 
     */
    public function getList($name) {
        $this->find(
            array('id', 'name')
            , array('name' => '%' . $name . '%')
            , array('id' => 'desc')
        );
        return $this->getRowsetAsArray();
    }
    
   
    /**
     * validCredential returns user id as assoc array if credential is valid or false
     * 
     * @param string $credential
     * @return array | boolean 
     */
    public function validCredential($credential) {
        $sql = "select id from $this->_name where md5(concat(login,password)) like '$credential'";
        try {
            $sth = $this->_db->prepare($sql);
            $sth->setFetchMode(PDO::FETCH_ASSOC);
            $sth->execute();
            $valid = $sth->fetch();
            $sth->closeCursor();
        } catch (PDOException $exc) {
            echo $exc->getTraceAsString();
        }
        return $valid;
    }
    
    /**
     * userExists
     * 
     * @param string $login
     * @return type 
     */
    public function userExists($login) {
        $this->cleanRowset();
        $this->find(
            ['id']
            , ['login' => $login]
            , ['id' => 'desc']
            , []
            , 'login'
        );
        return (count($this->getRowset()) > 0);
    }
    
    /**
     * getByEmail
     * 
     * @param string $email
     * @param array $what
     * @return \Model_Domain_Users 
     */
    public function getByEmail($email, $what = array('*')) {
        $this->cleanRowset();
        $where = array(
            'email' => $email
        );
        $this->find($what, $where);
        $result = $this->getCurrent();
        $result = (isset($result->email)) ? $result : array();
        return $result;
    }


    /**
     * validate
     * 
     * @param int $id
     * @return boolean 
     */
    public function validate($id) {
        return $this->changeStatus($id, self::USERS_STATUS_VALID);
    }
    
    /**
     * changeStatus
     * 
     * @param int $id
     * @param string $status
     * @return boolean 
     */
    private function changeStatus($id, $status) {
        $returnCode = false;
        if (!empty($id) && !empty($status)) {
            $this->cleanRowset();
            $this->setWhere(array('id' => $id));
            $datas = array('status' => $status);
            $returnCode = $this->update($datas);
        }
        return $returnCode;
    }
    
    
    /**
     * countEmailsByProfil
     * 
     * @param string $profil
     * @return array 
     */
    public function countEmailsByProfil($profil = 'pros') {
        $result = false;
        $this->cleanRowset();
        $this->find(
            array('email')
            , array('profil' => $profil)
            , array('id' => 'desc')
            , array()
            , 'email'
        );
        $result = $this->getRowsetAsArray();
        return $result;
}

    /**
     * getEmailsByProfil
     * 
     * @param string $profil
     * @return array 
     */
    public function getEmailsByProfil($profil = 'pros') {
        $results = array();
        $result = $this->countEmailsByProfil($profil);
        foreach ($result as $item) {
            if (!empty($item['email'])) {
                $results[] = strtolower($item['email']);
            }           
        }
        unset($result);
        return $results;
    }
    
        /**
     * isValid
     * 
     * @param int $uid
     * @return boolean 
     */
    public function isValid($uid) {
        $isValid = false;
        if (!empty($uid)) {
            $counter = $this->counter(
                array(
                    'id' => $uid
                    , 'status' => 'valid'
                )
            );
            $isValid = ($counter > 1);
        }
        return $counter;
    }
    
    /*
     * countExpirationDays
     * 
     * @param int $uid
     */
    public function countExpirationDays($uid) {
        $this->cleanRowset();
        $what = array();
        $where = array('id' => $uid);
        $this->find($what, $where);
        $dateToday = new \DateTime(date('Y-m-d H:i:s'));
        $dateEnd = new \DateTime($this->_current->dateexp);
        $daysCount = ($dateToday->diff($dateEnd, false)->format('%R%a'));
        return $daysCount;
    }
    
    /**
     * isExpired
     * 
     * @param int $uid
     * @return boolean 
     */
    public function isExpired($uid) {
        return ($this->countExpirationDays($uid) <= 0);
    }
    

    /**
     * setExpirationDate
     * 
     * @param int $uid
     * @param string $offer
     * @return boolean 
     */
    public function setExpirationDate($uid, $offer) {
        $result = false;
        if (!empty($uid)) {
            $criterias = array('id' => $uid);
            $this->cleanRowset();
            $this->find(array(), $criterias);
            $domainObject = $this->getCurrent();
            $domainObject->dateexp = $this->getExpirationDate($offer);
            $this->save($domainObject);
            $result = true;
        }
        return $result;
    }
    
    /**
     * getExpirationDate
     * 
     * @param string $offer
     * @return string 
     */
    public function getExpirationDate($offer) {
        $dateExp = '';
        if ($this->isValidOffer($offer)){
            $dateFormat = 'Y-m-d H:i:s';
            $dateToday = new DateTime(date($dateFormat));
            $prefixUnit = ($offer == 'demo') ? 'PT' : 'P';
            $suffixUnit = ($offer == 'demo') ? 'H' : 'D';
            $interval = $prefixUnit . $this->getOfferDaysValidity($offer) 
                . $suffixUnit;
            $dateInteval = new DateInterval($interval);
            $dateExp = ($dateToday->add($dateInteval)->format($dateFormat));    
        }
        return $dateExp;
    }

    /**
     * getOffers
     * 
     * @return type 
     */
    private function getOffers() {
        return array('demo', 'liberte', 'classique', 'premium');
    }
    
    /**
     * getOfferInterval
     * 
     * @param string $offer
     * @return string 
     */
    private function getOfferDaysValidity($offer) {
        $daysValidity = array(
            96 // Hours
            , 1095 // Days
            , 365 // Days
            , 365 // Days
        );
        $offerInterval = array_combine($this->getOffers(), $daysValidity);
        return $offerInterval[$offer];
    }
    
    /**
     * isValidOffer
     * 
     * @param string $offer
     * @return boolean 
     */
    public function isValidOffer($offer) {
        return (boolean) in_array($offer, $this->getOffers());
    }
    
    
    /**
     * countOffersAjax
     * 
     * @param string $offer
     * @return array 
     */
    public function countOffersAjax() {
        $this->cleanRowset();
        $validOffers = '("demo","liberte","premium","classique")';
        $where = array(
            'offer#in' => $validOffers
        );
        $this->find(
            array('offer')
            , $where
            , array('id' => 'desc')
            , array(15)
            , 'offer'
        );
        return $this->getRowsetAsArray();
    }
    
    /**
     * countByRoleAjax
     * 
     * @param string $offer
     * @return array 
     */
    public function countByRoleAjax() {
        $this->cleanRowset();
        $validOffers = '("admin","user")';
        $where = array(
            'profil#in' => $validOffers
        );
        $this->find(
            array('profil')
            , $where
            , array('id' => 'desc')
            , array(15)
            , 'profil'
        );
        return $this->getRowsetAsArray();
    }
    
    
    /**
     * countByStatusAjax
     * 
     * @param string $offer
     * @return array 
     */
    public function countByStatusAjax() {
        $this->cleanRowset();
        $validOffers = '("valid","waiting")';
        $where = array(
            'status#in' => $validOffers
        );
        $this->find(
            array('status')
            , $where
            , array('id' => 'desc')
            , array(15)
            , 'status'
        );
        return $this->getRowsetAsArray();
    }
    
    /**
     * countByCountriesAjax
     * 
     * @param string $offer
     * @return array 
     */
    public function countByCountriesAjax() {
        $this->cleanRowset();
        $this->find(
            array('country')
            , array()
            , array('id' => 'desc')
            , array(15)
            , 'country'
        );
        return $this->getRowsetAsArray();
}

    /**
     * updateIp
     * 
     */
    public function updateIp() {
        $this->cleanRowset();
        $id =  \Pimvc\App::getInstance()->getRequest()->getSession('id');
        //$id = Tools_Session::getUid();
        $user = $this->getById($id);
        $user->ip = $_SERVER['REMOTE_ADDR'];
        $this->save($user);
    }
    
    /**
     * getAllowedIps
     * 
     * @return array 
     */
    public function getAllowedIps() {
        $this->cleanRowset();
        $what = array('ip');
        $where = array('ip#!=' => ' ');
        $order = array('id' => 'desc');
        $limit = array();
        $groupBy = 'ip';
        $this->find(
            $what
            , $where
            , $order
            , $limit
            , $groupBy
        );
        return $this->getRowsetAsArray();
    }
    
    /**
     * getListFromIdsIn
     * 
     * @param array $in
     * @return array 
     */
    public function getListFromIdsIn($in = array()) {
        $this->cleanRowset();
        $what = array('id', 'name');
        $inCriteria = "('" . implode("','", $in) . "')";
        $where = array('id#in' => $inCriteria);
        $this->find($what, $where);     
        $results = $this->getRowsetAsArray();
        return $results;
    }
    
    /**
     * getAuthByToken
     * 
     * @param string $token
     * @return array 
     */
    public function getAuthByToken($token) {
        $where = array(
            'token' => $token
            , 'status' => 'valid'
        );
        $this->cleanRowset();
        $this->find($this->userInfoFields, $where);
        return $this->getRowsetAsArray();
    }
}

