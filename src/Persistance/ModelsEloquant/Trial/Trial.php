<?php
/**
 * Created by PhpStorm.
 * User: Admin
 * Date: 17.10.2019
 * Time: 23:21
 */

namespace App\Persistance\ModelsEloquant\Trial;


use Illuminate\Database\Eloquent\Model;

class Trial extends Model
{
    public $timestamps = false;
    protected $table = "name_sports";
    protected $primaryKey = "id_name_sport";

    protected $fillable = array(
        "name_sport"
    );
}