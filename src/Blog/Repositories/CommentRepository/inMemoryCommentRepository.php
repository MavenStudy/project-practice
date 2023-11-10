<?php
namespace Maven\ProjectPractice\Blog\Repositories\CommentRepository;

use Maven\ProjectPractice\Blog\Comment;
use Maven\ProjectPractice\Blog\Exceptions\CommentNotFoundException;
use Maven\ProjectPractice\Blog\UUID;

class inMemoryCommentRepository implements CommentRepositoryInterface{
    private array $comments = [];

    public function save(Comment $comment): void
    {
        $this->comments[(string)$comment->getUuid()] = $comment;
    }

    public function get(UUID $uuid): Comment
    {
        $uuidString = (string)$uuid;
        if (isset($this->comments[$uuidString])) {
            return $this->comments[$uuidString];
        }

        throw new CommentNotFoundException("Комментарий не найден: {$uuid}");
    }

    public function getByPost(UUID $postUuid): array
    {
        $postComments = [];
        foreach ($this->comments as $comment) {
            if ($comment->getPostUuid() == $postUuid) {
                $postComments[] = $comment;
            }
        }
        return $postComments;
    }

    public function getByAuthor(UUID $authorUuid): array
    {
        $authorComments = [];
        foreach ($this->comments as $comment) {
            if ($comment->getAuthorUuid() == $authorUuid) {
                $authorComments[] = $comment;
            }
        }
        return $authorComments;
    }
    public function getAllUUIDs(): array
    {
        return array_map(
            static function (Comment $comment) {
                return $comment->getUuid();
            },
            $this->comments
        );
    }
}