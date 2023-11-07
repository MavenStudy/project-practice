<?php
namespace Maven\ProjectPractice\Blog\Repositories\UserRepository;

use Maven\ProjectPractice\Blog\User;
use Maven\ProjectPractice\Blog\UUID;

interface UserRepositoryInterface
{
    public function save(User $user): void;
    public function get(UUID $uuid):User;
    public function getByUsername(string $username):User;

}