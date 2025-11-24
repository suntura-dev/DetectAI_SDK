<?php

namespace DetectAI\Detectors;

use OpenAI;
use Exception;
use DetectAI\AbstractDetector;
use DetectAI\DataTransferObjects\DetectionResultDTO;
use DetectAI\Enums\DetectionType;

class OpenAIDetector extends AbstractDetector {    
    public function detect(): DetectionResultDTO
    {
        $file = $this->getFile();

        $mimeType = $file->getMimeType();

        if (!in_array($mimeType, ['image/jpeg', 'image/png', 'image/webp'])) {
            throw new Exception('Invalid File Format provided');
        }

        $base64 = base64_encode(file_get_contents($file->getRealPath()));

        $response = OpenAI::responses()->create([
            'model' => 'gpt-4.1',
            'input' => [
                [
                    'role' => 'user',
                    'content' => [
                        [
                            'type' => 'input_text',
                            'text' => "what's in this image?"
                        ],
                        [
                            'type' => 'input_image',
                            'image_url' => "data:{$mimeType};base64,{$base64}"
                        ]
                    ]
                ]
            ],
            'response_format' => [
                'type' => 'json_schema',
                'json_schema' => $this->getResponseSchema(),
            ]
        ]);

        $responseObj = json_decode($response->outputText);

        return (new DetectionResultDTO(
            $responseObj->score,
            DetectionType::OPENAI_API,
            $responseObj->score === 0.0 ? 'Image has not been tempered with' : 'Image may have been tempered with',
        ));
    }

    private function getResponseSchema(): array
    {
        return [
            'name' => 'detection_result',
            'schema' => [
                'type' => 'object',
                'properties' => [
                    'score' => [
                        'type' => 'number',
                        'description' => 'Confidence score of the detection',
                        'minimum' => 0.0,
                        'maximum' => 1.0,
                        'multipleOf' => 0.1
                    ],
                    'reason' => [
                        'type' => 'string',
                        'description' => 'Explanation for the detection result'
                    ],
                ],
                'required' => ['score', 'detection_type', 'reason']
            ]
        ];
    }
}