<?php

namespace App\Application\Actions\Team;
use App\Application\Actions\Action;
use App\Services\Team\TeamService;
use Psr\Http\Message\RequestInterface as Request;
use Psr\Http\Message\ResponseInterface as Response;

class TeamAction extends Action
{
    private $temaService;
    public function __construct(TeamService $teamService)
    {
        $this->temaService = $teamService;
    }

    public function add(Request $request, Response $response, $args): Response
    {

    }

    public function get(Request $request, Response $response, $args): Response
    {

    }

    public function getAll(Request $request, Response $response, $args): Response
    {

    }

    public function delete(Request $request, Response $response, $args): Response
    {

    }

    public function update(Request $request, Response $response, $args): Response
    {

    }
}