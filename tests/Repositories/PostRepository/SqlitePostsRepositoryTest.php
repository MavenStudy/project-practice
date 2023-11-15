<?php
namespace Maven\ProjectPractice\UnitTest\Repositories\PostRepository;
use Maven\ProjectPractice\Blog\Comment;
use Maven\ProjectPractice\Blog\Exceptions\PostNotFoundException;
use Maven\ProjectPractice\Blog\Exceptions\UserNotFoundException;
use Maven\ProjectPractice\Blog\Post;
use Maven\ProjectPractice\Blog\Repositories\PostRepository\SqlitePostsRepository;
use Maven\ProjectPractice\Blog\Repositories\UserRepository\SqliteUsersRepository;
use Maven\ProjectPractice\Blog\User;
use Maven\ProjectPractice\Blog\UUID;
use PHPUnit\Framework\TestCase;

class SqlitePostsRepositoryTest extends TestCase{
    public function testGetThrowsPostNotFoundException(): void
    {
        $statementStub = $this->createStub(\PDOStatement::class);
        $statementStub->method('execute')->willReturn(true);
        $statementStub->method('fetch')->willReturn(false);
        $connectionStub = $this->createStub(\PDO::class);
        $connectionStub->method('prepare')->willReturn($statementStub);
        $repository = new SqlitePostsRepository($connectionStub);
        $uuid = UUID::random();
        $this->expectException(PostNotFoundException::class);
        $this->expectExceptionMessage("Пост не найден: {$uuid}");
        $repository->get($uuid);
    }

    public function testItSaveToDatabase(): void {
        $uuid = UUID::random();
        $author_uuid = UUID::random();
        $statementMock = $this->createMock(\PDOStatement::class);
        $statementMock->expects($this->once())
            ->method('execute')
            ->with([
                ':uuid' => $uuid,
                ':author_uuid' => $author_uuid,
                ':title' => 'title',
                ':text' =>'text',
        ]);

        $connectionStub = $this->createStub(\PDO::class);
        $connectionStub->method('prepare')
            ->willReturn($statementMock);

        $repository = new SqlitePostsRepository($connectionStub);
        $repository->save(new Post($uuid, $author_uuid, 'title','text'));
    }

    public function testGetPostByUUID(): void
    {
        $uuid = UUID::random();
        $author_uuid = UUID::random();
        $postData = [
            'uuid' => $uuid,
            'author_uuid' => $author_uuid,
            'title' => 'title',
            'text' => 'text'
        ];

        $statementStub = $this->createStub(\PDOStatement::class);
        $statementStub->method('execute')->willReturn(true);
        $statementStub->method('fetch')->willReturn($postData);

        $connectionStub = $this->createStub(\PDO::class);
        $connectionStub->method('prepare')->willReturn($statementStub);

        $repository = new SqlitePostsRepository($connectionStub);

        $post = $repository->get($uuid);

        $this->assertInstanceOf(Post::class, $post);
        $this->assertEquals($uuid, $post->getUuid());
        $this->assertEquals($author_uuid, $post->getAuthorUuid());
        $this->assertEquals('title', $post->getTitle());
        $this->assertEquals('text', $post->getText());
    }

    public function testGetByAuthor(): void
    {
        $uuid = UUID::random();
        $author_uuid = UUID::random();
        $postData = [
            [
                'uuid' => $uuid,
                'author_uuid' => $author_uuid,
                'title' => 'Test Post 1',
                'text' => 'This is a test post 1.'
            ]
        ];

        $statementStub = $this->createStub(\PDOStatement::class);
        $statementStub->method('execute')->willReturn(true);
        $statementStub->method('fetch')->willReturnOnConsecutiveCalls(...$postData);

        $connectionStub = $this->createStub(\PDO::class);
        $connectionStub->method('prepare')->willReturn($statementStub);

        $repository = new SqlitePostsRepository($connectionStub);
        $posts = $repository->getByAuthor($uuid);

        $this->assertIsArray($posts);
        $this->assertNotEmpty($posts);
        $this->assertInstanceOf(Post::class, $posts[0]);

    }
    public function testGetAll(): void
    {
        $uuid = UUID::random();
        $author_uuid = UUID::random();
        $postData = [
            [
                'uuid' => $uuid,
                'author_uuid' => $author_uuid,
                'title' => 'title',
                'text' => 'text'
            ]
        ];

        $statementStub = $this->createStub(\PDOStatement::class);
        $statementStub->method('execute')->willReturn(true);
        $statementStub->method('fetch')->willReturnOnConsecutiveCalls(...$postData);

        $connectionStub = $this->createStub(\PDO::class);
        $connectionStub->method('query')->willReturn($statementStub);

        $repository = new SqlitePostsRepository($connectionStub);
        $posts = $repository->getAll();

        $this->assertIsArray($posts);
        $this->assertNotEmpty($posts);
        $this->assertInstanceOf(Post::class, $posts[0]);

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
            ->with("SELECT uuid FROM posts")
            ->willReturn($pdoStatement);

        $postRepository = new SqlitePostsRepository($pdo);

        $expectedUUIDs = [ $uuid1,  $uuid2,  $uuid3];
        $actualUUIDs = $postRepository->getAllUUIDs();

        $this->assertEquals($expectedUUIDs, $actualUUIDs);
    }

}