<?php
namespace Maven\ProjectPractice\Blog\Http\Actions\Comments;

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
use Maven\ProjectPractice\Blog\Comment;
use Maven\ProjectPractice\Blog\UUID;
use Psr\Log\LoggerInterface;

class CreateComment implements ActionInterface {
    public function __construct(
        private CommentRepositoryInterface $commentRepository,
        private UserRepositoryInterface $userRepository,
        private PostRepositoryInterface $postRepository,
        private LoggerInterface $logger
    )
    {
    }

    /**
     * @throws InvalidArgumentException
     * @throws HttpException
     */
    public function handle(Request $request): Response
    {   $this->logger->info("Вызвано сохранение комментария");
        $uuid = UUID::random();
        $postUuid = new UUID($request->jsonBodyField('post_uuid'));
        $authorUuid = new UUID($request->jsonBodyField('author_uuid'));
        $text = $request->jsonBodyField('text');

        try {
            $comment = new Comment($uuid, $postUuid, $authorUuid, $text);
        } catch (HttpException $exception)
        {
            $this->logger->error($exception->getMessage());
            return new ErrorResponse($exception->getMessage());
        }
        $this->commentRepository->save($comment);
        $this->logger->info("Комментарий создан: $uuid");
        return new SuccessfulResponse([
            'uuid'=> (string)$uuid
        ]);
    }

}