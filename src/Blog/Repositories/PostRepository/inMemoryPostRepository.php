<?php
namespace Maven\ProjectPractice\Blog\Repositories\PostRepository;

use Maven\ProjectPractice\Blog\Post;
use Maven\ProjectPractice\Blog\Exceptions\PostNotFoundException;
use Maven\ProjectPractice\Blog\UUID;

class inMemoryPostRepository implements PostRepositoryInterface{
    private array $posts = [];
    public function save(Post $post): void
    {
        $this->posts[] = $post;
    }
    public function get(UUID $uuid):Post
    {
        foreach ($this->posts as $post) {
            if ($post->getUuid() == $uuid) {
                return $post;
            }
        }

        throw new PostNotFoundException("Пост не найден: {$uuid}");
    }
    public function getByAuthor(UUID $authorUuid): array
    {
        $authorPosts = [];

        foreach ($this->posts as $post) {
            if ($post->getAuthorUuid() == $authorUuid) {
                $authorPosts[] = $post;
            }
        }

        return $authorPosts;
    }
    public function getAll(): array
    {
        return $this->posts;
    }

    public function getAllUUIDs(): array
    {
        return array_map(
            static function (Post $post) {
                return $post->getUuid();
            },
            $this->posts
        );
    }

}