<?php
namespace Maven\ProjectPractice\Blog\Commands;

use Maven\ProjectPractice\Blog\Exceptions\ArgumentsException;
use Maven\ProjectPractice\Blog\Exceptions\UserNotFoundException;
use Maven\ProjectPractice\Blog\Repositories\UserRepository\UserRepositoryInterface;
use Maven\ProjectPractice\Blog\Exceptions\CommandException;
use  Maven\ProjectPractice\Blog\User;
use Maven\ProjectPractice\Blog\UUID;
use Maven\ProjectPractice\Blog\Name;
class CreateUserCommand{
    public function __construct(
        private UserRepositoryInterface $userRepository,
    )
    {
    }
    public function handle(Arguments $arguments):void
    {
        $username = $arguments->get('username');

        if ($this->userExist($username))
        {
            throw new CommandException("Пользователь: $username уже существует");
        }
        $this->userRepository->save(new User(
            UUID::random(),
                $username,
            new Name(
                $arguments->get('first_name'),
                $arguments->get('last_name'))
            )
        );
    }
    public function userExist(string $username):bool
    {
        try{
            $this->userRepository->getByUsername($username);
        } catch (UserNotFoundException){
            return false;
        }
        return true;
    }
}