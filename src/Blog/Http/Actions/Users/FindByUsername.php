<?php
namespace Maven\ProjectPractice\Blog\Http\Actions\Users;

use Maven\ProjectPractice\Blog\Exceptions\HttpException;
use Maven\ProjectPractice\Blog\Exceptions\UserNotFoundException;
use Maven\ProjectPractice\Blog\Http\Actions\ActionInterface;
use Maven\ProjectPractice\Blog\Http\ErrorResponse;
use Maven\ProjectPractice\Blog\Http\Request;
use Maven\ProjectPractice\Blog\Http\Response;
use Maven\ProjectPractice\Blog\Http\SuccessfulResponse;
use Maven\ProjectPractice\Blog\Repositories\UserRepository\UserRepositoryInterface;

class FindByUsername implements ActionInterface {
    public function __construct(
        private UserRepositoryInterface $userRepository
    )
    {
    }
    public function handle(Request $request): Response
    {
        try {
            $username = $request->query('username');
        } catch (HttpException $error) {
            return new ErrorResponse($error->getMessage());
        }

        try {
            $user = $this->userRepository->getByUsername($username);
            return new SuccessfulResponse([
                'username' => $user->getUsername(),
                'first_name' => $user->getName()->getFirstName(),
                'last_name' => $user->getName()->getLastName()
            ]);
        } catch (UserNotFoundException $error) {
            return new ErrorResponse($error->getMessage());
        }
    }

}