<?php

namespace App\Persistance\Repositories\LocalAdmin;
use App\Domain\Models\IModel;
use App\Domain\Models\IRepository;
use App\Domain\Models\LocalAdmin\LocalAdmin;
use App\Domain\Models\LocalAdmin\LocalAdminNotFoundException;
use App\Domain\Models\Organization;
use App\Domain\Models\User\UserCreater;
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
        $result = LocalAdminEloquant::query()->join('user', 'user.user_id', '=', 'local_admin.user_id')->get([
            'user.user_id',
            'user.name',
            'user.email',
            'user.role_id',
            'user.is_activity',
            'user.registration_date',
            'local_admin.organization_id',
            'local_admin.local_admin_id'
        ]);

        if (count($result) == 0){
            return null;
        }

        $user = UserCreater::createModel([
            'id' => $result[0]['user_id'],
            'name' => $result[0]['name'],
            'password' => '',
            'email' => $result[0]['email'],
            'roleId' => $result[0]['role_id'],
            'dateTime' => new \DateTime($result[0]['registration_date']),
            'isActivity' => $result[0]['is_activity']
        ]);

        return new LocalAdmin($user, $result[0]['organization_id'], $result[0]['local_admin_id']);
    }

    /**
     * @inheritDoc
     */
    public function getAll(): ?array
    {
        $results = LocalAdminEloquant::query()->join('user', 'user.user_id', '=', 'local_admin.user_id')->get([
            'user.user_id',
            'user.name',
            'user.email',
            'user.role_id',
            'user.is_activity',
            'user.registration_date',
            'local_admin.organization.id'
        ]);

        if (count($results) == 0){
            return null;
        }

        $localAdmins = [];
        foreach ($results as $result){
            $localAdmins[] = $result;
        }

        return $localAdmins;
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
        LocalAdminEloquant::query()->where('local_admin_id', '=', $id)->delete();
    }

    public function update(Organization $organization)
    {
        // TODO: Implement update() method.
    }
}