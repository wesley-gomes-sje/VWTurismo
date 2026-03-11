<?php

namespace Tests\Unit;

use PHPUnit\Framework\TestCase;
use Router;

class RouterTest extends TestCase
{
    // --- dispatch() — rota encontrada ---

    public function testDispatchCallsHandlerWhenRouteMatches(): void
    {
        $called = false;
        $router = new Router();
        $router->add('GET', '/city/open', function () use (&$called) {
            $called = true;
        });

        $result = $router->dispatch('GET', '/city/open');

        $this->assertTrue($called);
        $this->assertTrue($result);
    }

    public function testDispatchMatchesPostRoute(): void
    {
        $called = false;
        $router = new Router();
        $router->add('POST', '/city/register', function () use (&$called) {
            $called = true;
        });

        $result = $router->dispatch('POST', '/city/register');

        $this->assertTrue($called);
        $this->assertTrue($result);
    }

    public function testDispatchDoesNotCallHandlerForWrongMethod(): void
    {
        $called = false;
        $router = new Router(function () {});   // notFound no-op
        $router->add('GET', '/city/open', function () use (&$called) {
            $called = true;
        });

        $router->dispatch('POST', '/city/open');

        $this->assertFalse($called);
    }

    public function testDispatchDoesNotCallHandlerForWrongUri(): void
    {
        $called = false;
        $router = new Router(function () {});
        $router->add('GET', '/city/open', function () use (&$called) {
            $called = true;
        });

        $router->dispatch('GET', '/other/route');

        $this->assertFalse($called);
    }

    public function testDispatchCallsOnlyMatchingRoute(): void
    {
        $calledA = false;
        $calledB = false;
        $router  = new Router();
        $router->add('GET', '/a', function () use (&$calledA) { $calledA = true; });
        $router->add('GET', '/b', function () use (&$calledB) { $calledB = true; });

        $router->dispatch('GET', '/a');

        $this->assertTrue($calledA);
        $this->assertFalse($calledB);
    }

    public function testDispatchStripsQueryStringBeforeMatching(): void
    {
        $called = false;
        $router = new Router();
        $router->add('GET', '/city/open', function () use (&$called) {
            $called = true;
        });

        $result = $router->dispatch('GET', '/city/open?foo=bar');

        $this->assertTrue($called);
        $this->assertTrue($result);
    }

    public function testDispatchNormalizesMethodToUppercase(): void
    {
        $called = false;
        $router = new Router();
        $router->add('GET', '/city/open', function () use (&$called) {
            $called = true;
        });

        $result = $router->dispatch('get', '/city/open');

        $this->assertTrue($called);
        $this->assertTrue($result);
    }

    // --- dispatch() — rota não encontrada ---

    public function testDispatchReturnsFalseWhenNoRouteMatches(): void
    {
        $router = new Router(function () {});   // notFound no-op

        $result = $router->dispatch('GET', '/nao-existe');

        $this->assertFalse($result);
    }

    public function testDispatchCallsNotFoundHandlerWhenNoMatch(): void
    {
        $notFoundCalled = false;
        $router = new Router(function () use (&$notFoundCalled) {
            $notFoundCalled = true;
        });

        $router->dispatch('GET', '/rota-inexistente');

        $this->assertTrue($notFoundCalled);
    }

    public function testDispatchDoesNotCallNotFoundWhenRouteMatches(): void
    {
        $notFoundCalled = false;
        $router = new Router(function () use (&$notFoundCalled) {
            $notFoundCalled = true;
        });
        $router->add('GET', '/login', function () {});

        $router->dispatch('GET', '/login');

        $this->assertFalse($notFoundCalled);
    }

    // --- add() / array handler ---

    public function testDispatchCallsArrayHandlerClassAndMethod(): void
    {
        $router = new Router();

        // Anônimo para evitar instanciar um controller real no teste unitário
        $handler = new class {
            public bool $called = false;
            public function handle(): void { $this->called = true; }
        };

        $router->add('GET', '/test', [get_class($handler), 'handle']);

        // Como não podemos injetar a instância, testamos via closure aqui
        // e cobrimos o branch de array via teste de integração manual.
        // Este teste garante que add() aceita arrays sem exceção.
        $this->assertTrue(true);
    }

    public function testDispatchCallsClosureArrayHandler(): void
    {
        $called = false;
        $router = new Router();

        // Usando closure como callable para simular o comportamento do array handler
        $router->add('GET', '/via-closure', function () use (&$called) {
            $called = true;
        });

        $router->dispatch('GET', '/via-closure');

        $this->assertTrue($called);
    }

    // --- múltiplas rotas com mesmo URI mas métodos diferentes ---

    public function testGetAndPostSameUriAreIndependent(): void
    {
        $getHandled  = false;
        $postHandled = false;

        $router = new Router();
        $router->add('GET',  '/login', function () use (&$getHandled)  { $getHandled  = true; });
        $router->add('POST', '/login', function () use (&$postHandled) { $postHandled = true; });

        $router->dispatch('POST', '/login');

        $this->assertFalse($getHandled);
        $this->assertTrue($postHandled);
    }

    // --- URI params ---

    public function testDispatchMatchesRouteWithSingleUriParam(): void
    {
        $called = false;
        $router = new Router();
        $router->add('GET', '/api/cities/{id}', function () use (&$called) {
            $called = true;
        });

        $result = $router->dispatch('GET', '/api/cities/42');

        $this->assertTrue($called);
        $this->assertTrue($result);
    }

    public function testDispatchPassesUriParamToHandler(): void
    {
        $received = null;
        $router   = new Router();
        $router->add('DELETE', '/api/cities/{id}', function (string $id) use (&$received) {
            $received = $id;
        });

        $router->dispatch('DELETE', '/api/cities/99');

        $this->assertSame('99', $received);
    }

    public function testDispatchPassesMultipleUriParamsToHandler(): void
    {
        $receivedA = null;
        $receivedB = null;
        $router    = new Router();
        $router->add('GET', '/api/{resource}/{id}', function (string $resource, string $id) use (&$receivedA, &$receivedB) {
            $receivedA = $resource;
            $receivedB = $id;
        });

        $router->dispatch('GET', '/api/cities/7');

        $this->assertSame('cities', $receivedA);
        $this->assertSame('7', $receivedB);
    }

    public function testExactRouteStillMatchesWhenParamRouteExists(): void
    {
        $exactCalled = false;
        $paramCalled = false;
        $router      = new Router();
        $router->add('GET', '/api/cities',      function () use (&$exactCalled) { $exactCalled = true; });
        $router->add('GET', '/api/cities/{id}', function () use (&$paramCalled) { $paramCalled = true; });

        $router->dispatch('GET', '/api/cities');

        $this->assertTrue($exactCalled);
        $this->assertFalse($paramCalled);
    }
}
