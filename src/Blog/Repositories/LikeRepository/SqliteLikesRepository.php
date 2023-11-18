<?php

namespace Maven\ProjectPractice\Blog\Repositories\LikeRepository;

use Maven\ProjectPractice\Blog\Comment;
use Maven\ProjectPractice\Blog\Exceptions\CommentNotFoundException;
use Maven\ProjectPractice\Blog\Exceptions\LikeNotFoundException;
use Maven\ProjectPractice\Blog\Like;
use Maven\ProjectPractice\Blog\UUID;
use PDO;

class SqliteLikesRepository implements LikeRepositoryInterface
{
    private PDO $connection;

    public function __construct(PDO $connection)
    {
        $this->connection = $connection;
    }

    public function save(Like $like): void
    {
        $statement = $this->connection->prepare(
            "INSERT INTO likes (uuid, post_uuid, user_uuid) VALUES (:uuid, :post_uuid, :author_uuid)"
        );

        $statement->execute([
            ':uuid' => (string)$like->getUuid(),
            ':post_uuid' => (string)$like->getPostUuid(),
            ':author_uuid' => (string)$like->getAuthorUuid()
        ]);
    }

    public function get(UUID $uuid): Like
    {
        $statement = $this->connection->prepare("SELECT * FROM likes WHERE uuid = :uuid");
        $statement->execute([':uuid' => (string)$uuid]);

        $result = $statement->fetch(PDO::FETCH_ASSOC);

        if ($result === false) {
            throw new LikeNotFoundException("Лайк не найден: {$uuid}");
        }

        return new Like(
            new UUID($result['uuid']),
            new UUID($result['post_uuid']),
            new UUID($result['user_uuid'])
        );
    }

    public function getByPost(UUID $postUuid): array
    {
        $statement = $this->connection->prepare("SELECT * FROM likes WHERE post_uuid = :post_uuid");
        $statement->execute([':post_uuid' => (string)$postUuid]);

        $likes = [];
        while ($result = $statement->fetch(PDO::FETCH_ASSOC)) {
            $likes[] = new Like(
                new UUID($result['uuid']),
                new UUID($result['post_uuid']),
                new UUID($result['user_uuid'])
            );
        }

        return $likes;
    }

    public function getByAuthor(UUID $authorUuid): array
    {
        $statement = $this->connection->prepare("SELECT * FROM likes WHERE user_uuid = :author_uuid");
        $statement->execute([':author_uuid' => (string)$authorUuid]);

        $likes = [];
        while ($result = $statement->fetch(PDO::FETCH_ASSOC)) {
            $likes[] = new Like(
                new UUID($result['uuid']),
                new UUID($result['post_uuid']),
                new UUID($result['user_uuid'])
            );
        }

        return $likes;
    }
}
