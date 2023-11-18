<?php
namespace Maven\ProjectPractice\Blog\Http\Actions\Likes;

use Maven\ProjectPractice\Blog\Exceptions\CommandException;
use Maven\ProjectPractice\Blog\Exceptions\HttpException;
use Maven\ProjectPractice\Blog\Exceptions\InvalidArgumentException;
use Maven\ProjectPractice\Blog\Exceptions\LikeNotFoundException;
use Maven\ProjectPractice\Blog\Exceptions\PostNotFoundException;
use Maven\ProjectPractice\Blog\Exceptions\UserNotFoundException;
use Maven\ProjectPractice\Blog\Http\Actions\ActionInterface;
use Maven\ProjectPractice\Blog\Http\ErrorResponse;
use Maven\ProjectPractice\Blog\Http\Request;
use Maven\ProjectPractice\Blog\Http\Response;
use Maven\ProjectPractice\Blog\Http\SuccessfulResponse;
use Maven\ProjectPractice\Blog\Like;
use Maven\ProjectPractice\Blog\Repositories\LikeRepository\LikeRepositoryInterface;
use Maven\ProjectPractice\Blog\Repositories\UserRepository\UserRepositoryInterface;
use Maven\ProjectPractice\Blog\Repositories\PostRepository\PostRepositoryInterface;
use Maven\ProjectPractice\Blog\UUID;

class AddLikeToPost implements ActionInterface {
    public function __construct(
        private LikeRepositoryInterface $likeRepository,
        private UserRepositoryInterface $userRepository,
        private PostRepositoryInterface $postRepository
    )
    {
    }

    /**
     * @throws InvalidArgumentException
     * @throws HttpException
     */
    public function handle(Request $request): Response
    {
        $uuid = UUID::random();
        $postUuid = new UUID($request->jsonBodyField('post_uuid'));
        $authorUuid = new UUID($request->jsonBodyField('author_uuid'));

        if (!$this->authorExists($authorUuid)) {
            throw new UserNotFoundException("Автор не найден: $authorUuid");
        }
        if (!$this->postExists($postUuid)) {
            throw new PostNotFoundException("Пост не найден: $postUuid");
        }
        $likesByPost = $this->likeRepository->getByPost($postUuid);
        foreach ($likesByPost as $like) {
            if ((string)$like->getAuthorUuid() === (string)$authorUuid) {
                throw new CommandException("Лайк уже был поставлен");
            }
        }
        try {
            $like = new Like(UUID::random(), $postUuid, $authorUuid);
        } catch (HttpException $exception) {
            return new ErrorResponse($exception->getMessage());
        }
        $this->likeRepository->save($like);
        return new SuccessfulResponse([
            'uuid'=> (string)$uuid
        ]);
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
    private function postExists(UUID $postUuid): bool
    {
        try {
            $this->postRepository->get($postUuid);
            return true;
        } catch (PostNotFoundException $exception) {
            return false;
        }
    }

}