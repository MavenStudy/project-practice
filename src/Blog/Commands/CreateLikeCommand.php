<?php
namespace Maven\ProjectPractice\Blog\Commands;

use Maven\ProjectPractice\Blog\Exceptions\ArgumentsException;
use Maven\ProjectPractice\Blog\Exceptions\PostNotFoundException;
use Maven\ProjectPractice\Blog\Exceptions\UserNotFoundException;
use Maven\ProjectPractice\Blog\Exceptions\CommentNotFoundException;
use Maven\ProjectPractice\Blog\Like;
use Maven\ProjectPractice\Blog\Repositories\CommentRepository\CommentRepositoryInterface;
use Maven\ProjectPractice\Blog\Repositories\LikeRepository\LikeRepositoryInterface;
use Maven\ProjectPractice\Blog\Repositories\PostRepository\PostRepositoryInterface;
use Maven\ProjectPractice\Blog\Repositories\UserRepository\UserRepositoryInterface;
use Maven\ProjectPractice\Blog\Exceptions\CommandException;
use Maven\ProjectPractice\Blog\Comment;
use Maven\ProjectPractice\Blog\UUID;

class CreateLikeCommand{
    private LikeRepositoryInterface $likeRepository;
    private UserRepositoryInterface $userRepository;
    private PostRepositoryInterface $postRepository;

    public function __construct(
        LikeRepositoryInterface $likeRepository,
        UserRepositoryInterface $userRepository,
        PostRepositoryInterface $postRepository
    ) {
        $this->likeRepository = $likeRepository;
        $this->userRepository = $userRepository;
        $this->postRepository = $postRepository;
    }
    public function handle(Arguments $arguments): void
    {
        $postUuid = new UUID($arguments->get('post_uuid'));
        $authorUuid = new UUID($arguments->get('author_uuid'));

        if (!$this->postExists($postUuid) || !$this->authorExists($authorUuid)) {
            throw new CommandException("Пост или автор не найден: {$postUuid} или {$authorUuid}");
        }

        $likesByPost = $this->likeRepository->getByPost($postUuid);
        foreach ($likesByPost as $like) {
            if ((string)$like->getAuthorUuid() === (string)$authorUuid) {
                throw new CommandException("Лайк уже был поставлен");
            }
        }

        $like = new Like(
            UUID::random(),
            $postUuid,
            $authorUuid
        );
        $this->likeRepository->save($like);
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