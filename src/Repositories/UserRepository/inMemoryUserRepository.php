<?php
namespace Maven\ProjectPractice\Repositories\UserRepository;

use Maven\ProjectPractice\Blog\User;
use Maven\ProjectPractice\Exceptions\UserNotFoundException;

class inMemoryUserRepository{
    private array $users = [];
    public function save(User $user): void
    {
        $this->users[] = $user;
    }
    public function get(int $id):User
    {
        foreach ($this->users as $user)
        {
            if($user->id() === $id)
            {
                return $user;
            }
        }
        throw  new UserNotFoundException("Пользователь не найден: $id");
    }
}