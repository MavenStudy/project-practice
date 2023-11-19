<?php
namespace Maven\ProjectPractice\Blog\Commands;

use Maven\ProjectPractice\Blog\Exceptions\ArgumentsException;
use Maven\ProjectPractice\Blog\Exceptions\PostNotFoundException;
use Maven\ProjectPractice\Blog\Exceptions\UserNotFoundException;
use Maven\ProjectPractice\Blog\Exceptions\CommentNotFoundException;
use Maven\ProjectPractice\Blog\Repositories\PostRepository\PostRepositoryInterface;
use Maven\ProjectPractice\Blog\Repositories\UserRepository\UserRepositoryInterface;
use Maven\ProjectPractice\Blog\Repositories\CommentRepository\CommentRepositoryInterface;
use Maven\ProjectPractice\Blog\Exceptions\CommandException;
use Maven\ProjectPractice\Blog\Comment;
use Maven\ProjectPractice\Blog\UUID;
use Psr\Log\LoggerInterface;

class CreateCommentCommand{

    public function __construct(
        private CommentRepositoryInterface $commentRepository,
        private UserRepositoryInterface $userRepository,
        private PostRepositoryInterface $postRepository,
        private LoggerInterface $logger
    ) {
    }
    public function handle(Arguments $arguments): void
    {
        $this->logger->info("Вызвано сохранение пользователя");
        $postUuid = new UUID($arguments->get('post_uuid'));
        $authorUuid = new UUID($arguments->get('author_uuid'));
        $text = $arguments->get('text');

        if (!$this->postExists($postUuid) || !$this->authorExists($authorUuid)) {
            $this->logger->warning("Пост или автор не найден: {$postUuid} или {$authorUuid}");
            throw new CommandException("Пост или автор не найден: {$postUuid} или {$authorUuid}");
        }
        $uuid = UUID::random();
        $comment = new Comment(
            $uuid,
            $postUuid,
            $authorUuid,
            $text
        );
        $this->commentRepository->save($comment);
        $this->logger->info("Комментарий записан: $uuid");
    }
    public function postExists(UUID $postUuid): bool
    {
        try {
            $this->postRepository->get($postUuid);
        } catch (PostNotFoundException $e) {
            return false;
        }

        return true;
    }

    public function authorExists(UUID $authorUuid): bool
    {
        try {
            $this->userRepository->get($authorUuid);
        } catch (UserNotFoundException $e) {
            return false;
        }

        return true;
    }
}