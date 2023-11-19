<?php
namespace Maven\ProjectPractice\UnitTest\Commands;

use Maven\ProjectPractice\Blog\Commands\Arguments;
use Maven\ProjectPractice\Blog\Commands\CreatePostCommand;
use Maven\ProjectPractice\Blog\Exceptions\CommandException;
use Maven\ProjectPractice\Blog\Exceptions\UserNotFoundException;
use Maven\ProjectPractice\Blog\Name;
use Maven\ProjectPractice\Blog\Repositories\PostRepository\PostRepositoryInterface;
use Maven\ProjectPractice\Blog\Post;
use Maven\ProjectPractice\Blog\Repositories\UserRepository\UserRepositoryInterface;
use Maven\ProjectPractice\Blog\User;
use Maven\ProjectPractice\Blog\UUID;

use PHPUnit\Framework\TestCase;
use Psr\Log\LoggerInterface;

class CreatePostCommandTest extends TestCase
{
    public function testPostIsSavedInRepository(): void
    {
        $authorUuid = UUID::random();
        $title = 'Title';
        $text = 'Text';

        $userRepositoryMock = $this->createMock(UserRepositoryInterface::class);
        $userRepositoryMock->method('get')
            ->willReturn(new User(UUID::random(),'user',new Name('user','user')));

        $postRepositoryMock = $this->createMock(PostRepositoryInterface::class);
        $postRepositoryMock->expects($this->once())
            ->method('save')
            ->with($this->isInstanceOf(Post::class));
        $logger = $this->createMock(LoggerInterface::class);
        $command = new CreatePostCommand($postRepositoryMock, $userRepositoryMock,$logger);

        $arguments = new Arguments([
            'author_uuid' => $authorUuid,
            'title' => $title,
            'text' => $text
        ]);
        $command->handle($arguments);
    }

    public function testItTrowsAnExceptionWhenAuthorAlreadyExists():void
    {
        $postRepositoryMock = $this->createMock(PostRepositoryInterface::class);
        $userRepositoryMock = $this->createMock(UserRepositoryInterface::class);
        $logger = $this->createMock(LoggerInterface::class);
        $command = $this->getMockBuilder(CreatePostCommand::class)
            ->setConstructorArgs([$postRepositoryMock, $userRepositoryMock,$logger])
            ->onlyMethods(['authorExists'])
            ->getMock();
        $command->expects($this->once())
        ->method('authorExists')
            ->willReturn(false);
        $authorUuid= UUID::random();
        $this->expectException(CommandException::class);
        $this->expectExceptionMessage("Автор не найден: $authorUuid");

        $command->handle(new Arguments(['author_uuid' => $authorUuid,'title' => 'title','text' => 'text']));
    }

    public function testAuthorExistsReturnsFalseWhenUserNotFoundExceptionIsThrown(): void
    {
        $authorUuid = UUID::random();

        $userRepositoryMock = $this->createMock(UserRepositoryInterface::class);
        $userRepositoryMock->method('get')
            ->with($authorUuid)
            ->willThrowException(new UserNotFoundException());
        $logger = $this->createMock(LoggerInterface::class);
        $command = new CreatePostCommand(
            $this->createMock(PostRepositoryInterface::class),
            $userRepositoryMock, $logger
        );

        $this->assertFalse($command->authorExists($authorUuid));
    }

}
