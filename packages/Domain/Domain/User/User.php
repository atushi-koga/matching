<?php
declare(strict_types=1);

namespace packages\Domain\Domain\User;

use Carbon\Carbon;
use packages\Domain\Domain\Common\Prefecture;

class User
{
    /** @var int */
    private $id;

    /** @var string */
    private $nickname;

    /** @var Prefecture */
    private $prefecture;

    /** @var Gender */
    private $gender;

    /** @var BirthDay */
    private $birthday;

    /** @var Email */
    private $email;

    /** @var Password */
    private $password;

    /**
     * User constructor.
     *
     * @param string     $nickname
     * @param Prefecture $prefecture
     * @param Gender     $gender
     * @param BirthDay   $birthday
     * @param Email      $email
     * @param Password   $password
     */
    public function __construct(
        string $nickname,
        Prefecture $prefecture,
        Gender $gender,
        BirthDay $birthday,
        Email $email,
        Password $password
    ) {
        $this->nickname   = $nickname;
        $this->prefecture = $prefecture;
        $this->gender     = $gender;
        $this->birthday   = $birthday;
        $this->email      = $email;
        $this->password   = $password;
    }

    /**
     * @return int
     */
    public function getId(): int
    {
        return $this->id;
    }

    /**
     * @return string
     */
    public function getNickName(): string
    {
        return $this->nickname;
    }

    /**
     * @return mixed
     */
    public function getPrefectureKey()
    {
        return $this->prefecture->getKey();
    }

    /**
     * @return string
     */
    public function getPrefectureValue(): string
    {
        return $this->prefecture->getValue();
    }

    /**
     * @return mixed
     */
    public function getGenderKey()
    {
        return $this->gender->getKey();
    }

    /**
     * @return string
     */
    public function getGenderValue(): string
    {
        return $this->gender->getValue();
    }

    /**
     * @return Carbon
     */
    public function getBirthDate(): Carbon
    {
        return $this->birthday->getBirthDate();
    }

    /**
     * @return string
     */
    public function getFormatBirthDate(): string
    {
        return $this->birthday->getFormatBirthDate();
    }

    /**
     * @return string
     */
    public function getEmail(): string
    {
        return $this->email->getValue();
    }

    /**
     * @return string
     */
    public function getPassword(): string
    {
        return $this->password->getHash();
    }

    /**
     * @param int $id
     */
    public function setId(int $id)
    {
        $this->id = $id;
    }
}