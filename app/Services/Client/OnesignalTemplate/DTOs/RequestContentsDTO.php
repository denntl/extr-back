<?php

namespace App\Services\Client\OnesignalTemplate\DTOs;

class RequestContentsDTO
{
    public function __construct(
        public int $geo,
        public string $title,
        public string $text,
        public string $image,
    ) {
    }

    /**
     * @param array<RequestContentsDTO> $array
     * @return array
     */
    public static function fromArray(array $array): array
    {
        $output = [];
        foreach ($array as $value) {
            $output[] = new self(
                $value['geo'],
                $value['title'],
                $value['text'],
                $value['image'],
            );
        }
        return $output;
    }
}
