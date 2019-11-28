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
    public static function getView(Trial $standard):array
    {
        return [
            'trialName' => $standard->getTrialName(),
            'trialId' => $standard->getTrialId(),
            'resultForBronze' => $standard->getResultForBronze(),
            'resultForSilver' => $standard->getResultForSilver(),
            'resultForGold' => $standard->getResultForGold()
        ];
    }
}