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
use App\Persistance\ModelsEloquant\DataBase;
use App\Persistance\ModelsEloquant\TableTranslator\TableTranslator;
use Illuminate\Database\Capsule\Manager as Capsule;
use Illuminate\Support\Facades\Log;
use Monolog\Logger;

class TrialRepository
{
    public function getList(int $gender, int $age, Capsule $capsule):array
    {
        $ageCategories = AgeCategory::query()
            ->where('max_age', '<=', $age)
            ->orderByDesc('max_age')
            ->limit(1)
            ->get();

        $idAgeCategory = $ageCategories[0]->id_age_category;

        $results =  TableTranslator::query()
            ->leftJoin('name_sports', 'name_sports.id_name_sport', '=', 'all_data_of_standard.id_name_sport')
            ->where('id_age_category', '=', $idAgeCategory)
            ->where('gender', '=', $gender)
            ->get();

        $response = [];

        foreach ($results as $result)
        {
            $response[] = new Trial($result->name_sport, $result->id_all_data_standard, $result->result_for_silver, $result->result_for_bronze,
                $result->result_for_gold, 0);
        }

        return $response;
    }

    public function getSecondResult(float $firstResult, int $allDataStandardId):int
    {
        $logger = new Logger('a');
        $translatorModels = TableTranslator::query()
            ->where('id_all_data_standard', '=', $allDataStandardId)
            ->get();

        $values = $translatorModels[0]->value_for_100;
        $logger->alert($values);
        $values = explode(';', $values);

//        for($i = 0; $i < count($values); $i++){
//            if ((int)$values[$i] )
//        }

        $logger->alert(count($values));
        return -1;
    }
}