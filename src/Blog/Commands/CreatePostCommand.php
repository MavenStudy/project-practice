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
class CreatePostCommand{
    private PostRepositoryInterface $postRepository;
    private UserRepositoryInterface $userRepository;

    public function __construct(PostRepositoryInterface $postRepository,UserRepositoryInterface $userRepository)
    {
        $this->postRepository = $postRepository;
        $this->userRepository = $userRepository;
    }

    /**
     * @throws ArgumentsException
     * @throws CommandException
     * @throws InvalidArgumentException
     */
    public function handle(Arguments $arguments): void
    {
        $authorUuid = new UUID($arguments->get('author_uuid'));
        $title = $arguments->get('title');
        $text = $arguments->get('text');

        if (!$this->authorExists($authorUuid)) {
            throw new CommandException("Автор не найден: $authorUuid");
        }

        $post = new Post(UUID::random(), $authorUuid, $title, $text);
        $this->postRepository->save($post);
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