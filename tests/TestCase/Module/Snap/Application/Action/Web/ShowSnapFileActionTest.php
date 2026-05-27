<?php

declare(strict_types=1);

namespace App\Test\TestCase\Module\Snap\Application\Action\Web;

use App\Module\Snap\Application\Action\Web\ShowSnapFileAction;
use App\Module\Snap\Domain\Snap;
use App\Module\Snap\Infrastructure\SnapRepository;
use App\Test\Traits\AppTestTrait;
use App\Test\Traits\DatabaseTestTrait;
use Doctrine\DBAL\Connection;
use Fig\Http\Message\StatusCodeInterface;
use PhpCommonEnums\MimeType\Enumeration\MimeTypeEnum;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;
use Sqids\Sqids;

#[CoversClass(ShowSnapFileAction::class)]
class ShowSnapFileActionTest extends TestCase
{
    use AppTestTrait;
    use DatabaseTestTrait;

    private string $fixtureBinary;

    protected function setUp(): void
    {
        $this->setUpApp();

        $filePath = dirname(__DIR__, 5) . '/Fixtures/assets/snap-01.webp';
        $this->fixtureBinary = file_get_contents($filePath) ?: '';
    }

    public function testActionReturnsRawImageOnValidRequest(): void
    {
        $repository = new SnapRepository($this->container?->get(Connection::class));

        $snap = new Snap(
            id: null,
            albumId: 1,
            image: $this->fixtureBinary,
            mimeType: MimeTypeEnum::ImageWebp
        );
        $savedSnap = $repository->store($snap);

        $filename = sprintf('%s.webp', $savedSnap->getSqid());

        $request = $this->createRequest('GET', sprintf('/snaps/%s', $filename));
        $response = $this->app->handle($request);

        $this->assertSame(StatusCodeInterface::STATUS_OK, $response->getStatusCode());
        $this->assertSame('image/webp', $response->getHeaderLine('Content-Type'));
        $this->assertSame('public, max-age=31536000', $response->getHeaderLine('Cache-Control'));
        $this->assertSame($this->fixtureBinary, (string)$response->getBody());
    }

    public function testActionThrows404WhenExtensionDoesNotMatchMimeType(): void
    {
        $repository = new SnapRepository($this->container?->get(Connection::class));

        $snap = new Snap(
            id: null,
            albumId: 1,
            image: $this->fixtureBinary,
            mimeType: MimeTypeEnum::ImageWebp
        );
        $savedSnap = $repository->store($snap);

        $invalidFilename = sprintf('%s.jpg', $savedSnap->getSqid());

        $request = $this->createRequest('GET', sprintf('/snaps/%s', $invalidFilename));
        $response = $this->app->handle($request);

        $this->assertSame(StatusCodeInterface::STATUS_NOT_FOUND, $response->getStatusCode());
    }

    public function testActionThrows404WhenCustomExtensionProvided(): void
    {
        $repository = new SnapRepository($this->container?->get(Connection::class));

        $snap = new Snap(
            id: null,
            albumId: 1,
            image: $this->fixtureBinary,
            mimeType: MimeTypeEnum::ImageWebp
        );
        $savedSnap = $repository->store($snap);

        $soupFilename = sprintf('%s.soup', $savedSnap->getSqid());

        $request = $this->createRequest('GET', sprintf('/snaps/%s', $soupFilename));
        $response = $this->app->handle($request);

        $this->assertSame(StatusCodeInterface::STATUS_NOT_FOUND, $response->getStatusCode());
    }

    public function testActionThrows404WhenSqidIsInvalid(): void
    {
        $request = $this->createRequest('GET', '/snaps/completely-invalid-sqid-string.webp');
        $response = $this->app->handle($request);

        $this->assertSame(StatusCodeInterface::STATUS_NOT_FOUND, $response->getStatusCode());
    }

    public function testActionThrows404WhenRecordDoesNotExist(): void
    {
        $sqids = $this->container?->get(Sqids::class);

        $nonExistentSqid = $sqids->encode([99999]);
        $filename = sprintf('%s.webp', $nonExistentSqid);

        $request = $this->createRequest('GET', sprintf('/snaps/%s', $filename));
        $response = $this->app->handle($request);

        $this->assertSame(StatusCodeInterface::STATUS_NOT_FOUND, $response->getStatusCode());
    }
}
