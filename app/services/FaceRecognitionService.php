<?php

declare(strict_types=1);

namespace App\Services;

class FaceRecognitionService
{
    /**
     * Calculates the Euclidean distance between two 128-dimensional face descriptor vectors.
     *
     * @param array<float> $descriptorA
     * @param array<float> $descriptorB
     * @return float
     */
    public function computeEuclideanDistance(array $descriptorA, array $descriptorB): float
    {
        if (count($descriptorA) !== 128 || count($descriptorB) !== 128) {
            throw new \InvalidArgumentException('Both descriptor vectors must contain exactly 128 values.');
        }

        $sumOfSquares = 0.0;
        for ($i = 0; $i < 128; $i++) {
            $diff = (float) $descriptorA[$i] - (float) $descriptorB[$i];
            $sumOfSquares += $diff * $diff;
        }

        return sqrt($sumOfSquares);
    }

    /**
     * Verifies live face descriptor against stored baseline descriptor.
     *
     * @param array<float> $liveDescriptor
     * @param array<float> $storedDescriptor
     * @param float $threshold (e.g. 0.60)
     * @return array{isMatch: bool, distance: float, threshold: float}
     */
    public function verifyFace(array $liveDescriptor, array $storedDescriptor, float $threshold = 0.60): array
    {
        $distance = $this->computeEuclideanDistance($liveDescriptor, $storedDescriptor);
        $isMatch = ($distance <= $threshold);

        return [
            'isMatch' => $isMatch,
            'distance' => round($distance, 4),
            'threshold' => $threshold
        ];
    }
}
