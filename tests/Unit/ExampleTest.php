<?php

namespace Tests\Unit;

use PHPUnit\Framework\TestCase;
use DetectAI\Detectors\ExifDetector;
use DetectAI\DataTransferObjects\DetectionResultDTO;
use DetectAI\Enums\DetectionType;

class ExampleTest extends TestCase
{
    /**
     * Test ExifDetector detect() method with an AI-altered image.
     */
    public function test_exif_detector_detect_with_ai_altered_image(): void
    {
        $imagePath = __DIR__ . '/../Mocks/Images/Image Altered By AI.jpeg';
        
        // Ensure the image file exists
        $this->assertFileExists($imagePath, 'Test image file should exist');
        
        // Create ExifDetector instance
        $detector = new ExifDetector($imagePath);
        
        // Call detect() method
        $result = $detector->detect();
        
        // Assert the result is a DetectionResultDTO instance
        $this->assertInstanceOf(DetectionResultDTO::class, $result);
        
        // Assert the detection type is EXIF_ANALYSIS
        $this->assertEquals(DetectionType::EXIF_ANALYSIS, $result->getDetectionType());
        
        // Assert the score is a float and within expected range (0.0 to 1.0)
        $score = $result->getScore();
        $this->assertIsFloat($score);
        $this->assertGreaterThanOrEqual(0.0, $score);
        $this->assertLessThanOrEqual(1.0, $score);
        
        // Assert the reason is a non-empty string
        $reason = $result->getReason();
        $this->assertIsString($reason);
        $this->assertNotEmpty($reason);
        
        // Assert comparison images is an array
        $this->assertIsArray($result->getComparisonImages());
        
        // Assert metadata is an array
        $this->assertIsArray($result->getMetadata());
    }
}
