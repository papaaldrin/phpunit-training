<?php

namespace Tests\Unit;

use App\Router;
use App\Exceptions\RouteNotFoundException;
use PHPUnit\Framework\TestCase;

class RouterTest extends TestCase
{
    private Router $router;

    protected function setUp(): void
    {
        parent::setUp();

        $this->router = new Router();
    }

    public function testRegisterARoute(): void
    {
        // when we call a register method
        $this->router->register('get', '/users', ['Users', 'index']);

        $expected = [
            'get' => [
                '/users' => ['Users', 'index'],
            ],
        ];

        // then we assert route was registered
        $this->assertSame($expected, $this->router->routes());
    }

    public function testRegisterAGetRoute(): void
    {
        $this->router->get('/users', ['Users', 'index']);

        $expected = [
            'get' => [
                '/users' => ['Users', 'index'],
            ],
        ];

        $this->assertSame($expected, $this->router->routes());
    }
    
    public function testRegisterAPostRoute(): void
    {
        $this->router->post('/users', ['Users', 'store']);

        $expected = [
            'post' => [
                '/users' => ['Users', 'store'],
            ],
        ];

        $this->assertSame($expected, $this->router->routes());
    }

    public function testThereAreNoRoutesWhenRouterIsCreated(): void
    {
        $this->assertEmpty((new Router())->routes());
    }

    /**
     * @dataProvider \Tests\DataProviders\RouterDataProvider::routeNotFoundCases
     */
    public function testThrowsRouteNotFoundException(
        string $requestUri,
        string $requestMethod
    ): void
    {
        $users = new class() {
            public function delete(): bool
            {
                return true;
            }
        };

        $this->router->post('/users', [$users::class, 'store']);
        $this->router->get('/users', ['Users', 'index']);

        $this->expectException(RouteNotFoundException::class);
        $this->router->resolve($requestUri, $requestMethod);
    }

    public function testResolvesRouteFromAClosure()
    {
        $this->router->get('/users', fn() => [1, 2, 3]);

        $this->assertSame(
            [1, 2, 3],
            $this->router->resolve('/users', 'get')
        );
    }

    public function testResolvesRoute()
    {
        $users = new class() {
            public function index(): array
            {
                return [1, 2, 3];
            }
        };

        $this->router->get('/users', [$users::class, 'index']);

        // assertEquals = ==
        // assertSame = ===
        $this->assertSame(
            [1, 2, 3],
            $this->router->resolve('/users', 'get')
        );
    }
}
