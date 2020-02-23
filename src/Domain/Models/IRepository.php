<?php


namespace App\Domain\Models;


interface IRepository
{
    /**
     * @return IModel[]
     */
    public function get(int $id):?IModel;
    /**
     * @return IModel[]
    */
    public function getAll():?array ;
    public function add(IModel $model);
    public function delete(int $id);
}