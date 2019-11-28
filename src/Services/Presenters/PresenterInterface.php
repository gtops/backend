<?php
/**
 * Created by PhpStorm.
 * User: Admin
 * Date: 18.10.2019
 * Time: 0:19
 */

namespace App\Services\Presenters;


use App\Domain\Models\Trial;

Interface PresenterInterface
{
    public static function getView(Trial $trial):array ;
}