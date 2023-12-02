<?php
namespace Maven\ProjectPractice\Blog\Commands\Users;

use Maven\ProjectPractice\Blog\Commands\Arguments;
use Maven\ProjectPractice\Blog\Commands\CreateUserCommand;
use Maven\ProjectPractice\Blog\Exceptions\CommandException;

class CreateUsers
{
    private $userRepository;
    private $faker;
    private $logger;

    public function __construct($userRepository,  $faker, $logger)
    {
        $this->userRepository = $userRepository;
        $this->faker = $faker;
        $this->logger = $logger;
    }

    public function create($number_users): void
    {
        $command = new CreateUserCommand($this->userRepository, $this->logger);

        for ($i = 0; $i < $number_users; $i++) {
            $arguments = new Arguments([
                'username' => $this->faker->userName,
                'first_name' => $this->faker->firstName,
                'last_name' => $this->faker->lastName,
            ]);

            try {
                $command->handle($arguments);
            } catch (CommandException $error) {
                $this->logger->error($error->getMessage(), ['exception' => $error]);
            }
        }
    }
}