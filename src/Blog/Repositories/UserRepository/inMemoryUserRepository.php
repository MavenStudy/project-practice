<?php
namespace Maven\ProjectPractice\Blog\Repositories\UserRepository;

use Maven\ProjectPractice\Blog\User;
use Maven\ProjectPractice\Blog\Exceptions\UserNotFoundException;
use Maven\ProjectPractice\Blog\UUID;

class inMemoryUserRepository implements UserRepositoryInterface{
    private array $users = [];
    public function save(User $user): void
    {
        $this->users[] = $user;
    }
    public function get(UUID $uuid):User
    {
        foreach ($this->users as $user)
        {
            if((string)$user->uuid() === (string)$uuid)
            {
                return $user;
            }
        }
        throw  new UserNotFoundException("Пользователь не найден: $uuid");
    }
    public function getByUsername(string $username): User
    {
        foreach ($this->users as $user)
        {
            if((string)$user->getUsername() === $username)
            {
                return $user;
            }
        }
        throw  new UserNotFoundException("Пользователь не найден: $username");
    }
    public function getAllUUIDs(): array
    {
        return array_map(
            static function (User $user) {
                return $user->getUuid();
            },
            $this->users
        );
    }
}