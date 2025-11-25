<?php

namespace DetectAI\Detectors;

use OpenAI;
use Exception;
use DetectAI\AbstractDetector;
use DetectAI\DataTransferObjects\DetectionResultDTO;
use DetectAI\Enums\DetectionType;
use Illuminate\Http\File;

class OpenAIDetector extends AbstractDetector {    
    public function detect(): DetectionResultDTO
    {
        $suspectFileMimetype = $this->getFile()->getMimeType();

        if (!in_array($suspectFileMimetype, ['image/jpeg', 'image/png', 'image/webp'])) {
            throw new Exception('Invalid Suspect File Format provided');
        }

        $originalFileMimetype = $this->getOriginalFile()->getMimeType();

        if (!in_array($originalFileMimetype, ['image/jpeg', 'image/png', 'image/webp'])) {
            throw new Exception('Invalid Original File Format provided');
        }

        $response = OpenAI::responses()->create([
            'model' => 'gpt-4.1',
            'input' => [
                [
                    'role' => 'user',
                    'content' => [
                        [
                            'type' => 'input_text',
                            'text' => "{$this->getPrompt()}"
                        ],
                        [
                            'type' => 'input_image',
                            'image_url' => "data:{$originalFileMimetype};base64,{$this->getFileContents($this->getOriginalFile())}",
                            'name' => 'image_original', 
                        ],
                        [
                            'type' => 'input_image',
                            'image_url' => "data:{$suspectFileMimetype};base64,{$this->getFileContents($this->getFile())}",
                            'name' => 'image_suspect', 
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
            $responseObj->reason,
        ));
    }

    private function getPrompt(): string
    {
        $basePath = base_path('prompts/OpenAIDetector.txt');
        $prompt = file_get_contents($basePath);

        if (!$prompt || empty($prompt)) {
            throw new Exception(sprintf("Could not extract prompt from: %s", $basePath));
        }

        return $prompt;
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

    private function getFileContents(File $file): string
    {
        return base64_encode(file_get_contents($file->getRealPath()));
    }
}