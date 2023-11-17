<?php
namespace Maven\ProjectPractice\Blog\Repositories\PostRepository;

use Maven\ProjectPractice\Blog\Post;
use Maven\ProjectPractice\Blog\Exceptions\PostNotFoundException;
use Maven\ProjectPractice\Blog\UUID;
use PDO;

class SqlitePostsRepository implements PostRepositoryInterface {
    public function __construct(
       private \PDO $connection,
    ){}
    public function save(Post $post): void
    {
        $statement = $this->connection->prepare(
            "INSERT INTO posts (uuid, author_uuid, title, text) VALUES (:uuid, :author_uuid, :title, :text)"
        );

        $statement->execute([
            ':uuid' => (string)$post->getUuid(),
            ':author_uuid' => (string)$post->getAuthorUuid(),
            ':title' => $post->getTitle(),
            ':text' => $post->getText(),
        ]);
    }
    public function get(UUID $uuid): Post
    {
        $statement = $this->connection->prepare("SELECT * FROM posts WHERE uuid = :uuid");
        $statement->execute([':uuid' => (string)$uuid]);

        $result = $statement->fetch(PDO::FETCH_ASSOC);

        if ($result === false) {
            throw new PostNotFoundException("Пост не найден: {$uuid}");
        }

        return new Post(
            new UUID($result['uuid']),
            new UUID($result['author_uuid']),
            $result['title'],
            $result['text']
        );
    }

    public function delete(UUID $uuid): void
    {
        $statement = $this->connection->prepare("DELETE FROM posts WHERE uuid = :uuid");
        $statement->execute([':uuid' => (string)$uuid]);
    }

    public function getByAuthor(UUID $authorUuid): array
    {
        $statement = $this->connection->prepare("SELECT * FROM posts WHERE author_uuid = :author_uuid");
        $statement->execute([':author_uuid' => (string)$authorUuid]);

        $posts = [];

        while ($result = $statement->fetch(PDO::FETCH_ASSOC)) {
            $posts[] = new Post(
                new UUID($result['uuid']),
                new UUID($result['author_uuid']),
                $result['title'],
                $result['text']
            );
        }

        return $posts;
    }
    public function getAll(): array
    {
        $statement = $this->connection->query("SELECT * FROM posts");

        $posts = [];

        while ($result = $statement->fetch(PDO::FETCH_ASSOC)) {
            $posts[] = new Post(
                new UUID($result['uuid']),
                new UUID($result['author_uuid']),
                $result['title'],
                $result['text']
            );
        }

        return $posts;
    }
    public function getAllUUIDs(): array
    {
        $statement = $this->connection->prepare("SELECT uuid FROM posts");
        $statement->execute();

        $result = $statement->fetchAll(PDO::FETCH_COLUMN);

        return $result;
    }

}