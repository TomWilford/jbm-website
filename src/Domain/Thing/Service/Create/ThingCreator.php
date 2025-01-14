<?php

declare(strict_types=1);

namespace App\Domain\Thing\Service\Create;

use App\Domain\Thing\Enum\FaultLevel;
use App\Domain\Thing\Repository\ThingRepository;
use App\Domain\Thing\Thing;
use App\Infrastructure\Service\Creator\CreatorInterface;
use Doctrine\DBAL\Exception;

class ThingCreator implements CreatorInterface
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
     *      featured: bool,
     *      url: string,
     *      fault_level: string,
     *      active_from: string,
     *      active_to: string
     * } $data
     * @throws \Exception|Exception
     */
    public function createFromArray(array $data): object
    {
        $thing = new Thing(
            id: null,
            name: $data['name'],
            shortDescription: $data['short_description'],
            description: $data['description'],
            featured: (bool)$data['featured'],
            faultLevel: FaultLevel::from($data['fault_level']),
            activeFrom: (new \DateTimeImmutable($data['active_from']))->getTimestamp(),
            activeTo: $data['active_to'] ? (new \DateTimeImmutable($data['active_to']))->getTimestamp() : null,
            url: $data['url'] ?: null,
        );
        return $this->repository->store($thing);
    }
}
