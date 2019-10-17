<?php
/**
 * Created by PhpStorm.
 * User: Admin
 * Date: 17.10.2019
 * Time: 8:07
 */

namespace App\Persistance\ModelsEloquant\AgeCategory;
use \Illuminate\Database\Eloquent\Model;

class AgeCategory extends Model
{
    public $timestamps = false;
    protected $table = "age_category";
    protected $primaryKey = "id_age_category";
    protected $fillable = array(
        "name_age_category",
        "min_age",
        "max_age"
    );
}