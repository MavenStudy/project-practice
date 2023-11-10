<?php
namespace Maven\ProjectPractice\Blog\Repositories\PostRepository;

use Maven\ProjectPractice\Blog\Post;
use Maven\ProjectPractice\Blog\UUID;

interface PostRepositoryInterface
{
    public function save(Post $post): void;
    public function get(UUID $uuid): Post;
    public function getByAuthor(UUID $authorUuid): array;
    public function getAll(): array;
    public function getAllUUIDs(): array;

}