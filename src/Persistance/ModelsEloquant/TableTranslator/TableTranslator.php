<?php
/**
 * Created by PhpStorm.
 * User: Admin
 * Date: 17.10.2019
 * Time: 12:53
 */

namespace App\Persistance\ModelsEloquant\TableTranslator;


use Illuminate\Database\Eloquent\Model;

class TableTranslator extends Model
{
    public $timestamps = false;
    protected $table = "all_data_of_standard";
    protected $primaryKey = "id_all_data_standard";
    protected $fillable = array(
        "id_age_category",
        "id_name_sport",
        "gender",
        "value_for_100",
        "id_version",
        "id_group_standard_in_age_category",
        "result_for_gold",
        "result_for_silver",
        "result_for_bronze"
    );
}