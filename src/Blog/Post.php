<?php
namespace Maven\ProjectPractice\Blog;

class Post{

    public function __construct(
        UUID $uuid,
        UUID $authorUuid,
        string $title,
        string $text)
    {
        $this->uuid = $uuid;
        $this->authorUuid = $authorUuid;
        $this->title = $title;
        $this->text = $text;
    }

    public function getUuid(): UUID
    {
        return $this->uuid;
    }

    public function getAuthorUuid(): UUID
    {
        return $this->authorUuid;
    }

    public function getTitle(): string
    {
        return $this->title;
    }

    public function getText(): string
    {
        return $this->text;
    }


}
