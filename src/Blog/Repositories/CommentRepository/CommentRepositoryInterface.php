<?php
namespace Maven\ProjectPractice\Blog\Repositories\CommentRepository;

use Maven\ProjectPractice\Blog\Comment;
use Maven\ProjectPractice\Blog\UUID;

interface CommentRepositoryInterface
{
    public function save(Comment $comment): void;
    public function get(UUID $uuid): Comment;
    public function getByPost(UUID $postUuid): array;
    public function getByAuthor(UUID $authorUuid): array;
    public function getAllUUIDs(): array;
}