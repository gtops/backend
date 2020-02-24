<?php
namespace App\Services\LocalAdmin;


use App\Application\Actions\ActionError;
use App\Application\Middleware\AuthorizeMiddleware;
use App\Domain\Models\LocalAdmin\LocalAdmin;
use App\Domain\Models\Role\Role;
use App\Domain\Models\User\UserCreater;
use App\Persistance\Repositories\LocalAdmin\LocalAdminRepository;
use App\Persistance\Repositories\Organization\OrganizationRepository;
use App\Persistance\Repositories\Role\RoleRepository;
use App\Persistance\Repositories\User\UserRepository;
use App\Services\Token\Token;
use Psr\Http\Message\ResponseInterface;
use Slim\Psr7\Response;

class LocalAdminService
{
    private $localAdminRepository;
    private $roleRepository;
    private $userRepository;
    private $organizationRepository;

    public function __construct(LocalAdminRepository $localAdminRepository, RoleRepository $roleRepository, UserRepository $userRepository, OrganizationRepository $organizationRepository)
    {
        $this->localAdminRepository = $localAdminRepository;
        $this->roleRepository = $roleRepository;
        $this->userRepository = $userRepository;
        $this->organizationRepository = $organizationRepository;
    }

    public function addWithoutMessage(string $name, string $password, string $email, int $organizationId, ResponseInterface $response)
    {
        if ($this->organizationRepository->get($organizationId) == null) {
            $response->getBody()->write(json_encode(['errors' => array(new ActionError(ActionError::BAD_REQUEST, 'такой организации не существует'))]));
            return $response->withStatus(400);
        }

        if ($this->localAdminRepository->localAdminIsSetOnDB($email, $organizationId)) {
            $response->getBody()->write(json_encode(['errors' => array(new ActionError(ActionError::BAD_REQUEST, 'такой локальный администратор в данной организации существует'))]));
            return $response->withStatus(400);
        }

        $user = $this->userRepository->getByEmail($email);
        $userId = -1;
        if ($user == null) {
            $roles = $this->roleRepository->getAll();
            $roleId = $this->getRoleIdWithName(AuthorizeMiddleware::LOCAL_ADMIN, $roles);

            $rowParams = [
                'id' => $userId,
                'name' => $name,
                'password' => Token::getEncodedPassword($password),
                'email' => $email,
                'roleId' => $roleId,
                'isActivity' => 1,
                'dateTime' => new \DateTime()
            ];

            $user = UserCreater::createModel($rowParams);
            $userId = $this->userRepository->add($user);
            $user->setId($userId);
        }


        return $this->localAdminRepository->add(new LocalAdmin($user, $organizationId, -1));
    }

    public function update()
    {

    }

    public function delete(int $localAdminId, int $organizationId, ResponseInterface $response)
    {
        /**@var $localAdmin LocalAdmin*/
        $localAdmin = $this->localAdminRepository->get($localAdminId);

        if ($localAdmin == null){
            return $response->withStatus(200);
        }

        if ($localAdmin->getOrganizationId() != $organizationId){
            $response->getBody()->write(json_encode(['errors' => array(new ActionError(ActionError::BAD_REQUEST, 'данный локальный администратор не относится к переданной организации'))]));
            return $response->withStatus(400);
        }

        $this->localAdminRepository->delete($localAdminId);
    }

    public function get(int $id): ?LocalAdmin
    {

    }

    public function getAll():array
    {

    }

    private function getRoleIdWithName(string $name, array $roles):?int
    {
        foreach ($roles as $role){
            if ($role['name_of_role'] == $name){
                return  $role['role_id'];
            }
        }

        return null;
    }
}