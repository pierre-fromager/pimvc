<?php

/**
 * Description of Pimvc\Model\Users
 *
 * @author Pierre Fromager <pf@pier-infor.fr>
 */

namespace Pimvc\Model;

use \Pimvc\Db\Model\Orm;

class Users extends Orm implements Interfaces\Users
{
    protected $_name = 'user';
    protected $_primary = self::PARAM_ID;
    protected $_alias = 'users';
    protected $_adapter = self::MODEL_ADAPTER_MYSQL;
    protected $userInfoFields = [
        self::PARAM_ID
        , self::PARAM_NAME
        , self::PARAM_EMAIL
        , self::PARAM_PASSWORD
        , self::PARAM_PROFIL
        , self::PARAM_STATUS
    ];

    /**
     * __construct
     *
     * @param array $config
     */
    public function __construct($config = [])
    {
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
    public function getAuth($login, $password)
    {
        $what = [
            self::PARAM_ID, self::PARAM_FID, self::PARAM_EMAIL,
            self::PARAM_LOGIN, self::PARAM_PASSWORD, self::PARAM_NAME,
            self::PARAM_PROFIL, self::PARAM_STATUS, self::PARAM_TOKEN
        ];
        $where = [
            self::PARAM_LOGIN => $login
            , self::PARAM_PASSWORD => $password
            , self::PARAM_STATUS.'#'.'in' => "('valid','waiting')" // Allow both waiting and valid users to login
        ];
        $this->cleanRowset();
        $this->find($what, $where);
        return $this->getRowsetAsArray();
    }

    /**
     * getPro
     *
     * @return array
     */
    public function getPro()
    {
        $what = [
            self::PARAM_ID, self::PARAM_FID, self::PARAM_NAME, 'photo'
            , 'adresse', 'gsm', self::PARAM_EMAIL
            , 'cp', 'ville', 'site'
        ];
        $where = [self::PARAM_PROFIL => 'pro'];
        $order = [self::PARAM_FID => 'asc'];
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
    public function getStatus()
    {
        return [
            'waiting' => 'En attente'
            , 'valid' => 'Validé'
            , 'disable' => 'Désactivé'
        ];
    }

    /**
     * getAll
     *
     * @return array
     */
    public function getAll()
    {
        $this->find(['*'], [self::PARAM_ID => '%'], [self::PARAM_ID => 'asc']);
        return $this->getRowsetAsArray();
    }

    /**
     * getById
     *
     * @param int $id
     * @param array $what
     * @return \Model_Domain_Users
     */
    public function getById($id, $what = ['*'])
    {
        $this->cleanRowset();
        $this->find($what, [self::PARAM_ID => $id]);
        return $this->_current;
    }

    /**
     * getWaitings
     *
     * @return array
     */
    public function getWaitings()
    {
        return $this->getByStatus(self::USERS_STATUS_WAITING);
    }

    /**
     * getValids
     *
     * @return array
     */
    public function getValids($what)
    {
        return $this->getByStatus(self::USERS_STATUS_VALID, $what);
    }

    /**
     * getByStatus
     *
     * @param string $status
     * @return array
     */
    private function getByStatus($status, $what = ['*'])
    {
        return $this->find($what, [self::PARAM_STATUS => $status])->getRowsetAsArray();
    }

    /**
     * count
     *
     * @return int
     */
    public function count()
    {
        $this->find([self::PARAM_ID]);
        return count($this->getRowsetAsArray());
    }

    /**
     * getList
     *
     * @param string $name
     * @return array
     */
    public function getList($name)
    {
        return $this->find(
            [self::PARAM_ID, self::PARAM_NAME],
            [self::PARAM_NAME => '%' . $name . '%'],
            [self::PARAM_ID => 'desc']
        )->getRowsetAsArray();
    }

    /**
     * validCredential returns user id as assoc array if credential is valid or false
     *
     * @param string $credential
     * @return array | boolean
     */
    public function validCredential($credential)
    {
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
    public function userExists($login)
    {
        $this->cleanRowset();
        $this->find(
            [self::PARAM_ID],
            [self::PARAM_LOGIN => $login],
            [self::PARAM_ID => 'desc'],
            [],
            self::PARAM_LOGIN
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
    public function getByEmail($email, $what = ['*'])
    {
        $this->cleanRowset();
        $this->find($what, [self::PARAM_EMAIL => $email]);
        $result = $this->getCurrent();
        $result = (isset($result->email)) ? $result : [];
        return $result;
    }

    /**
     * validate
     *
     * @param int $id
     * @return boolean
     */
    public function validate($id)
    {
        return $this->changeStatus($id, self::USERS_STATUS_VALID);
    }

    /**
     * changeStatus
     *
     * @param int $id
     * @param string $status
     * @return boolean
     */
    private function changeStatus($id, $status)
    {
        $returnCode = false;
        if (!empty($id) && !empty($status)) {
            $this->cleanRowset();
            $this->setWhere([self::PARAM_ID => $id]);
            $datas = [self::PARAM_STATUS => $status];
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
    public function countEmailsByProfil($profil = 'pros')
    {
        $result = false;
        $this->cleanRowset();
        return $this->find(
            [self::PARAM_EMAIL],
            [self::PARAM_PROFIL => $profil],
            [self::PARAM_ID => 'desc'],
            [],
            self::PARAM_EMAIL
        )->getRowsetAsArray();
    }

    /**
     * getEmailsByProfil
     *
     * @param string $profil
     * @return array
     */
    public function getEmailsByProfil($profil = 'pros')
    {
        $results = [];
        $result = $this->countEmailsByProfil($profil);
        foreach ($result as $item) {
            if (!empty($item[self::PARAM_EMAIL])) {
                $results[] = strtolower($item[self::PARAM_EMAIL]);
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
    public function isValid($uid)
    {
        $isValid = false;
        if (!empty($uid)) {
            $counter = $this->counter([self::PARAM_ID => $uid, self::PARAM_STATUS => 'valid']);
            $isValid = ($counter > 1);
        }
        return $counter;
    }

    /*
     * countExpirationDays
     *
     * @param int $uid
     */

    public function countExpirationDays($uid)
    {
        $this->cleanRowset();
        $what = [];
        $where = [self::PARAM_ID => $uid];
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
    public function isExpired($uid)
    {
        return ($this->countExpirationDays($uid) <= 0);
    }

    /**
     * setExpirationDate
     *
     * @param int $uid
     * @param string $offer
     * @return boolean
     */
    public function setExpirationDate($uid, $offer)
    {
        $result = false;
        if (!empty($uid)) {
            $criterias = [self::PARAM_ID => $uid];
            $this->cleanRowset();
            $this->find([], $criterias);
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
    public function getExpirationDate($offer)
    {
        $dateExp = '';
        if ($this->isValidOffer($offer)) {
            $dateFormat = 'Y-m-d H:i:s';
            $dateToday = new \DateTime(date($dateFormat));
            $prefixUnit = ($offer == 'demo') ? 'PT' : 'P';
            $suffixUnit = ($offer == 'demo') ? 'H' : 'D';
            $interval = $prefixUnit . $this->getOfferDaysValidity($offer)
                    . $suffixUnit;
            $dateInteval = new \DateInterval($interval);
            $dateExp = ($dateToday->add($dateInteval)->format($dateFormat));
        }
        return $dateExp;
    }

    /**
     * getOffers
     *
     * @return type
     */
    private function getOffers()
    {
        return ['demo', 'liberte', 'classique', 'premium'];
    }

    /**
     * getOfferInterval
     *
     * @param string $offer
     * @return string
     */
    private function getOfferDaysValidity($offer)
    {
        $daysValidity = [
            96 // Hours
            , 1095 // Days
            , 365 // Days
            , 365 // Days
        ];
        $offerInterval = array_combine($this->getOffers(), $daysValidity);
        return $offerInterval[$offer];
    }

    /**
     * isValidOffer
     *
     * @param string $offer
     * @return boolean
     */
    public function isValidOffer($offer)
    {
        return (boolean) in_array($offer, $this->getOffers());
    }

    /**
     * countOffersAjax
     *
     * @param string $offer
     * @return array
     */
    public function countOffersAjax()
    {
        $this->cleanRowset();
        return $this->find(
            ['offer'],
            ['offer' . '#' . 'in' => '("demo","liberte","premium","classique")'],
            [self::PARAM_ID => 'desc'],
            [15],
            'offer'
        )->getRowsetAsArray();
    }

    /**
     * countByRoleAjax
     *
     * @param string $offer
     * @return array
     */
    public function countByRoleAjax()
    {
        $this->cleanRowset();
        return $this->find(
            [self::PARAM_PROFIL],
            ['profil#in' => '("admin","user")'],
            [self::PARAM_ID => 'desc'],
            [15],
            self::PARAM_PROFIL
        )->getRowsetAsArray();
    }

    /**
     * countByStatusAjax
     *
     * @param string $offer
     * @return array
     */
    public function countByStatusAjax()
    {
        $this->cleanRowset();
        return $this->find(
            [self::PARAM_STATUS],
            [self::PARAM_STATUS . '#' . 'in' => '("valid","waiting")'],
            [self::PARAM_ID => 'desc'],
            [15],
            self::PARAM_STATUS
        )->getRowsetAsArray();
    }

    /**
     * countByCountriesAjax
     *
     * @param string $offer
     * @return array
     */
    public function countByCountriesAjax()
    {
        $this->cleanRowset();
        return $this->find(
            ['country'],
            [],
            [self::PARAM_ID => 'desc'],
            [15],
            'country'
        )->getRowsetAsArray();
    }

    /**
     * updateIp
     *
     */
    public function updateIp()
    {
        $this->cleanRowset();
        $id = \Pimvc\App::getInstance()->getRequest()->getSession(self::PARAM_ID);
        $user = $this->getById($id);
        $user->ip = $_SERVER['REMOTE_ADDR'];
        $this->save($user);
    }

    /**
     * getAllowedIps
     *
     * @return array
     */
    public function getAllowedIps()
    {
        $this->cleanRowset();
        $this->find(
            [self::PARAM_IP],
            [self::PARAM_IP . '#' . '!=' => ' '],
            [self::PARAM_ID => 'desc'],
            [],
            self::PARAM_IP
        );
        return $this->getRowsetAsArray();
    }

    /**
     * getListFromIdsIn
     *
     * @param array $in
     * @return array
     */
    public function getListFromIdsIn($in = [])
    {
        $this->cleanRowset();
        return $this->find(
            [self::PARAM_ID, self::PARAM_NAME],
            [self::PARAM_ID . '#' . 'in' => "('" . implode("','", $in) . "')"]
        )->getRowsetAsArray();
    }

    /**
     * getAuthByToken
     *
     * @param string $token
     * @return array
     */
    public function getAuthByToken($token)
    {
        $this->cleanRowset();
        $this->find(
            $this->userInfoFields,
            [self::PARAM_TOKEN => $token, self::PARAM_STATUS => 'valid']
        );
        return $this->getRowsetAsArray();
    }
}
