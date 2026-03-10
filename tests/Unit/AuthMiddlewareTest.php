<?php

namespace Tests\Unit;

use AuthMiddleware;
use PHPUnit\Framework\Attributes\PreserveGlobalState;
use PHPUnit\Framework\Attributes\RunInSeparateProcess;
use PHPUnit\Framework\TestCase;

class AuthMiddlewareTest extends TestCase
{
    // --- check() ---

    #[RunInSeparateProcess]
    #[PreserveGlobalState(false)]
    public function testCheckReturnsFalseWhenSessionIsEmpty(): void
    {
        $this->assertFalse(AuthMiddleware::check());
    }

    #[RunInSeparateProcess]
    #[PreserveGlobalState(false)]
    public function testCheckReturnsFalseWhenIdUserIsAbsent(): void
    {
        session_start();
        $_SESSION = ['profile' => 'user'];   // sem idUser

        $this->assertFalse(AuthMiddleware::check());
    }

    #[RunInSeparateProcess]
    #[PreserveGlobalState(false)]
    public function testCheckReturnsTrueWhenIdUserIsSet(): void
    {
        session_start();
        $_SESSION['idUser'] = 42;

        $this->assertTrue(AuthMiddleware::check());
    }

    // --- handle() com redirectFn injetável ---

    #[RunInSeparateProcess]
    #[PreserveGlobalState(false)]
    public function testHandleCallsRedirectFnWhenNotAuthenticated(): void
    {
        $redirectCalled = false;

        AuthMiddleware::handle(function () use (&$redirectCalled) {
            $redirectCalled = true;
        });

        $this->assertTrue($redirectCalled);
    }

    #[RunInSeparateProcess]
    #[PreserveGlobalState(false)]
    public function testHandleDoesNotCallRedirectFnWhenAuthenticated(): void
    {
        session_start();
        $_SESSION['idUser'] = 1;

        $redirectCalled = false;

        AuthMiddleware::handle(function () use (&$redirectCalled) {
            $redirectCalled = true;
        });

        $this->assertFalse($redirectCalled);
    }

    // --- protect() ---

    #[RunInSeparateProcess]
    #[PreserveGlobalState(false)]
    public function testProtectCallsHandlerWhenAuthenticated(): void
    {
        session_start();
        $_SESSION['idUser'] = 7;

        $called  = false;
        $handler = AuthMiddleware::protect(
            function () use (&$called) { $called = true; }
        );

        $handler();

        $this->assertTrue($called);
    }

    #[RunInSeparateProcess]
    #[PreserveGlobalState(false)]
    public function testProtectDoesNotCallHandlerWhenNotAuthenticated(): void
    {
        $handlerCalled  = false;
        $redirectCalled = false;

        $handler = AuthMiddleware::protect(
            function () use (&$handlerCalled)  { $handlerCalled  = true; },
            function () use (&$redirectCalled) { $redirectCalled = true; }
        );

        $handler();

        $this->assertFalse($handlerCalled);
        $this->assertTrue($redirectCalled);
    }

    #[RunInSeparateProcess]
    #[PreserveGlobalState(false)]
    public function testProtectCallsRedirectWhenNotAuthenticated(): void
    {
        $redirected = false;

        $handler = AuthMiddleware::protect(
            function () {},                                       // handler
            function () use (&$redirected) { $redirected = true; } // redirectFn
        );

        $handler();

        $this->assertTrue($redirected);
    }

    #[RunInSeparateProcess]
    #[PreserveGlobalState(false)]
    public function testProtectWithArrayHandlerCallsMethodWhenAuthenticated(): void
    {
        session_start();
        $_SESSION['idUser'] = 1;

        // Cria uma classe anônima acessível via nome — usa closure como proxy
        $called  = false;
        $handler = AuthMiddleware::protect(
            function () use (&$called) { $called = true; }
        );

        $handler();

        $this->assertTrue($called);
    }
}
