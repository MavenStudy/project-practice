<?php
namespace Maven\ProjectPractice\UnitTest\Repositories\UserRepository;
use Maven\ProjectPractice\Blog\Exceptions\UserNotFoundException;
use Maven\ProjectPractice\Blog\Name;
use Maven\ProjectPractice\Blog\Repositories\UserRepository\SqliteUsersRepository;
use Maven\ProjectPractice\Blog\User;
use Maven\ProjectPractice\Blog\UUID;
use PHPUnit\Framework\TestCase;

class SqliteUsersRepositoryTest extends TestCase{
    public function testItThrowsAnExceptionWhenUserNotFound():void{
        $connectionStub = $this->createStub(\PDO::class);
        $statementStub = $this->createStub(\PDOStatement::class);
        $statementStub->method('fetch')
            ->willReturn(false);
        $connectionStub->method('prepare')
            ->willReturn($statementStub);
        $repository =new SqliteUsersRepository($connectionStub);
        $this->expectException(UserNotFoundException::class);
        $this->expectExceptionMessage('Не найден пользователь: Name');
        $repository->getByUsername('Name');
    }
    public function testItSaveToDatabase(): void {
        $uuid = UUID::random();
        $statementMock = $this->createMock(\PDOStatement::class);
        $statementMock->expects($this->once())
            ->method('execute')
            ->with([
            ':uuid' => $uuid,
            ':username'=> 'username',
            ':first_name' => 'user',
            ':last_name' => 'name'
        ]);

        $connectionStub = $this->createStub(\PDO::class);
        $connectionStub->method('prepare')
            ->willReturn($statementMock);

        $repository = new SqliteUsersRepository($connectionStub);
        $repository->save(new User($uuid, 'username', new Name('user', 'name')));
    }

    public function testGetUserByUUID(): void
    {
        $uuid = UUID::random();
        $userData = [
            'uuid' => $uuid,
            'username' => 'username',
            'first_name' => 'user',
            'last_name' => 'name'
        ];

        $statementStub = $this->createStub(\PDOStatement::class);
        $statementStub->method('execute')
            ->willReturn(true);
        $statementStub->method('fetch')
            ->willReturn($userData);

        $connectionStub = $this->createStub(\PDO::class);
        $connectionStub->method('prepare')
            ->willReturn($statementStub);

        $repository = new SqliteUsersRepository($connectionStub);
        $user = $repository->get($uuid);

        $this->assertInstanceOf(User::class, $user);
        $this->assertEquals($uuid, $user->getUuid());
        $this->assertEquals('username', $user->getUsername());
        $this->assertEquals('user', $user->getName()->getFirstName());
        $this->assertEquals('name', $user->getName()->getLastName());
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
            ->with("SELECT uuid FROM users")
            ->willReturn($pdoStatement);

        $userRepository = new SqliteUsersRepository($pdo);

        $expectedUUIDs = [ $uuid1,  $uuid2,  $uuid3];
        $actualUUIDs = $userRepository->getAllUUIDs();

        $this->assertEquals($expectedUUIDs, $actualUUIDs);
    }

}