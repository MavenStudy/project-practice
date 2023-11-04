<?php
namespace Maven\ProjectPractice\Blog;

class User
{
    public function __construct(
        private int $id,
        private Name $name
    ) {}

    public function getId(): int
    {
        return $this->id;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function __toString()
    {
        return "$this->name";
    }
}
