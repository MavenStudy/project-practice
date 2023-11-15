<?php
namespace Maven\ProjectPractice\UnitTest\Repositories\CommentRepository;
use Maven\ProjectPractice\Blog\Comment;
use Maven\ProjectPractice\Blog\Exceptions\CommentNotFoundException;
use Maven\ProjectPractice\Blog\Repositories\CommentRepository\SqliteCommentsRepository;
use Maven\ProjectPractice\Blog\UUID;
use PHPUnit\Framework\TestCase;

class SqliteCommentsRepositoryTest extends TestCase{
    public function testGetThrowsCommentNotFoundException(): void
    {
        $statementStub = $this->createStub(\PDOStatement::class);
        $statementStub->method('execute')->willReturn(true);
        $statementStub->method('fetch')->willReturn(false);
        $connectionStub = $this->createStub(\PDO::class);
        $connectionStub->method('prepare')->willReturn($statementStub);
        $repository = new SqliteCommentsRepository($connectionStub);
        $uuid = UUID::random();
        $this->expectException(CommentNotFoundException::class);
        $this->expectExceptionMessage("Комментарий не найден: {$uuid}");
        $repository->get($uuid);

    }

    public function testItSaveToDatabase(): void {
        $uuid = UUID::random();
        $author_uuid = UUID::random();
        $post_uuid = UUID::random();
        $statementMock = $this->createMock(\PDOStatement::class);
        $statementMock->expects($this->once())
            ->method('execute')
            ->with([
                ':uuid' => $uuid,
                ':post_uuid' => $post_uuid,
                ':author_uuid' => $author_uuid,
                ':text' =>'text',
        ]);

        $connectionStub = $this->createStub(\PDO::class);
        $connectionStub->method('prepare')
            ->willReturn($statementMock);

        $repository = new SqliteCommentsRepository($connectionStub);
        $repository->save(new Comment($uuid,$post_uuid, $author_uuid,'text'));
    }
    public function testCommentCreationFromDatabaseResult(): void
    {
        $uuid = UUID::random();
        $author_uuid = UUID::random();
        $post_uuid = UUID::random();
        $comments = [
            'uuid' => $uuid,
            'post_uuid' => $post_uuid ,
            'author_uuid' => $author_uuid ,
            'text' => 'text',
        ];

        $statementStub = $this->createStub(\PDOStatement::class);
        $statementStub->method('execute')->willReturn(true);
        $statementStub->method('fetch')->willReturn($comments);

        $connectionStub = $this->createStub(\PDO::class);
        $connectionStub->method('prepare')->willReturn($statementStub);

        $repository = new SqliteCommentsRepository($connectionStub);

        $comment = $repository->get($uuid);

        $this->assertInstanceOf(Comment::class, $comment);
        $this->assertEquals($uuid, $comment->getUuid());
        $this->assertEquals($post_uuid, $comment->getPostUuid());
        $this->assertEquals($author_uuid, $comment->getAuthorUuid());
        $this->assertEquals('text', $comment->getText());
    }
    public function testGetByPostReturnsArrayOfComments(): void
    {
        $author_uuid = UUID::random();
        $post_uuid = UUID::random();
        $statementStub = $this->createStub(\PDOStatement::class);
        $statementStub->method('execute')->willReturn(true);
        $statementStub->method('fetch')->willReturnOnConsecutiveCalls(
            ['uuid' => UUID::random(), 'post_uuid' =>  $post_uuid, 'author_uuid' => $author_uuid, 'text' => 'Comment 1'],
            ['uuid' => UUID::random(), 'post_uuid' =>  $post_uuid, 'author_uuid' => $author_uuid, 'text' => 'Comment 2']
        );


        $connectionStub = $this->createStub(\PDO::class);
        $connectionStub->method('prepare')->willReturn($statementStub);

        $repository = new SqliteCommentsRepository($connectionStub);
        $comments = $repository->getByPost($post_uuid);

        $this->assertCount(2, $comments);
        $this->assertInstanceOf(Comment::class, $comments[0]);
        $this->assertInstanceOf(Comment::class, $comments[1]);
    }


    public function testGetByAuthorReturnsArrayOfComments(): void
    {
        $author_uuid = UUID::random();
        $statementStub = $this->createStub(\PDOStatement::class);
        $statementStub->method('execute')->willReturn(true);
        $statementStub->method('fetch')->willReturnOnConsecutiveCalls(
            ['uuid' => UUID::random(), 'post_uuid' => UUID::random(), 'author_uuid' => $author_uuid, 'text' => 'Comment 1'],
            ['uuid' => UUID::random(), 'post_uuid' => UUID::random(), 'author_uuid' => $author_uuid, 'text' => 'Comment 2']
        );

        $connectionStub = $this->createStub(\PDO::class);
        $connectionStub->method('prepare')->willReturn($statementStub);
        $repository = new SqliteCommentsRepository($connectionStub);
        $comments = $repository->getByAuthor($author_uuid);

        $this->assertCount(2, $comments);
        $this->assertInstanceOf(Comment::class, $comments[0]);
        $this->assertInstanceOf(Comment::class, $comments[1]);
    }
    public function testGetAllUUIDs(): void
    {
        $uuid1 = UUID::random();
        $uuid2 = UUID::random();
        $uuid3 = UUID::random();

        $pdoStatement = $this->createMock(\PDOStatement::class);
        $pdoStatement->expects($this->once())
            ->method('fetchAll')
            ->willReturn([ $uuid1,  $uuid2,  $uuid3]);

        $pdo = $this->createMock(\PDO::class);
        $pdo->expects($this->once())
            ->method('prepare')
            ->with("SELECT uuid FROM comments")
            ->willReturn($pdoStatement);

        $postRepository = new SqliteCommentsRepository($pdo);

        $expectedUUIDs = [ $uuid1,  $uuid2,  $uuid3];
        $actualUUIDs = $postRepository->getAllUUIDs();

        $this->assertEquals($expectedUUIDs, $actualUUIDs);
    }

}