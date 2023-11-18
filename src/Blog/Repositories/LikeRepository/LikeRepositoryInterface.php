<?php
namespace Maven\ProjectPractice\Blog\Repositories\LikeRepository;

use Maven\ProjectPractice\Blog\Comment;
use Maven\ProjectPractice\Blog\Like;
use Maven\ProjectPractice\Blog\UUID;

interface LikeRepositoryInterface
{
    public function save(Like $like): void;
    public function get(UUID $uuid): Like;
    public function getByPost(UUID $postUuid): array;
    public function getByAuthor(UUID $authorUuid): array;

}