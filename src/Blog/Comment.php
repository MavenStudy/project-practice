<?php
namespace Maven\ProjectPractice\Blog;

class Comment{

    public function __construct(UUID $uuid, UUID $postUuid, UUID $authorUuid, string $text)
    {
        $this->uuid = $uuid;
        $this->postUuid = $postUuid;
        $this->authorUuid = $authorUuid;
        $this->text = $text;
    }

    public function getUuid(): UUID
    {
        return $this->uuid;
    }

    public function getPostUuid(): UUID
    {
        return $this->postUuid;
    }

    public function getAuthorUuid(): UUID
    {
        return $this->authorUuid;
    }

    public function getText(): string
    {
        return $this->text;
    }

}