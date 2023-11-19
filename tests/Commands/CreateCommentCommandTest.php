<?php
namespace Commands;

use Maven\ProjectPractice\Blog\Commands\Arguments;
use Maven\ProjectPractice\Blog\Commands\CreateCommentCommand;
use Maven\ProjectPractice\Blog\Comment;
use Maven\ProjectPractice\Blog\Exceptions\CommandException;
use Maven\ProjectPractice\Blog\Exceptions\PostNotFoundException;
use Maven\ProjectPractice\Blog\Exceptions\UserNotFoundException;
use Maven\ProjectPractice\Blog\Name;
use Maven\ProjectPractice\Blog\Repositories\CommentRepository\CommentRepositoryInterface;
use Maven\ProjectPractice\Blog\Repositories\PostRepository\PostRepositoryInterface;
use Maven\ProjectPractice\Blog\Post;
use Maven\ProjectPractice\Blog\Repositories\UserRepository\UserRepositoryInterface;
use Maven\ProjectPractice\Blog\User;
use Maven\ProjectPractice\Blog\UUID;

use PHPUnit\Framework\TestCase;
use Psr\Log\LoggerInterface;

class CreateCommentCommandTest extends TestCase
{
    public function testCommentIsSavedInRepository(): void
    {
        $authorUuid = UUID::random();
        $postUuid = UUID::random();
        $text = 'Text';

        $userRepositoryMock = $this->createMock(UserRepositoryInterface::class);
        $userRepositoryMock->method('get')
            ->willReturn(new User(UUID::random(),'user',new Name('user','user')));

        $postRepositoryMock = $this->createMock(PostRepositoryInterface::class);
        $postRepositoryMock->method('get')
            ->willReturn(new Post(UUID::random(),UUID::random(),UUID::random(),'text'));

        $commentRepositoryMock = $this->createMock(CommentRepositoryInterface::class);
        $commentRepositoryMock->expects($this->once())
            ->method('save')
            ->with($this->isInstanceOf(Comment::class));
        $logger = $this->createMock(LoggerInterface::class);
        $command = new CreateCommentCommand($commentRepositoryMock, $userRepositoryMock, $postRepositoryMock, $logger );

        $arguments = new Arguments([
            'author_uuid' => $authorUuid,
            'post_uuid' => $postUuid,
            'text' => $text
        ]);
        $command->handle($arguments);
    }
    public function testItTrowsAnExceptionWhenPostOrAuthorAlreadyExists():void
    {
        $commentRepositoryMock = $this->createMock(CommentRepositoryInterface::class);
        $postRepositoryMock = $this->createMock(PostRepositoryInterface::class);
        $userRepositoryMock = $this->createMock(UserRepositoryInterface::class);
        $logger = $this->createMock(LoggerInterface::class);
        $command = $this->getMockBuilder(CreateCommentCommand::class)
            ->setConstructorArgs([$commentRepositoryMock, $userRepositoryMock,$postRepositoryMock,$logger])
            ->onlyMethods(['postExists','authorExists'])
            ->getMock();
        $command->expects($this->once())
            ->method('postExists')
            ->willReturn(false);
        $postUuid= UUID::random();
        $authorUuid= UUID::random();
        $this->expectException(CommandException::class);
        $this->expectExceptionMessage("Пост или автор не найден: {$postUuid} или {$authorUuid}");

        $command->handle(new Arguments(['author_uuid' => $authorUuid,'post_uuid' => $postUuid,'text' => 'text']));
    }
    public function testAuthorExistsReturnsFalseWhenUserNotFoundExceptionIsThrown(): void
    {
        $commentRepositoryMock = $this->createMock(CommentRepositoryInterface::class);
        $postRepositoryMock = $this->createMock(PostRepositoryInterface::class);
        $userRepositoryMock = $this->createMock(UserRepositoryInterface::class);

        $userRepositoryMock->method('get')
            ->willThrowException(new UserNotFoundException());
        $logger = $this->createMock(LoggerInterface::class);
        $command = new CreateCommentCommand($commentRepositoryMock, $userRepositoryMock,$postRepositoryMock,$logger );
        $result = $command->authorExists(UUID::random());
        $this->assertFalse($result);
    }
    public function testPostExistsReturnsFalseWhenPostNotFoundExceptionIsThrown(): void
    {
        $commentRepositoryMock = $this->createMock(CommentRepositoryInterface::class);
        $postRepositoryMock = $this->createMock(PostRepositoryInterface::class);
        $userRepositoryMock = $this->createMock(UserRepositoryInterface::class);

        $postRepositoryMock->method('get')
            ->willThrowException(new PostNotFoundException());
        $logger = $this->createMock(LoggerInterface::class);
        $command = new CreateCommentCommand($commentRepositoryMock, $userRepositoryMock,$postRepositoryMock,$logger);
        $result = $command->postExists(UUID::random());
        $this->assertFalse($result);
    }
}
