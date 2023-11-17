<?php
namespace Maven\ProjectPractice\Blog\Http\Actions\Users;

use Maven\ProjectPractice\Blog\Exceptions\HttpException;
use Maven\ProjectPractice\Blog\Exceptions\UserNotFoundException;
use Maven\ProjectPractice\Blog\Http\Actions\ActionInterface;
use Maven\ProjectPractice\Blog\Http\ErrorResponse;
use Maven\ProjectPractice\Blog\Http\Request;
use Maven\ProjectPractice\Blog\Http\Response;
use Maven\ProjectPractice\Blog\Http\SuccessfulResponse;
use Maven\ProjectPractice\Blog\Name;
use Maven\ProjectPractice\Blog\Repositories\UserRepository\UserRepositoryInterface;
use Maven\ProjectPractice\Blog\User;
use Maven\ProjectPractice\Blog\UUID;

class CreateUser implements ActionInterface {
    public function __construct(
        private UserRepositoryInterface $userRepository
    )
    {
    }
    public function handle(Request $request): Response
    {
        try {
            $uuid = UUID::random();

            $user = new User(
                $uuid,
                $request->jsonBodyField('username'),
                new Name(
                    $request->jsonBodyField('first_name'),
                    $request->jsonBodyField('last_name')
                )
            );
        } catch (HttpException $exception)
        {
            return new ErrorResponse($exception->getMessage());
        }
        $this->userRepository->save($user);
        return new SuccessfulResponse([
            'uuid'=> (string)$uuid
        ]);
    }

}