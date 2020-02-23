<?php

namespace App\Application\Actions\Organization;

use App\Services\Presenters\OrganizationsToResponsePresenter;
use Psr\Http\Message\RequestInterface as Request;
use Psr\Http\Message\ResponseInterface as Response;
use App\Domain\Models\OrganizationCreater;
use App\Services\Organization\OrganiztionService;

class OrganizationAction extends \App\Application\Actions\Action
{
    private $organizationService;

    public function __construct(OrganiztionService $organizationService)
    {
        $this->organizationService = $organizationService;
    }

    public function add(Request $request, Response $response, $args): Response
    {
        $rawParams = json_decode($request->getBody()->getContents(), true);
        $rawParams['id'] = -1;
        /**TODO сделать валидацию объекта организация*/
        $this->organizationService->addOrganization(OrganizationCreater::createModel($rawParams));
        return $response;
    }

    public function get(Request $request, Response $response, $args): Response
    {
        $id = $args['id'];
        $organization = $this->organizationService->getOrganization($id);
        if ($organization == null){
            return $this->respond(404, [], $response);
        }

        return $this->respond(200, $organization->toArray(), $response);
    }

    public function getAll(Request $request, Response $response, $args): Response
    {
        $organizations = $this->organizationService->getOrganizations();
        if ($organizations == null){
            $this->respond(404, [], $response);
        }

        return $this->respond(200, OrganizationsToResponsePresenter::getView($organizations), $response);
    }

    public function delete(Request $request, Response $response, $args): Response
    {
        $id = $args['id'];
        $this->organizationService->deleteOrganization($id);
        return $response;
    }

    public function update(Request $request, Response $response, $args): Response
    {

    }
}