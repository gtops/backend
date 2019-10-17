<?php
/**
 * Created by PhpStorm.
 * User: Admin
 * Date: 17.10.2019
 * Time: 9:48
 */

namespace App\Swagger;

use Monolog\Logger;

class SwaggerWatcher
{

    /**
     * @SWG\Swagger(
     * schemes={"http"},
     * host="petrodim.beget.techs",
     * basePath="/",
     * @SWG\Info(
     * title="GTO",
     * version="1.0.0"
     * )
     * )
     */

    /**
     * @SWG\Get(
     *     path="/api/resource.json",
     *     @SWG \Response(response="200", description="An example resource")
     * )
     */

    private $pathToProject;

    public function __construct($pathToProject)
    {
        $this->pathToProject = $pathToProject;
    }

    public function getDocumentation():string
    {
        $logger = new Logger('a');
        $logger->alert(__DIR__);
        $swagger = \Swagger\scan($this->pathToProject . '/src');
        file_put_contents($this->pathToProject . 'public/api.json', $swagger);
        $html = file_get_contents(__DIR__ . '/index.html');
        return $html;

    }
}