<?php

declare(strict_types=1);

namespace App\Dto;

/**
 * Data Transfer Object for project information.
 */
readonly class ProjectDto
{
    public function __construct(
        public string $title,
        public string $description,
        public string $technologies,
        public string $image,
        public string $code,
        public string $link,
    ) {
    }

    /**
     * Create a DTO from an array representation of a project.
     *
     * @param array{title: string, description: string, technologies: string, image: string, code: string, link: string} $data
     * @return self
     */
    public static function fromArray(array $data): self
    {
        return new self(
            $data['title'],
            $data['description'],
            $data['technologies'],
            $data['image'],
            $data['code'],
            $data['link'],
        );
    }

    /**
     * Convert the DTO to an array representation.
     *
     * @return array{title: string, description: string, technologies: string, image: string, code: string, link: string}
     */
    public function toArray(): array
    {
        return [
            'title' => $this->title,
            'description' => $this->description,
            'technologies' => $this->technologies,
            'image' => $this->image,
            'code' => $this->code,
            'link' => $this->link,
        ];
    }
}
