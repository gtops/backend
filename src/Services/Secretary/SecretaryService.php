<?php

namespace App\Services\Secretary;

use App\Application\Actions\ActionError;
use App\Application\Middleware\AuthorizeMiddleware;
use App\Domain\Models\Event\Event;
use App\Domain\Models\Secretary\Secretary;
use App\Persistance\Repositories\Event\EventRepository;
use App\Persistance\Repositories\LocalAdmin\LocalAdminRepository;
use App\Persistance\Repositories\Organization\OrganizationRepository;
use App\Persistance\Repositories\Role\RoleRepository;
use App\Persistance\Repositories\Secretary\SecretaryRepository;
use App\Persistance\Repositories\User\UserRepository;
use Psr\Http\Message\ResponseInterface;

class SecretaryService
{
    private $secretaryRepository;
    private $userRepository;
    private $organizationRepository;
    private $localAdminRepository;
    private $eventRepository;
    private $roleRepository;


    public function __construct(
        SecretaryRepository $secretaryRepository,
        UserRepository $userRepository,
        OrganizationRepository $organizationRepository,
        LocalAdminRepository $localAdminRepository,
        EventRepository $eventRepository,
        RoleRepository $roleRepository
    )
    {
        $this->secretaryRepository = $secretaryRepository;
        $this->userRepository = $userRepository;
        $this->organizationRepository = $organizationRepository;
        $this->localAdminRepository = $localAdminRepository;
        $this->eventRepository = $eventRepository;
        $this->roleRepository = $roleRepository;
    }

    public function addFromExistingAccount($localAdminEmail,  $secretaryEmail, int $organizationId, int $eventId, ResponseInterface $response)
    {
        $response = $this->getInitedResponseWitStatus($organizationId, $localAdminEmail, $secretaryEmail, $eventId, $response);
        if ($response->getStatusCode() != 200){
            return $response;
        }

        $user = $this->userRepository->getByEmail($secretaryEmail);
        $roles = $this->roleRepository->getAll();
        $roleId = $this->getRoleIdWithName(AuthorizeMiddleware::SECRETARY, $roles);

        if ($user == null) {
            $response->getBody()->write(json_encode(['errors' => array(new ActionError(ActionError::BAD_REQUEST, 'такого пользователя не существует'))]));
            return $response->withStatus(404);
        }else{
            if ($user->getRoleId() != $this->getRoleIdWithName(AuthorizeMiddleware::SIMPLE_USER, $roles)){
                $response->getBody()->write(json_encode(['errors' => array(new ActionError(ActionError::BAD_REQUEST, 'этому пользователю уже присуще другая роль'))]));
                return $response->withStatus(400);
            }

            $user->setRoleId($roleId);
            $this->userRepository->update($user);
        }

        $this->secretaryRepository->add(new Secretary(-1 , $eventId, $organizationId, $user));
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

    private function getInitedResponseWitStatus(int $organizationId, string $localAdminEmail, string $secretaryEmail, int $eventId, ResponseInterface $response)
    {
        if (!$this->localAdminRepository->localAdminIsSetOnDB($localAdminEmail, $organizationId)) {
            $response->getBody()->write(json_encode(['errors' => array(new ActionError(ActionError::BAD_REQUEST, 'данный локальный администратор не может редактировать мероприятия этой организации'))]));
            return $response->withStatus(400);
        }

        if ($this->organizationRepository->get($organizationId) == null) {
            $response->getBody()->write(json_encode(['errors' => array(new ActionError(ActionError::BAD_REQUEST, 'такой организации не существует'))]));
            return $response->withStatus(400);
        }

        /**@var $event Event*/
        $event = $this->eventRepository->get($eventId);
        if ($event == null){
            $response->getBody()->write(json_encode(['errors' => array(new ActionError(ActionError::BAD_REQUEST, 'данного мероприятия не существует'))]));
            return $response->withStatus(400);
        }

        if ($event->getIdOrganization() != $organizationId){
            $response->getBody()->write(json_encode(['errors' => array(new ActionError(ActionError::BAD_REQUEST, 'мероприятие не относится к данной организации'))]));
            return $response->withStatus(400);
        }

        return $response;
    }
}