<?php

namespace App\Domain\Models\User;

use App\Domain\Models\IModel;

class User implements IModel
{
    private $id;
    private $name;
    private $password;
    private $email;
    private $roleId;
    private $isActivity;
    private $registrationDate;

    public function __construct(
        int $id,
        string $name,
        string $password,
        string $email,
        string $roleId,
        string $isActivity,
        \DateTime $registrationDate
    )
    {
        $this->id = $id;
        $this->name = $name;
        $this->password = $password;
        $this->email = $email;
        $this->roleId = $roleId;
        $this->isActivity = $isActivity;
        $this->registrationDate = $registrationDate;
    }

    public function getId():?int
    {
        return $this->id;
    }

    public function getName():string
    {
        return $this->name;
    }

    public function getPassword():string
    {
        return $this->password;
    }

    public function getEmail():string
    {
        return $this->email;
    }

    public function getRoleId():int
    {
        return $this->roleId;
    }

    public function isActivity():int
    {
        return $this->isActivity;
    }

    public function getRegistrationDate():string
    {
        return $this->registrationDate->setTimezone(new \DateTimeZone('europe/moscow'))
            ->format('Y-m-d H:i:s');
    }

    public function setRoleId(int $roleId)
    {
        $this->roleId = $roleId;
    }

    public function setId(int $id){
        $this->id = $id;
    }

    public function toArray(): array
    {
        return [
            'userId' => $this->getId(),
            'name' => $this->getName(),
            'password' => $this->getPassword(),
            'email' => $this->getEmail(),
            'roleId' => $this->getRoleId(),
            'isActivity' => $this->isActivity(),
            'registrationDate' => $this->getRegistrationDate()
        ];
    }
}