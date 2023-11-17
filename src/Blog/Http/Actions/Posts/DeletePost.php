<?php
namespace Maven\ProjectPractice\Blog\Http\Actions\Posts;

use Exception;
use Maven\ProjectPractice\Blog\Exceptions\HttpException;
use Maven\ProjectPractice\Blog\Exceptions\InvalidArgumentException;
use Maven\ProjectPractice\Blog\Exceptions\PostNotFoundException;
use Maven\ProjectPractice\Blog\Exceptions\UserNotFoundException;
use Maven\ProjectPractice\Blog\Http\Actions\ActionInterface;
use Maven\ProjectPractice\Blog\Http\ErrorResponse;
use Maven\ProjectPractice\Blog\Http\Request;
use Maven\ProjectPractice\Blog\Http\Response;
use Maven\ProjectPractice\Blog\Http\SuccessfulResponse;
use Maven\ProjectPractice\Blog\Repositories\UserRepository\UserRepositoryInterface;
use Maven\ProjectPractice\Blog\Repositories\PostRepository\PostRepositoryInterface;
use Maven\ProjectPractice\Blog\UUID;

class DeletePost implements ActionInterface {
    public function __construct(
        private PostRepositoryInterface $postRepository,
        private UserRepositoryInterface $userRepository
    )
    {
    }
    public function handle(Request $request): Response {
        $uuid = new UUID($request->query('uuid'));
        $this->postRepository->delete($uuid);
        return new SuccessfulResponse(['message' => 'Пост удален']);
    }
}