<?php
namespace Maven\ProjectPractice\Blog;

class User
{
    public function __construct(
        private UUID $uuid,
        private string $username,
        private Name $name
    ) {}

    public function getUuid(): UUID
    {
        return $this->uuid;
    }

    public function getUsername(): string
    {
        return $this->username;
    }

    public function getName(): Name
    {
        return $this->name;
    }

    public function __toString()
    {
        return "$this->name";
    }
}
