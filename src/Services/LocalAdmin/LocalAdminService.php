<?php
namespace App\Services\LocalAdmin;


use App\Application\Actions\ActionError;
use App\Application\Middleware\AuthorizeMiddleware;
use App\Domain\Models\IModel;
use App\Domain\Models\LocalAdmin\LocalAdmin;
use App\Domain\Models\Role\Role;
use App\Domain\Models\User\UserCreater;
use App\Persistance\Repositories\LocalAdmin\LocalAdminRepository;
use App\Persistance\Repositories\Organization\OrganizationRepository;
use App\Persistance\Repositories\Role\RoleRepository;
use App\Persistance\Repositories\User\UserRepository;
use App\Services\Token\Token;
use DateTime;
use http\Client\Curl\User;
use Psr\Http\Message\ResponseInterface;
use Slim\Psr7\Response;
use function GuzzleHttp\Psr7\normalize_header;

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

    public function add(string $name, string $password, string $email, $gender, DateTime $dateOfBirth, int $organizationId, ResponseInterface $response)
    {
        $response = $this->getInitedResponseWitStatus($organizationId, $email, $response);
        if ($response->getStatusCode() != 200){
            return $response;
        }

        $user = $this->userRepository->getByEmail($email);
        $userId = -1;
        $roles = $this->roleRepository->getAll();
        $roleId = $this->getRoleIdWithName(AuthorizeMiddleware::LOCAL_ADMIN, $roles);

        if ($user == null) {
            $rowParams = [
                'id' => $userId,
                'name' => $name,
                'password' => Token::getEncodedPassword($password),
                'email' => $email,
                'roleId' => $roleId,
                'isActivity' => 1,
                'dateTime' => new DateTime(),
                'gender'=> $gender,
                'dateOfBirth' => $dateOfBirth
            ];

            $user = UserCreater::createModel($rowParams);
            $userId = $this->userRepository->add($user);
            $user->setId($userId);
        }else{
            $response->getBody()->write(json_encode(['errors' => array(new ActionError(ActionError::BAD_REQUEST, 'такой пользователь уже существует'))]));
            return $response->withStatus(400);
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

        $roles = $this->roleRepository->getAll();
        $roleId = $this->getRoleIdWithName(AuthorizeMiddleware::SIMPLE_USER, $roles);
        $user = $this->userRepository->getByEmail($localAdmin->getUser()->getEmail());
        $user->setRoleId($roleId);
        $this->userRepository->update($user);
        $this->localAdminRepository->delete($localAdminId);
    }

    public function get(int $id, int $organizationId): ?IModel
    {
        /**@var $localAdmin LocalAdmin*/
        $localAdmin = $this->localAdminRepository->get($id);
        if ($localAdmin == null){
            return null;
        }

        if ($localAdmin->getOrganizationId() != $organizationId){
            return null;
        }

        return $localAdmin;
    }

    public function getAll(int $organizationId):?array
    {
        $response = [];
        $localAdmins = $this->localAdminRepository->getAll();
        if($localAdmins == null){
            return null;
        }

        foreach ($localAdmins as $localAdmin) {
            if ($localAdmin['organizationId'] == $organizationId){
                $response[] = $localAdmin;
            }
        }

        return $response;

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

    public function addFromExistingAccount($email, int $organizationId, ResponseInterface $response)
    {
        $response = $this->getInitedResponseWitStatus($organizationId, $email, $response);
        if ($response->getStatusCode() != 200){
            return $response;
        }

        $user = $this->userRepository->getByEmail($email);
        $roles = $this->roleRepository->getAll();
        $roleId = $this->getRoleIdWithName(AuthorizeMiddleware::LOCAL_ADMIN, $roles);

        if ($user == null) {
            $error = new ActionError(ActionError::BAD_REQUEST, 'такого пользователя не существует');
            $response->getBody()->write(json_encode(['errors' => array($error->jsonSerialize())]));
            return $response->withStatus(404);
        }else{
            if ($user->getRoleId() != $this->getRoleIdWithName(AuthorizeMiddleware::SIMPLE_USER, $roles)){
                $error = new ActionError(ActionError::BAD_REQUEST, 'этому пользователю уже присуще другая роль');
                $response->getBody()->write(json_encode(['errors' => array($error->jsonSerialize())]));
                return $response->withStatus(400);
            }

            $user->setRoleId($roleId);
            $this->userRepository->update($user);
        }


        return $this->localAdminRepository->add(new LocalAdmin($user, $organizationId, -1));
    }

   private function getInitedResponseWitStatus(int $organizationId, string $email, ResponseInterface $response)
   {
       if ($this->organizationRepository->get($organizationId) == null) {
           $error = new ActionError(ActionError::BAD_REQUEST, 'такой организации не существует');
           $response->getBody()->write(json_encode(['errors' => array($error->jsonSerialize())]));
           return $response->withStatus(400);
       }

       if ($this->localAdminRepository->localAdminIsSetOnDB($email, $organizationId)) {
           $error = new ActionError(ActionError::BAD_REQUEST, 'такой локальный администратор в данной организации уже существует');
           $response->getBody()->write(json_encode(['errors' => array($error->jsonSerialize())]));
           return $response->withStatus(400);
       }

       return $response;
   }
}