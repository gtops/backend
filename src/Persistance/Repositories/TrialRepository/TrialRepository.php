<?php
/**
 * Created by PhpStorm.
 * User: Admin
 * Date: 17.10.2019
 * Time: 23:44
 */

namespace App\Persistance\Repositories\TrialRepository;


use App\Domain\Models\Trial;
use App\Persistance\ModelsEloquant\AgeCategory\AgeCategory;
use App\Persistance\ModelsEloquant\ResultGuide\ResultGuide;
use Illuminate\Database\Capsule\Manager as Capsule;
use Monolog\Logger;

class TrialRepository
{
    public function getNameOfAgeCategory(int $age)
    {
        $result = AgeCategory::query()
            ->where('min_age', '<=', $age)
            ->where('max_age', '>=', $age)
            ->get();

        return $result[0]->name_age_category;
    }

    public function getList(int $gender, int $age, Capsule $capsule):array
    {
        $ageCategories = AgeCategory::query()
            ->where('max_age', '<=', $age)
            ->orderByDesc('max_age')
            ->limit(1)
            ->get();

        $idAgeCategory = $ageCategories[0]->id_age_category;

        $results =  ResultGuide::query()
            ->leftJoin('trial', 'trial.id_trial', '=', 'result_guide.id_trial')
            ->where('id_age_category', '=', $idAgeCategory)
            ->where('gender', '=', $gender)
            ->get();

        $response = [];

        foreach ($results as $result)
        {
            $silver = str_replace(',', '.', $result->result_for_silver);
            $bronze = str_replace(',', '.', $result->result_for_bronze);
            $gold = str_replace(',', '.', $result->result_for_gold);
            //добавить фильтрацию для времени в минутах и секундах
            $response[] = new Trial($result->trial, $result->id_result_guide, (float)$silver, (float)$bronze,
               (float) $gold, 0);
        }

        return $response;
    }

    public function getSecondResult(float $firstResult, int $allDataStandardId):int
    {
        $logger = new Logger('a');
        $translatorModels = ResultGuide::query()
            ->where('id_result_guide', '=', $allDataStandardId)
            ->get();

        $values = $translatorModels[0]->results;
        $values = explode(';', $values);

        return $this->getTranslatedResult($values, $firstResult);
    }

    private function getTranslatedResult(array $results, float $firstResult):int
    {
        for($i = 0; $i < count($results); $i++){
           $keyValue = explode('=', $results[$i]);
           if ($keyValue[1] <= $firstResult){
               return $keyValue[0];
           }
        }

        return 0;
    }
}