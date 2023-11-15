<?php
namespace Maven\ProjectPractice\UnitTest\Commands;

use Maven\ProjectPractice\Blog\Commands\Arguments;
use Maven\ProjectPractice\Blog\Commands\CreateUserCommand;
use Maven\ProjectPractice\Blog\Exceptions\ArgumentsException;
use Maven\ProjectPractice\Blog\Exceptions\UserNotFoundException;
use Maven\ProjectPractice\Blog\Repositories\UserRepository\UserRepositoryInterface;
use Maven\ProjectPractice\Blog\Exceptions\CommandException;
use Maven\ProjectPractice\Blog\User;
use Maven\ProjectPractice\Blog\UUID;
use PHPUnit\Framework\TestCase;

class CreateUserCommandTest extends TestCase
{
    public function testItTrowsAnExceptionWhenUserAlreadyExists():void
    {
        $command = new CreateUserCommand($this->createMock(UserRepositoryInterface::class));
        $this->expectException(CommandException::class);
        $this->expectExceptionMessage("Пользователь: user уже существует");

        $command->handle(new Arguments(['username'=>'user']));
    }
    public function testItRequiresFirstName():void{
        $userRepository = $this->makeUserRepository();
        $command= new CreateUserCommand($userRepository);
        $this->expectException(ArgumentsException::class);
        $this->expectExceptionMessage("Отсутствует: first_name");

        $command->handle(new Arguments(['username'=>'user']));

    }
    public function testItRequiresLastName():void{
        $userRepository = $this->makeUserRepository();
        $command= new CreateUserCommand($userRepository);
        $this->expectException(ArgumentsException::class);
        $this->expectExceptionMessage("Отсутствует: last_name");
        $command->handle(new Arguments(['username'=>'user','first_name'=>'user']));

    }
    public function makeUserRepository():UserRepositoryInterface{
        return new class implements UserRepositoryInterface {
            public function save(User $user): void
            {
            }

            /**
             * @throws UserNotFoundException
             */
            public function get(UUID $uuid): User
            {
                throw new UserNotFoundException("Пользователь не найден");
            }

            public function getByUsername(string $username): User
            {
                throw new UserNotFoundException("Пользователь не найден");
            }

            private function getUser(\PDOStatement $statement, string $payload)
            {
                throw new UserNotFoundException("Пользователь не найден");
            }

            public function getAllUUIDs(): array
            {
                throw new UserNotFoundException("Пользователь не найден");
            }
        };
    }


}