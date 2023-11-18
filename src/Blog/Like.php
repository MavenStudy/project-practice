<?php
namespace Maven\ProjectPractice\Blog;

class Like{

    public function __construct(UUID $uuid, UUID $postUuid, UUID $authorUuid)
    {
        $this->uuid = $uuid;
        $this->postUuid = $postUuid;
        $this->authorUuid = $authorUuid;

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

}