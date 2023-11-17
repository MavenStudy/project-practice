<?php
namespace Maven\ProjectPractice\UnitTest\Http\Actions\Posts;

use Maven\ProjectPractice\Blog\Exceptions\InvalidArgumentException;
use Maven\ProjectPractice\Blog\Http\Actions\Posts\CreatePost;
use Maven\ProjectPractice\Blog\Http\ErrorResponse;
use Maven\ProjectPractice\Blog\Http\Request;
use Maven\ProjectPractice\Blog\Http\SuccessfulResponse;
use Maven\ProjectPractice\Blog\Name;
use Maven\ProjectPractice\Blog\Repositories\PostRepository\PostRepositoryInterface;
use Maven\ProjectPractice\Blog\Repositories\UserRepository\UserRepositoryInterface;
use Maven\ProjectPractice\Blog\User;
use Maven\ProjectPractice\Blog\UUID;
use Maven\ProjectPractice\Blog\Exceptions\UserNotFoundException;

use PHPUnit\Framework\TestCase;

class CreatePostTest extends TestCase
{
    public function testHandleReturnsSuccessfulResponse()
    {
        $postRepositoryMock = $this->createMock(PostRepositoryInterface::class);
        $userRepositoryMock = $this->createMock(UserRepositoryInterface::class);
        $userRepositoryMock->expects($this->once())
            ->method('get')
            ->willReturn(new User(UUID::random(), 'username', new Name('test', 'test')));
        $postRepositoryMock->expects($this->once())
            ->method('save');

        $createPostAction = new CreatePost($postRepositoryMock, $userRepositoryMock);

        $request = new Request([], [], '
                {
                    "author_uuid": "eccf0eb2-cae3-4763-8eca-06cbfa4115be",
                    "title": "titl",
                    "text": "text"
                }');
        $response = $createPostAction->handle($request);
        $this->assertInstanceOf(SuccessfulResponse::class, $response);
    }
    public function testHandleThrowsExceptionOnInvalidUUIDFormat()
    {
        $postRepositoryMock = $this->createMock(PostRepositoryInterface::class);
        $userRepositoryMock = $this->createMock(UserRepositoryInterface::class);
        $createPostAction = new CreatePost($postRepositoryMock, $userRepositoryMock);
        $request = new Request([], [], '
                {
                    "author_uuid": "eccf0eb2-cae3-4763-8eca",
                    "title": "titl",
                    "text": "text"
                }');
        $this->expectException(InvalidArgumentException::class);
        $createPostAction->handle($request);
    }

    public function testHandleThrowsErrorIfUserNotFound()
    {
        $postRepositoryMock = $this->createMock(PostRepositoryInterface::class);
        $userRepositoryMock = $this->createMock(UserRepositoryInterface::class);
        $userRepositoryMock->method('get')
            ->willThrowException(new UserNotFoundException());

        $createPostAction = new CreatePost($postRepositoryMock, $userRepositoryMock);
        $request = new Request([], [], '
                {
                    "author_uuid": "eccf0eb2-cae3-4763-8eca-06cbfa4115be",
                    "title": "titl",
                    "text": "text"
                }');

        $this->expectException(UserNotFoundException::class);
        $createPostAction->handle($request);
    }
    public function testHandleThrowsErrorIfMissingData()
    {
        $postRepositoryMock = $this->createMock(PostRepositoryInterface::class);
        $userRepositoryMock = $this->createMock(UserRepositoryInterface::class);

        $createPostAction = new CreatePost($postRepositoryMock, $userRepositoryMock);
        $request = new Request([], [], '
                {
                    "title": "title",
                }');
        $response = $createPostAction->handle($request);
        $this->assertInstanceOf(ErrorResponse::class, $response);
        $this->expectOutputString('');
        $createPostAction->handle($request);
    }

}