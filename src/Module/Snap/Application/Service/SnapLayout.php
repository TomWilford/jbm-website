<?php

declare(strict_types=1);

namespace App\Module\Snap\Application\Service;

use App\Module\Snap\Domain\Orientation;
use App\Module\Snap\Domain\Snap;

class SnapLayout
{
    /**
     * @param array<Snap> $snaps
     *
     * @return array<array{
     *      snaps: array<Snap>,
     *      mode: string
     *  }>
     */
    public function buildRows(array $snaps): array
    {
        $rows = [];
        $queue = $snaps;

        while (!empty($queue)) {
            $current = array_shift($queue);

            $count = $this->findMatchesAhead($queue, $current->getOrientation());

            if ($count >= 3) {
                // Can fill a row of 4 = but randomly choose 2 or 4
                $mode = [2, 4][array_rand([2, 4])];
            } elseif ($count >= 1) {
                // At least one match ahead = randomly go solo or pair up
                $mode = [1, 2][array_rand([1, 2])];
            } else {
                // No matches ahead = solo row
                $mode = 1;
            }

            $take = $mode - 1;
            $group = array_merge([$current], array_splice($queue, 0, $take));

            $layoutMode = match ($mode) {
                4 => 'four',
                2 => 'two',
                default => 'one',
            };

            $rows[] = $this->makeRow($group, $layoutMode);
        }

        return $rows;
    }

    /**
     * @param array<Snap> $queue
     * @param Orientation $orientation
     *
     * @return int
     */
    private function findMatchesAhead(array $queue, Orientation $orientation): int
    {
        $result = [];
        foreach ($queue as $snap) {
            if ($snap->getOrientation() === $orientation) {
                $result[] = $snap;
            } else {
                break;
            }
        }

        return count($result);
    }

    /**
     * @param array<Snap> $group
     * @param string $mode
     *
     * @return array{
     *     snaps: array<Snap>,
     *     mode: string
     * }
     */
    private function makeRow(array $group, string $mode): array
    {
        return [
            'snaps' => $group,
            'mode' => $mode,
        ];
    }
}
