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
    public static function getView(array $trials, $results = null):array
    {
        /** @var  $trial Trial */
        $responseData = [];
        $itemsOfGroup = [];

        $groupId = -1;
        $tempArray = [];
        $allCount = count($trials);
        $counter = 1;
        foreach ($trials as $trial) {
            if (self::itemInArray($tempArray, $trial)){
                continue;
            }

            if ($trial->getIdGroup() != $groupId && $groupId != -1){
                $responseData[] = $itemsOfGroup;
                $itemsOfGroup = [];
                $groupId = $trial->getIdGroup();
            }

            if ($groupId == -1 || $groupId == $trial->getIdGroup()){
                if($results === null) {
                    $itemsOfGroup['group'][] = self::getTrialVIew($trial);
                }

                if (count($results) == 0){
                    $itemsOfGroup['group'][] = self::getTrialWithNullResults($trial);
                }

                $itemsOfGroup['necessary'] = $trial->getNecessarily();
                $groupId = $trial->getIdGroup();
            }

            if ($counter == $allCount)
            {
                $responseData[] = $itemsOfGroup;
                $itemsOfGroup = [];
                $groupId = $trial->getIdGroup();
            }
            $counter++;
        }

        return  $responseData;
    }

    /**@var $tempArray Trial[]*/
    private static function itemInArray(array $tempArray, Trial $item)
    {
        foreach ($tempArray as $trial){
            if ($trial->getResultGuideId() == $item->getResultGuideId()){
                return true;
            }
        }

        return false;
    }

    public static function getTrialVIew(Trial $trial):array
    {
        return [
            'trialName' => $trial->getTrialName(),
            'trialId' => $trial->getResultGuideId(),
            'resultForBronze' => $trial->getResultForBronze(),
            'resultForSilver' => $trial->getResultForSilver(),
            'resultForGold' => $trial->getResultForGold(),
            'typeTime' => $trial->getTypeTime()
        ];
    }

    private static function getTrialWithNullResults(Trial $trial)
    {
        return [
            'trialName' => $trial->getTrialName(),
            'trialId' => $trial->getResultGuideId(),
            'typeTime' => $trial->getTypeTime(),
            'firstResult' => null,
            'secondResult' => null,
            'badge' => null
        ];
    }
}