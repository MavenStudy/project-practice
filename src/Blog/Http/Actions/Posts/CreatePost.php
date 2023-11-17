<?php
namespace Maven\ProjectPractice\Blog\Http\Actions\Posts;

use Maven\ProjectPractice\Blog\Exceptions\ArgumentsException;
use Maven\ProjectPractice\Blog\Exceptions\HttpException;
use Maven\ProjectPractice\Blog\Exceptions\InvalidArgumentException;
use Maven\ProjectPractice\Blog\Exceptions\UserNotFoundException;
use Maven\ProjectPractice\Blog\Http\Actions\ActionInterface;
use Maven\ProjectPractice\Blog\Http\ErrorResponse;
use Maven\ProjectPractice\Blog\Http\Request;
use Maven\ProjectPractice\Blog\Http\Response;
use Maven\ProjectPractice\Blog\Http\SuccessfulResponse;
use Maven\ProjectPractice\Blog\Name;
use Maven\ProjectPractice\Blog\Repositories\CommentRepository\CommentRepositoryInterface;
use Maven\ProjectPractice\Blog\Repositories\UserRepository\UserRepositoryInterface;
use Maven\ProjectPractice\Blog\Repositories\PostRepository\PostRepositoryInterface;
use Maven\ProjectPractice\Blog\Post;
use Maven\ProjectPractice\Blog\UUID;

class CreatePost implements ActionInterface {
    public function __construct(
        private PostRepositoryInterface $postRepository,
        private UserRepositoryInterface $userRepository
    )
    {
    }

    /**
     * @throws InvalidArgumentException
     * @throws HttpException
     */
    public function handle(Request $request): Response
    {
        try {
            $uuid = UUID::random();
            $authorUuid = new UUID($request->jsonBodyField('author_uuid'));
            $title = $request->jsonBodyField('title');
            $text = $request->jsonBodyField('text');

            $authorExists = $this->authorExists($authorUuid);

            if (!$authorExists) {
                throw new UserNotFoundException("Автор не найден: $authorUuid");
            }

            $post = new Post($uuid, $authorUuid, $title, $text);
            $this->postRepository->save($post);

            return new SuccessfulResponse(['uuid' => (string)$uuid]);
        } catch (HttpException $exception) {
            return new ErrorResponse($exception->getMessage());
        }
    }
    private function authorExists(UUID $authorUuid): bool
    {
        try {
            $this->userRepository->get($authorUuid);
            return true;
        } catch (UserNotFoundException $exception) {
            return false;
        }
    }

}