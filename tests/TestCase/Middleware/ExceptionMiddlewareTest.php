<?php

declare(strict_types=1);

namespace App\Test\TestCase\Middleware;

use App\Middleware\ExceptionMiddleware;
use App\Renderer\JsonRenderer;
use App\Renderer\TwigRenderer;
use App\Test\Traits\AppTestTrait;
use Nyholm\Psr7\Factory\Psr17Factory;
use PHPUnit\Framework\TestCase;
use Psr\Http\Message\ResponseFactoryInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Log\LoggerInterface;

class ExceptionMiddlewareTest extends TestCase
{
    use AppTestTrait;

    public function testRenderHtmlFallbacksToRawMessage(): void
    {
        $logger = $this->createMock(LoggerInterface::class);
        $responseFactory = $this->createMock(ResponseFactoryInterface::class);
        $jsonRenderer = $this->createMock(JsonRenderer::class);
        $twigRenderer = $this->createMock(TwigRenderer::class);
        $twigRenderer->method('twig')->willThrowException(new \Exception());

        $exception = new \RuntimeException('Test exception', 500);

        $middleware = new ExceptionMiddleware(
            $responseFactory,
            $jsonRenderer,
            $twigRenderer,
            $logger,
            false
        );

        $response = (new Psr17Factory())->createResponse();

        $result = $middleware->renderHtml($response, $exception);

        $this->assertResponseContains('Message: Test exception', $result);
    }
}
