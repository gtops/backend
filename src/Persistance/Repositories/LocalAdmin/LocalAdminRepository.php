<?php

namespace App\Persistance\Repositories\LocalAdmin;
use App\Domain\Models\IModel;
use App\Domain\Models\IRepository;
use App\Domain\Models\LocalAdmin\LocalAdmin;
use App\Domain\Models\LocalAdmin\LocalAdminNotFoundException;
use App\Domain\Models\Organization;
use App\Persistance\Repositories\User\UserRepository;
use App\Persistance\ModelsEloquant\LocalAdmin\LocalAdmin as LocalAdminEloquant;
use function MongoDB\BSON\toRelaxedExtendedJSON;

class LocalAdminRepository implements IRepository
{
    private $userRepository;
    public function __construct()
    {
        $this->userRepository = new UserRepository();
    }

    public function get(int $id): ?IModel
    {
    }

    /**
     * @inheritDoc
     */
    public function getAll(): ?array
    {
        // TODO: Implement getAll() method.
    }

    /**@var $model LocalAdmin*/
    public function add(IModel $model):int
    {
        if (!($model instanceof LocalAdmin)){
            throw new LocalAdminNotFoundException();
        }
        return LocalAdminEloquant::query()->create([
            'user_id' => $model->getUser()->getId(),
            'organization_id' => $model->getOrganizationId()
        ])->getAttribute('local_admin_id');
    }

    public function localAdminIsSetOnDB(string $email, int $organizationId):bool
    {
        $res = LocalAdminEloquant::query()->join('user', 'local_admin.user_id', '=', 'user.user_id')->where([
            'user.email' => $email,
            'local_admin.organization_id' => $organizationId
        ])->get();

        if (count($res) == 0){
            return false;
        }

        return true;
    }

    public function delete(int $id)
    {
        // TODO: Implement delete() method.
    }

    public function update(Organization $organization)
    {
        // TODO: Implement update() method.
    }
}