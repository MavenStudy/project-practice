<?php
namespace Maven\ProjectPractice\Blog\Repositories\UserRepository;

use Maven\ProjectPractice\Blog\User;
use Maven\ProjectPractice\Blog\Name;
use Maven\ProjectPractice\Blog\Exceptions\UserNotFoundException;
use Maven\ProjectPractice\Blog\UUID;
use PDO;

class SqliteUsersRepository implements UserRepositoryInterface {
    public function __construct(
       private \PDO $connection,
    ){}
    public function save(User $user): void
    {
        $statement = $this->connection->prepare(
            "INSERT INTO users (uuid,username,first_name,last_name) VALUES (:uuid,:username,:first_name,:last_name)"
        );
        $statement->execute([
            ':uuid' => (string)$user->getUuid(),
            ':username'=> $user->getUsername(),
            ':first_name' => $user->getName()->getFirstName(),
            ':last_name' => $user->getName()->getLastName()
        ]);
    }
    public function get(UUID $uuid): User
    {
        $statement = $this->connection->prepare("SELECT * FROM users WHERE uuid = :uuid");
        $statement->execute([':uuid' => (string)$uuid]);

        return $this->getUser($statement, $uuid);
    }

    public function getByUsername(string $username): User
    {
        $statement = $this->connection->prepare(
            'SELECT * FROM users WHERE username = :username'
        );
        $statement->execute([
            ':username' => $username,
        ]);
        return $this->getUser($statement,$username);
    }
    private function getUser(\PDOStatement $statement,string $payload)
    {
        $result = $statement->fetch(\PDO::FETCH_ASSOC);
        if ($result === false)
        {
            throw new UserNotFoundException('Не найден пользователь: $payload');
        }
        return new User(
            new UUID($result['uuid']),
            $result['username'],
            new Name($result['first_name'],$result['last_name']));
    }
    public function getAllUUIDs(): array
    {
        $statement = $this->connection->prepare("SELECT uuid FROM users");
        $statement->execute();

        $result = $statement->fetchAll(PDO::FETCH_COLUMN);

        return $result;
    }
}