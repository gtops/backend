<?php

use App\Persistance\Repositories\User\UserRepository;
use DI\ContainerBuilder;

class UserRepositoryTest extends \Codeception\Test\Unit
{
    /**
     * @var \UnitTester
     */
    protected $tester;
    private $userRepository;

    protected function _before()
    {
        $containerBuilder = new ContainerBuilder();
        $settings = require __DIR__ . '/../../../app/settings.php';
        $settings($containerBuilder);

        $dependencies = require __DIR__ . '/../../../app/dependencies.php';
        $dependencies($containerBuilder);

        $container = $containerBuilder->build();
        $this->userRepository = $container->get(UserRepository::class);
    }

    protected function _after()
    {
    }

    // tests
    public function testSomeFeature()
    {
        /**@var $user \App\Domain\Models\User\User*/
        $user = $this->userRepository->get(6);
        $this->tester->assertEquals('23', $user->getName());
    }
}