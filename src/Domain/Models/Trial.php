<?php
/**
 * Created by PhpStorm.
 * User: Admin
 * Date: 17.10.2019
 * Time: 23:48
 */

namespace App\Domain\Models;


class Trial implements IModel
{
    private $trialName;
    private $trialId;
    private $resultForSilver;
    private $resultForBronze;
    private $resultForGold;
    private $secondResult;
    private $necessarily;
    private $idGroupInAgeCategory;
    private $typeTime;

    public function __construct(string $trialName, int $trialId, string $resultForSilver, string $resultForBronze, string $resultForGold, string $seccndResult, bool $necessarily, int $idGroupInAgeCategory, bool $typeTime)
    {
        $this->trialName = $trialName;
        $this->trialId = $trialId;
        $this->resultForSilver = $resultForSilver;
        $this->resultForBronze = $resultForBronze;
        $this->secondResult = $seccndResult;
        $this->resultForGold = $resultForGold;
        $this->necessarily = $necessarily;
        $this->idGroupInAgeCategory = $idGroupInAgeCategory;
        $this->typeTime = $typeTime;
    }

    public function getTypeTime():bool
    {
        return $this->typeTime;
    }

    public function getNecessarily():bool
    {
        return $this->necessarily;
    }

    public function getIdGroup():int
    {
        return $this->idGroupInAgeCategory;
    }

    public function getTrialName():string
    {
        return $this->trialName;
    }

    public function getTrialId():int
    {
        return $this->trialId;
    }

    public function getResultForSilver():string
    {
        return $this->resultForSilver;
    }

    public function getResultForBronze():string
    {
        return $this->resultForBronze;
    }

    public function getResultForGold():string
    {
        return $this->resultForGold;
    }

    public function getSecondResult():int
    {
        return $this->secondResult;
    }

    public function toArray(): array
    {
        return [
                'trialName' => $this->getTrialName(),
                'trialId' => $this->getTrialId(),
                'resultForSilver' => $this->getResultForSilver(),
                'resultForBronze' => $this->getResultForBronze(),
                'resultForGold' => $this->getResultForGold(),
                'secondResult' => $this->getSecondResult(),
                'necessarily' => $this->getNecessarily(),
                'idGroupInAgeCategory' => $this->getIdGroup(),
                'typeTime' => $this->getTypeTime()
        ];
    }
}