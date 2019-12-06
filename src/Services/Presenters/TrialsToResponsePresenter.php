<?php
/**
 * Created by PhpStorm.
 * User: Admin
 * Date: 18.10.2019
 * Time: 0:20
 */

namespace App\Services\Presenters;
use App\Domain\Models\Trial;


class TrialsToResponsePresenter
{
    public static function getView(array $trials):array
    {
        /** @var  $trial Trial */
        $responseData = [];
        $arrayTrials = [];
        $trialsView = [];
        $groupId = -1;

        foreach ($trials as $trial){

            if ($trial->getIdGroup() != $groupId)
            {
                $arrayTrials['group'] = $trialsView;

                if ($groupId != -1) {
                    $responseData[] = $arrayTrials;
                }

                $arrayTrials = [];
                $groupId = $trial->getIdGroup();

                $arrayTrials['necessary'] = $trial->getNecessarily();

                $trialsView = [];
            }

            $trialsView[] = TrialsToResponsePresenter::getTrialVIew($trial);
        }

        return$responseData;
    }

    public static function getTrialVIew(Trial $trial):array
    {
        return [
            'trialName' => $trial->getTrialName(),
            'trialId' => $trial->getTrialId(),
            'resultForBronze' => $trial->getResultForBronze(),
            'resultForSilver' => $trial->getResultForSilver(),
            'resultForGold' => $trial->getResultForGold(),
            'typeTime' => $trial->getTypeTime()
        ];
    }
}