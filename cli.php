<?php
use Maven\ProjectPractice\Blog\Repositories\UserRepository\SqliteUsersRepository;
use Maven\ProjectPractice\Blog\Exceptions\CommandException;
use Maven\ProjectPractice\Blog\Commands\CreateUserCommand;
use Maven\ProjectPractice\Blog\Commands\Arguments;

use Faker\Factory;
require_once __DIR__.'/vendor/autoload.php';

$faker = Factory::create('ru_RU');

$connection = new PDO('sqlite:'.__DIR__.'/blog.sqlite');

$userRepository = new SqliteUsersRepository($connection);

$command = new CreateUserCommand($userRepository);
try {
    $command->handle(Arguments::fromArgv($argv));
}catch (CommandException $error){
    echo $error->getMessage()."<br>";
}
//$userRepository->save(new User(UUID::random(),$faker->userName(),new Name($faker->firstNameMale(), $faker->lastName())));