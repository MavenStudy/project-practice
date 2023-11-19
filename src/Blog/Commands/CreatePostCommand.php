<?php
namespace Maven\ProjectPractice\Blog\Commands;

use Maven\ProjectPractice\Blog\Exceptions\ArgumentsException;
use Maven\ProjectPractice\Blog\Exceptions\InvalidArgumentException;
use Maven\ProjectPractice\Blog\Exceptions\UserNotFoundException;
use Maven\ProjectPractice\Blog\Repositories\PostRepository\PostRepositoryInterface;
use Maven\ProjectPractice\Blog\Repositories\UserRepository\UserRepositoryInterface;
use Maven\ProjectPractice\Blog\Exceptions\CommandException;
use  Maven\ProjectPractice\Blog\Post;
use Maven\ProjectPractice\Blog\UUID;
use Maven\ProjectPractice\Blog\Name;
use Psr\Log\LoggerInterface;

class CreatePostCommand{


    public function __construct(
        private PostRepositoryInterface $postRepository,
        private UserRepositoryInterface $userRepository,
        private LoggerInterface $logger)
    {
    }

    /**
     * @throws ArgumentsException
     * @throws CommandException
     * @throws InvalidArgumentException
     */
    public function handle(Arguments $arguments): void
    {
        $this->logger->info("Вызвано сохранение поста");
        $authorUuid = new UUID($arguments->get('author_uuid'));
        $title = $arguments->get('title');
        $text = $arguments->get('text');

        if (!$this->authorExists($authorUuid)) {
            $this->logger->warning("Автор не найден: $authorUuid");
            throw new CommandException("Автор не найден: $authorUuid");
        }
        $uuid = UUID::random();
        $post = new Post($uuid, $authorUuid, $title, $text);
        $this->postRepository->save($post);
        $this->logger->info("Пост создан: $uuid");
    }
    public  function authorExists(UUID $authorUuid): bool
    {
        try {
            $this->userRepository->get($authorUuid);
        } catch (UserNotFoundException $e) {
            return false;
        }

        return true;
    }
}