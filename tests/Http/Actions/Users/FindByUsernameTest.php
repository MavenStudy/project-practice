<?php
namespace Maven\ProjectPractice\UnitTest\Http\Actions\Users;

use JsonException;
use Maven\ProjectPractice\Blog\Exceptions\UserNotFoundException;
use Maven\ProjectPractice\Blog\Http\Actions\Users\FindByUsername;
use Maven\ProjectPractice\Blog\Http\ErrorResponse;
use Maven\ProjectPractice\Blog\Http\Request;
use Maven\ProjectPractice\Blog\Http\SuccessfulResponse;
use Maven\ProjectPractice\Blog\Name;
use Maven\ProjectPractice\Blog\Repositories\UserRepository\UserRepositoryInterface;
use Maven\ProjectPractice\Blog\User;
use Maven\ProjectPractice\Blog\UUID;
use PHPUnit\Framework\TestCase;

class FindByUsernameTest extends TestCase{

    public function testItReturnErrorResponseIfNoUsernameProvided():void
    {
        $userRepository = $this->createMock(UserRepositoryInterface::class);
        $findByUsernameAction = new FindByUsername($userRepository);
        $request = new Request([],[],'');
        $response = $findByUsernameAction->handle($request);
        $this->assertInstanceOf(ErrorResponse::class, $response);
        $this->expectOutputString('{"success":false,"data":"\u041f\u0430\u0440\u0430\u043c\u0435\u0442\u0440: username \u043e\u0442\u0441\u0443\u0442\u0441\u0432\u0443\u0435\u0442"}');
        $response->send();
    }

    public function testItReturnErrorResponseIfNoUserNotFound(): void
    {
        $userRepository = $this->createMock(UserRepositoryInterface::class);
        $userRepository->method('getByUsername')
            ->willThrowException(new UserNotFoundException('User not found'));
        $findByUsernameAction = new FindByUsername($userRepository);
        $request = new Request(['username' => 'username'],[],'');
        $response = $findByUsernameAction->handle($request);
        $this->assertInstanceOf(ErrorResponse::class, $response);
        $response->send();
    }
    public function testItReturnsSuccessfulResponseIfUserFound(): void
    {
        $userRepository = $this->createMock(UserRepositoryInterface::class);
        $userRepository->method('getByUsername')
            ->willReturn(new User(
                UUID::random(),
                'username',
                new Name(
                    'first',
                    'last')));;
        $findByUsernameAction = new FindByUsername($userRepository);
        $request = new Request(['username' => 'username'],[],'');
        $response = $findByUsernameAction->handle($request);
        $this->assertInstanceOf(SuccessfulResponse::class, $response);
        $this->expectOutputString('{"success":true,"data":{"username":"username","first_name":"first","last_name":"last"}}');
        $response->send();
    }
}