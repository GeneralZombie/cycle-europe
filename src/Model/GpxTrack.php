<?php

declare (strict_types=1);

namespace App\Model;

final class GpxTrack
{
    public string $file;

    public ?string $href;

    public ?string $title;

    public function __construct(string $file, ?string $href = null, ?string $title = null)
    {
        $this->file = $file;
        $this->href = $href;
        $this->title = $title;
    }

    public function getFile(): string
    {
        return $this->file;
    }

    public function setFile(string $file): void
    {
        $this->file = $file;
    }

    public function getHref(): ?string
    {
        return $this->href;
    }

    public function setHref(?string $href): void
    {
        $this->href = $href;
    }

    public function getTitle(): ?string
    {
        return $this->title;
    }

    public function setTitle(?string $title): void
    {
        $this->title = $title;
    }
}