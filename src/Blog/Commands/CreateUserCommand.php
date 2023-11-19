<?php
namespace Maven\ProjectPractice\Blog\Commands;

use Maven\ProjectPractice\Blog\Exceptions\ArgumentsException;
use Maven\ProjectPractice\Blog\Exceptions\UserNotFoundException;
use Maven\ProjectPractice\Blog\Repositories\UserRepository\UserRepositoryInterface;
use Maven\ProjectPractice\Blog\Exceptions\CommandException;
use  Maven\ProjectPractice\Blog\User;
use Maven\ProjectPractice\Blog\UUID;
use Maven\ProjectPractice\Blog\Name;
use Psr\Log\LoggerInterface;

class CreateUserCommand{
    public function __construct(
        private UserRepositoryInterface $userRepository,
        private LoggerInterface $logger
    )
    {
    }
    public function handle(Arguments $arguments):void
    {
        $this->logger->info("Вызвано сохранение пользователя");
        $username = $arguments->get('username');

        if ($this->userExist($username))
        {
            $this->logger->warning("Пользователь: $username уже существует");
            throw new CommandException("Пользователь: $username уже существует");
        }
        $uuid = UUID::random();
        $this->userRepository->save(new User(
                $uuid,
                $username,
            new Name(
                $arguments->get('first_name'),
                $arguments->get('last_name'))
            )
        );
        $this->logger->info("Пользователь создан: $uuid");
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