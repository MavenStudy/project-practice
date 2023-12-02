<?php
namespace Maven\ProjectPractice\Blog\Commands\Posts;

use Maven\ProjectPractice\Blog\Commands\Arguments;
use Maven\ProjectPractice\Blog\Commands\CreatePostCommand;
use Maven\ProjectPractice\Blog\Exceptions\CommandException;

class CreatePosts
{
    private $postRepository;
    private $userRepository;
    private $faker;
    private $logger;

    public function __construct($postRepository,$userRepository,  $faker, $logger)
    {
        $this->postRepository = $postRepository;
        $this->userRepository = $userRepository;
        $this->faker = $faker;
        $this->logger = $logger;
    }

    function create($number_posts): void
    {
        $allUserUUIDs =  $this->userRepository->getAllUUIDs();
        $randomUserUUID = $allUserUUIDs[array_rand($allUserUUIDs)];

        $command = new CreatePostCommand( $this->postRepository,  $this->userRepository,$this->logger);

        for ($i = 0; $i < $number_posts; $i++) {
            $arguments = new Arguments([
                'author_uuid' => $randomUserUUID,
                'title' =>  $this->faker->word,
                'text' =>  $this->faker->realText,
            ]);
            try {
                $command->handle($arguments);
            } catch (CommandException $error) {
                $this->logger->error($error->getMessage(), ['exception' => $error]);
            }
        }
    }
}