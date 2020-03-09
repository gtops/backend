<?php

namespace App\Services\Event;
use App\Application\Actions\ActionError;
use App\Persistance\Repositories\Event\EventRepository;
use App\Persistance\Repositories\LocalAdmin\LocalAdminRepository;
use App\Domain\Models\Event\Event;
use Psr\Http\Message\ResponseInterface;

class EventService
{
    private $eventRepository;
    private $localAdminRepository;

    public function __construct(LocalAdminRepository $localAdminRepository, EventRepository $eventRepository)
    {
        $this->localAdminRepository = $localAdminRepository;
        $this->eventRepository = $eventRepository;
    }

    public function add(Event $event, string $userEmail, ResponseInterface $response)
    {
        if (!$this->localAdminRepository->localAdminIsSetOnDB($userEmail, $event->getIdOrganization())){
            return $response->withStatus(403);
        }

        return $this->eventRepository->add($event);
    }

    public function delete(int $organizationId, int $eventId, ResponseInterface $response, string $userEmail)
    {
        $response = $this->getInitedResponseWithStatusIfErrorOfAccess($response, $userEmail, $organizationId, $eventId);
        if ($response->getStatusCode() != 200){
            return $response;
        }

        $this->eventRepository->delete($eventId);
    }

    public function get(int $organizationId, int $eventId, ResponseInterface $response)
    {
        /**@var $event Event*/
        $event = $this->eventRepository->get($eventId);
        if ($event->getIdOrganization() != $organizationId){
            return $response->withStatus(400);
        }

        return $this->eventRepository->get($eventId);
    }

    private function getInitedResponseWithStatusIfErrorOfAccess(ResponseInterface $response, string $userEmail, int $organizationId, int $eventId)
    {
        if (!$this->localAdminRepository->localAdminIsSetOnDB($userEmail, $organizationId)){
            return $response->withStatus(403);
        }

        /**@var $event Event*/
        $event = $this->eventRepository->get($eventId);
        if ($event->getIdOrganization() != $organizationId){
            return $response->withStatus(403);
        }

        return $response->withStatus(200);
    }

    public function getAll(int $organizationId)
    {
        return $this->eventRepository->getAll();
    }

    public function update(Event $event, string $userEmail, ResponseInterface $response)
    {
        $response = $this->getInitedResponseWithStatusIfErrorOfAccess($response, $userEmail, $event->getIdOrganization(), $event->getId());
        if ($response->getStatusCode() != 200){
            return $response;
        }

        $this->eventRepository->update($event);

        return $response;
    }
}