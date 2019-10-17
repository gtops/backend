<?php
/**
 * Created by PhpStorm.
 * User: Admin
 * Date: 17.10.2019
 * Time: 23:48
 */

namespace App\Domain\Models;


class Trial
{
    private $trialName;
    private $trialId;
    private $resultForSilver;
    private $resultForBronze;
    private $resultForGold;
    private $secondResult;

    public function __construct(string $trialName, int $trialId, float $resultForSilver, float $resultForBronze, float $resultForGold, int $seccndResult)
    {
        $this->trialName = $trialName;
        $this->trialId = $trialId;
        $this->resultForSilver = $resultForSilver;
        $this->resultForBronze = $resultForBronze;
        $this->secondResult = $seccndResult;
        $this->resultForGold = $resultForGold;
    }

    public function getTrialName():string
    {
        return $this->trialName;
    }

    public function getTrialId():int
    {
        return $this->trialId;
    }

    public function getResultForSilver():float
    {
        return $this->resultForSilver;
    }

    public function getResultForBronze():float
    {
        return $this->resultForBronze;
    }

    public function getResultForGold():float
    {
        return $this->resultForGold;
    }

    public function getSecondResult():int
    {
        return $this->secondResult;
    }
}