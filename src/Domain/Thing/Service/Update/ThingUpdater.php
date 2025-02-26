<?php

declare(strict_types=1);

namespace App\Domain\Thing\Service\Update;

use App\Domain\Thing\Enum\FaultLevel;
use App\Domain\Thing\Repository\ThingRepository;
use App\Domain\Thing\Thing;
use App\Infrastructure\Enum\Unchanged;
use App\Infrastructure\Service\Updater\UpdaterInterface;
use DateTimeImmutable;
use Doctrine\DBAL\Exception;
use InvalidArgumentException;

readonly class ThingUpdater implements UpdaterInterface
{
    public function __construct(protected ThingRepository $repository)
    {
    }

    /**
     * @param array{
     *      name: string,
     *      short_description: string,
     *      description: string,
     *      featured: string,
     *      fault_level: string,
     *      active_from: string,
     *      active_to: string,
     *      url: string,
     *  }|array<string, mixed> $data
     * @param object $entity
     *
     * @throws Exception
     *
     * @return Thing
     */
    public function updateFromArray(array $data, object $entity): Thing
    {
        if (!$entity instanceof Thing) {
            throw new InvalidArgumentException('Entity must be instance of ' . Thing::class);
        }
        $thing = $entity->cloneWith(
            name: ($data['name'] === '') ? Unchanged::VALUE : $data['name'],
            shortDescription: ($data['short_description'] === '') ? Unchanged::VALUE : $data['short_description'],
            description: ($data['description'] === '') ? Unchanged::VALUE : $data['description'],
            featured: ($data['featured'] === '') ? Unchanged::VALUE : (bool)$data['featured'],
            faultLevel: ($data['fault_level'] === '') ? Unchanged::VALUE : FaultLevel::from($data['fault_level']),
            activeFrom: $this->resolveActiveFromValue($data['active_from']),
            activeTo: $this->resolveActiveToValue($data['active_to']),
            url: $this->resolveUrlValue($data['url'])
        );

        return $this->repository->update($thing);
    }

    private function resolveActiveFromValue(string $activeFrom): Unchanged|int
    {
        return ($activeFrom === '')
            ? Unchanged::VALUE
            : (new DateTimeImmutable($activeFrom))->getTimestamp();
    }

    private function resolveActiveToValue(string $activeTo): Unchanged|int|null
    {
        return $activeTo !== 'null'
            ? ($activeTo === '' ? Unchanged::VALUE : (new DateTimeImmutable($activeTo))->getTimestamp())
            : null;
    }

    private function resolveUrlValue(string $url): Unchanged|string|null
    {
        return ($url !== 'null')
            ? ($url === '' ? Unchanged::VALUE : $url)
            : null;
    }
}
