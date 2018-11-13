<?php
/**
 * Description of Users
 *
 * @author pierrefromager
 */
namespace Pimvc\Model\Interfaces;

interface Users
{

    const _ID = 'id';
    const _NAME = 'name';
    const _EMAIL = 'email';
    const _PASSWORD = 'password';
    const _PROFIL = 'profil';
    const _FID = 'fid';
    const _IP = 'ip';
    const _STATUS = 'status';
    const _LOGIN = 'login';
    const _TOKEN = 'token';
    const USERS_STATUS_VALID = 'valid';
    const USERS_STATUS_WAITING = 'waiting';

    public function getAuth($login, $password);

    public function getPro();

    public function getStatus();

    public function getAll();

    public function getById($id, $what = ['*']);

    public function getWaitings();

    public function getValids($what);

    public function count();

    public function getList($name);

    public function validCredential($credential);

    public function userExists($login);

    public function getByEmail($email, $what = ['*']);

    public function validate($id);

    public function countEmailsByProfil($profil = 'pros');

    public function getEmailsByProfil($profil = 'pros');

    public function isValid($uid);

    public function countExpirationDays($uid);

    public function isExpired($uid);

    public function setExpirationDate($uid, $offer);

    public function getExpirationDate($offer);

    public function isValidOffer($offer);

    public function countOffersAjax();

    public function countByRoleAjax();

    public function countByStatusAjax();

    public function countByCountriesAjax();

    public function updateIp();

    public function getAllowedIps();

    public function getListFromIdsIn($in = []);

    public function getAuthByToken($token);
}
