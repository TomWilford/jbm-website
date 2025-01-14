<?php

declare(strict_types=1);

namespace App\Domain\Thing\Service\Update;

use App\Domain\Thing\Enum\FaultLevel;
use App\Domain\Thing\Repository\ThingRepository;
use App\Domain\Thing\Thing;
use App\Infrastructure\Enum\Unchanged;
use App\Infrastructure\Service\Updater\UpdaterInterface;
use Doctrine\DBAL\Exception;

class ThingUpdater implements UpdaterInterface
{
    public function __construct(protected ThingRepository $repository)
    {
        //
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
     *  } $data
     * @param Thing $entity
     * @return Thing
     * @throws Exception
     */
    public function updateFromArray(array $data, object $entity): object
    {
        $activeFrom = ($data['active_from'] === '')
            ? Unchanged::VALUE
            : (new \DateTimeImmutable($data['active_from']))->getTimestamp();

        $activeTo = $data['active_to'] !== 'null'
            ? ($data['active_to'] === ''
                ? Unchanged::VALUE
                : (new \DateTimeImmutable($data['active_to']))->getTimestamp())
            : null;

        $data['url'] = ($data['url'] === 'null') ? null : $data['url'];

        $thing = $entity->cloneWith(
            name: ($data['name'] === '') ? Unchanged::VALUE : $data['name'],
            shortDescription: ($data['short_description'] === '') ? Unchanged::VALUE : $data['short_description'],
            description: ($data['description'] === '') ? Unchanged::VALUE : $data['description'],
            featured: ($data['featured'] === '') ? Unchanged::VALUE : (bool)$data['featured'],
            faultLevel: ($data['fault_level'] === '') ? Unchanged::VALUE : FaultLevel::from($data['fault_level']),
            activeFrom: $activeFrom,
            activeTo: $activeTo,
            url: ($data['url'] === '') ? Unchanged::VALUE : $data['url']
        );

        return $this->repository->update($thing);
    }
}
