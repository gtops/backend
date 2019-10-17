<?php
/**
 * Created by PhpStorm.
 * User: Admin
 * Date: 17.10.2019
 * Time: 23:35
 */

namespace App\Persistance\ModelsEloquant\GroupStandard;


use Illuminate\Database\Eloquent\Model;

class GroupStandard extends Model
{
    public $timestamps = false;
    protected $table = "age_category";
    protected $primaryKey = "id_group_standard";
    protected $fillable = array(
        "id_age_category"
    );
}