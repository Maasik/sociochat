<?php

namespace SocioChat\DAO;

use Core\DAO\DAOBase;
use Core\Utils\DbQueryHelper;

class ActivationsDAO extends DAOBase
{
    const EMAIL = 'email';
    const CODE = 'code';
    const TIMESTAMP = 'timestamp';
    const USED = 'used';

    protected $types = [
        self::USED => \PDO::PARAM_BOOL,
    ];

    public function __construct()
    {
        parent::__construct(
            [
                self::EMAIL,
                self::CODE,
                self::TIMESTAMP,
                self::USED,
            ]
        );

        $this->dbTable = 'activations';
    }

    public function getEmail()
    {
        return $this[self::EMAIL];
    }

    public function getTimestamp()
    {
        return $this[self::TIMESTAMP];
    }

    public function getCode()
    {
        return $this[self::CODE];
    }

    public function getIsUsed()
    {
        return $this[self::USED];
    }

    public function getByEmail($email)
    {
        return $this->getByPropId(self::EMAIL, $email);
    }

    public function setEmail($email)
    {
        $this[self::EMAIL] = $email;
        return $this;
    }

    public function setCode($code)
    {
        $this[self::CODE] = $code;
        return $this;
    }

    public function setTimestamp($time)
    {
        $this[self::TIMESTAMP] = $time;
        return $this;
    }

    public function setIsUsed($bool)
    {
        $this[self::USED] = $bool;
        return $this;
    }

    public function getActivation($email, $code)
    {
        return $this->getListByQuery(
            "SELECT * FROM {$this->dbTable} WHERE ".self::EMAIL." = :email AND ".self::CODE." = :code AND ".self::USED
            ." = :used LIMIT 1",
            [
                'email' => $email,
                'code' => $code,
                'used' => 'false'
            ]
        );
    }

    public function dropUsedActivations()
    {
        return $this->db->exec(
	        "DELETE FROM {$this->dbTable} WHERE ".self::USED." = :used OR ".self::TIMESTAMP." < :threshold",
	        [
		        'used' => 'true',
		        'threshold' => DbQueryHelper::timestamp2date(time() - 3600*2)
	        ]
        );
    }

    protected function getForeignProperties()
    {
        return [];
    }
}
