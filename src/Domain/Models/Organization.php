<?php


namespace App\Domain\Models;


class Organization implements IModel
{
    private $id;
    private $name;
    private $address;
    private $leader;
    private $phoneNumber;
    private $oqrn;
    private $paymentAccount;
    private $branch;
    private $bik;
    private $correspondentAccount;

    public function __construct
    (
        string $id,
        string $name,
        string $address,
        string $leader,
        string $phoneNumber,
        string $oqrn,
        string $paymentAccount,
        string $branch,
        string $bik,
        string $correspondentAccount
    )
    {
        $this->id = $id;
        $this->name = $name;
        $this->address = $address;
        $this->leader = $leader;
        $this->phoneNumber = $phoneNumber;
        $this->oqrn = $oqrn;
        $this->paymentAccount = $paymentAccount;
        $this->branch = $branch;
        $this->bik = $bik;
        $this->correspondentAccount = $correspondentAccount;
    }

    public function getId():string
    {
        return $this->id;
    }

    public function getName():string
    {
        return $this->name;
    }

    public function getAddress():string
    {
        return $this->address;
    }

    public function getLeader():string
    {
        return $this->leader;
    }

    public function getBik():string
    {
        return $this->bik;
    }

    public function getBranch():string
    {
        return $this->branch;
    }

    public function getCorrespondentAccount():string
    {
        return $this->correspondentAccount;
    }

    public function getPhoneNumber():string
    {
        return $this->phoneNumber;
    }

    public function getPaymentAccount():string
    {
        return $this->paymentAccount;
    }

    public function getOqrn():string
    {
        return $this->oqrn;
    }

    public function toArray(): array
    {
        return [
            'id' => $this->getId(),
            'name' => $this->getName(),
            'address' => $this->getAddress(),
            'leader' => $this->getLeader(),
            'phone_number' => $this->getPhoneNumber(),
            'OQRN' => $this->getOqrn(),
            'payment_account' => $this->getPaymentAccount(),
            'branch' => $this->getBranch(),
            'bik' => $this->getBik(),
            'correspondent_account' => $this->getCorrespondentAccount()
        ];
    }
}