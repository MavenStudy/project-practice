<?php
namespace Maven\ProjectPractice\Blog\Commands\Comments;

use Maven\ProjectPractice\Blog\Commands\Arguments;
use Maven\ProjectPractice\Blog\Commands\CreateCommentCommand;


class CreateComments
{
    private $commentRepository;
    private $postRepository;
    private $userRepository;
    private $faker;
    private $logger;

    public function __construct($commentRepository,$postRepository,$userRepository,  $faker, $logger)
    {
        $this->commentRepository = $commentRepository;
        $this->postRepository = $postRepository;
        $this->userRepository = $userRepository;
        $this->faker = $faker;
        $this->logger = $logger;
    }

    function createComments($number_comments): void
    {
        $allPostUUIDs = $this->postRepository->getAllUUIDs();
        $randomPostUUID = $allPostUUIDs[array_rand($allPostUUIDs)];

        $allUserUUIDs = $this->userRepository->getAllUUIDs();
        $randomUserUUID = $allUserUUIDs[array_rand($allUserUUIDs)];

        $command = new CreateCommentCommand($this->commentRepository,$this->userRepository, $this->postRepository,$this->logger);

        for ($i = 0; $i < $number_comments; $i++) {
            $arguments = new Arguments([
                'post_uuid' => $randomPostUUID,
                'author_uuid' => $randomUserUUID,
                'text' => $this->faker->realText,
            ]);

            try {
                $command->handle($arguments);
            } catch (\Exception $error) {
                $this->logger->error($error->getMessage(), ['exception' => $error]);
            }
        }
    }
}