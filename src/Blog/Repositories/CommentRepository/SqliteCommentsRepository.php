<?php

namespace Maven\ProjectPractice\Blog\Repositories\CommentRepository;

use Maven\ProjectPractice\Blog\Comment;
use Maven\ProjectPractice\Blog\Exceptions\CommentNotFoundException;
use Maven\ProjectPractice\Blog\UUID;
use PDO;

class SqliteCommentsRepository implements CommentRepositoryInterface
{
    private PDO $connection;

    public function __construct(PDO $connection)
    {
        $this->connection = $connection;
    }

    public function save(Comment $comment): void
    {
        $statement = $this->connection->prepare(
            "INSERT INTO comments (uuid, post_uuid, author_uuid, text) VALUES (:uuid, :post_uuid, :author_uuid, :text)"
        );

        $statement->execute([
            ':uuid' => (string)$comment->getUuid(),
            ':post_uuid' => (string)$comment->getPostUuid(),
            ':author_uuid' => (string)$comment->getAuthorUuid(),
            ':text' => $comment->getText(),
        ]);
    }

    public function get(UUID $uuid): Comment
    {
        $statement = $this->connection->prepare("SELECT * FROM comments WHERE uuid = :uuid");
        $statement->execute([':uuid' => (string)$uuid]);

        $result = $statement->fetch(PDO::FETCH_ASSOC);

        if ($result === false) {
            throw new CommentNotFoundException("Комментарий не найден: {$uuid}");
        }

        return new Comment(
            new UUID($result['uuid']),
            new UUID($result['post_uuid']),
            new UUID($result['author_uuid']),
            $result['text']
        );
    }

    public function getByPost(UUID $postUuid): array
    {
        $statement = $this->connection->prepare("SELECT * FROM comments WHERE post_uuid = :post_uuid");
        $statement->execute([':post_uuid' => (string)$postUuid]);

        $comments = [];
        while ($result = $statement->fetch(PDO::FETCH_ASSOC)) {
            $comments[] = new Comment(
                new UUID($result['uuid']),
                new UUID($result['post_uuid']),
                new UUID($result['author_uuid']),
                $result['text']
            );
        }

        return $comments;
    }

    public function getByAuthor(UUID $authorUuid): array
    {
        $statement = $this->connection->prepare("SELECT * FROM comments WHERE author_uuid = :author_uuid");
        $statement->execute([':author_uuid' => (string)$authorUuid]);

        $comments = [];
        while ($result = $statement->fetch(PDO::FETCH_ASSOC)) {
            $comments[] = new Comment(
                new UUID($result['uuid']),
                new UUID($result['post_uuid']),
                new UUID($result['author_uuid']),
                $result['text']
            );
        }

        return $comments;
    }
    public function getAllUUIDs(): array
    {
        $statement = $this->connection->prepare("SELECT uuid FROM comments");
        $statement->execute();

        $result = $statement->fetchAll(PDO::FETCH_COLUMN);

        return $result;
    }
}
