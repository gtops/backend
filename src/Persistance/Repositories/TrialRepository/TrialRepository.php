<?php
/**
 * Created by PhpStorm.
 * User: Admin
 * Date: 17.10.2019
 * Time: 23:44
 */

namespace App\Persistance\Repositories\TrialRepository;


use App\Domain\Models\IModel;
use App\Domain\Models\IRepository;
use App\Domain\Models\Organization;
use App\Domain\Models\Trial;
use App\Persistance\ModelsEloquant\AgeCategory\AgeCategory;
use App\Persistance\ModelsEloquant\ResultGuide\ResultGuide;
use Illuminate\Database\Capsule\Manager as Capsule;
use Monolog\Logger;

class TrialRepository implements IRepository
{
    public function getNameOfAgeCategory(int $age)
    {
        $result = AgeCategory::query()
            ->where('min_age', '<=', $age)
            ->where('max_age', '>=', $age)
            ->get();

        return $result[0]->name_age_category;
    }

    public function getList(int $gender, int $age):array
    {
        $ageCategories = AgeCategory::query()
            ->where('max_age', '<=', $age)
            ->orderByDesc('max_age')
            ->limit(1)
            ->get();

        $idAgeCategory = $ageCategories[0]->id_age_category;

        $results =  ResultGuide::query()
            ->leftJoin('trial', 'trial.id_trial', '=', 'result_guide.id_trial')
            ->leftJoin('group_result_guide', 'result_guide.id_group_result_guide', '=', 'group_result_guide.id_group_result_guide')
            ->where('result_guide.id_age_category', '=', $idAgeCategory)
            ->where('gender', '=', $gender)
            ->orderBy('result_guide.id_group_result_guide')
            ->get();


        $response = [];
        $logger = new Logger('a');
        foreach ($results as $result)
        {
            $silver = str_replace(',', ':', $result->result_for_silver);
            $logger->alert($result->result_for_silver);
            $bronze = str_replace(',', ':', $result->result_for_bronze);
            $gold = str_replace(',', ':', $result->result_for_gold);
            //добавить фильтрацию для времени в минутах и секундах
            $response[] = new Trial($result->trial, $result->id_result_guide, $silver, $bronze,
                $gold, 0, $result->necessarily, $result->id_group_result_guide, $result->type_time);
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

    private function getTranslatedResult(array $results, string $firstResult):int
    {
        for($i = 0; $i < count($results) - 1; $i++){
           $keyValue = explode('=', $results[$i]);
           if ($keyValue[1] <= $firstResult){
               return $keyValue[0];
           }
        }

        return 0;
    }

    public function getFilteredByEventId(int $id): IModel
    {
        // TODO: Implement get() method.
    }

    /**
     * @inheritDoc
     */
    public function getAll(): array
    {
        // TODO: Implement getAll() method.
    }

    public function add(IModel $model):int
    {
        // TODO: Implement add() method.
    }

    public function delete(int $id)
    {
        // TODO: Implement delete() method.
    }

    public function update(IModel $organization)
    {
        // TODO: Implement update() method.
    }
}