<?php


namespace App\Domain\Models;


interface IRepository
{
    /**
     * @return IModel[]
    */
    public function getAll():?array ;
    public function add(IModel $model):int;
    public function delete(int $id);
    public function update(IModel $model);
}