<?php
/**
 * Created by PhpStorm.
 * User: Admin
 * Date: 18.10.2019
 * Time: 0:20
 */

namespace App\Services\Presenters;
use App\Domain\Models\Trial;
use App\Services\Presenters\PresenterInterface;


class TrialToResponsePresenter implements PresenterInterface
{
    public $standard;

    public function __construct(Trial $standard)
    {
        $this->standard = $standard;
    }

    public function __get($name):Trial
    {
        return $this->standard;
    }

    public function __set($name, Trial $value) : void
    {
        $this->standard = $value;
    }

    public function getView():array
    {
        return [
            'trialName' => $this->standard->getTrialName(),
            'trialId' => $this->standard->getTrialId(),
            'resultForBronze' => $this->standard->getResultForBronze(),
            'resultForSilver' => $this->standard->getResultForSilver(),
            'resultForGold' => $this->standard->getResultForGold()
        ];
    }
}