<?php

declare(strict_types=1);

namespace Tests;

use App\Services\FaceRecognitionService;

class FaceRecognitionServiceTest
{
    public function testIdenticalVectorsDistanceZero(): void
    {
        $service = new FaceRecognitionService();
        $vec = array_fill(0, 128, 0.5);
        $dist = $service->computeEuclideanDistance($vec, $vec);
        self::expect(abs($dist) < 0.00001, "Expected distance 0.0 for identical vectors");
    }

    public function testVerifyFaceMatch(): void
    {
        $service = new FaceRecognitionService();
        $vecA = array_fill(0, 128, 0.1);
        $vecB = array_fill(0, 128, 0.12); // small difference

        $res = $service->verifyFace($vecA, $vecB, 0.60);
        self::expect($res['isMatch'] === true, "Expected match to be true for close vectors");
    }

    public function testVerifyFaceMismatch(): void
    {
        $service = new FaceRecognitionService();
        $vecA = array_fill(0, 128, 0.1);
        $vecB = array_fill(0, 128, 0.9); // large difference

        $res = $service->verifyFace($vecA, $vecB, 0.60);
        self::expect($res['isMatch'] === false, "Expected match to be false for distant vectors");
    }

    private static function expect(bool $condition, string $message): void
    {
        if (!$condition) {
            throw new \RuntimeException($message);
        }
    }
}
