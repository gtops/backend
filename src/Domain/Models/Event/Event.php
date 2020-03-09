<?php

namespace App\Domain\Models\Event;
use App\Domain\Models\IModel;

class Event implements IModel
{
    private $id;
    private $idOrganization;
    private $name;
    private $startDate;
    private $expirationDate;
    private $descrition;

    public function __construct(int $id, int $idOrganization, string $name, \DateTime $startDate, \DateTime $expirationDate, $descrition = '')
    {
        $this->id = $id;
        $this->idOrganization = $idOrganization;
        $this->name = $name;
        $this->startDate = $startDate;
        $this->expirationDate = $expirationDate;
        $this->descrition = $descrition;
    }

    public function getId():int
    {
        return $this->id;
    }

    public function getIdOrganization():int
    {
        return $this->idOrganization;
    }

    public function getName():string
    {
        return $this->name;
    }

    public function getStartDate():\DateTime
    {
        return $this->startDate;
    }

    public function getExpirationDate():\DateTime
    {
        return $this->expirationDate;
    }

    public function getDescription():string
    {
        return $this->descrition;
    }

    public function toArray(): array
    {
        return [
            'id' => $this->getId(),
            'idOrganization' => $this->getIdOrganization(),
            'name' => $this->getName(),
            'startDate' => $this->getStartDate()->format('Y-m-d H:i:s'),
            'expirationDate' => $this->getExpirationDate()->format('Y-m-d H:i:s'),
            'description' => $this->getDescription()
        ];
    }
}