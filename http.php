<?php

use Maven\ProjectPractice\Blog\Exceptions\PostNotFoundException;
use Maven\ProjectPractice\Blog\Http\Actions\Posts\CreatePost;
use Maven\ProjectPractice\Blog\Http\Actions\Posts\DeletePost;
use Maven\ProjectPractice\Blog\Http\ErrorResponse;
use Maven\ProjectPractice\Blog\Http\Request;
use Maven\ProjectPractice\Blog\Http\SuccessfulResponse;
use Maven\ProjectPractice\Blog\Exceptions\HttpException;
use Maven\ProjectPractice\Blog\Http\Actions\Users\FindByUsername;
use Maven\ProjectPractice\Blog\Repositories\UserRepository\SqliteUsersRepository;
use Maven\ProjectPractice\Blog\Repositories\CommentRepository\SqliteCommentsRepository;
use Maven\ProjectPractice\Blog\Repositories\PostRepository\SqlitePostsRepository;
use Maven\ProjectPractice\Blog\Http\Actions\Users\CreateUser;
use Maven\ProjectPractice\Blog\Http\Actions\Comments\CreateComment;

require_once __DIR__.'/vendor/autoload.php';
$commentRepository = new SqliteCommentsRepository(new PDO('sqlite:' . __DIR__ . '/blog.sqlite'));
$userRepository = new SqliteUsersRepository(new PDO('sqlite:' . __DIR__ . '/blog.sqlite'));
$postRepository = new SqlitePostsRepository(new PDO('sqlite:' . __DIR__ . '/blog.sqlite'));

$request = new Request($_GET, $_SERVER,file_get_contents('php://input'));

try {
    $path = $request->path() ;
} catch (HttpException) {
    (new ErrorResponse)->send();
    return;
}
try {
    $method = $request->method();
} catch (HttpException) {
    (new ErrorResponse)->send();
    return;
}
$routes = [
    'GET'=>[
        '/users/show' => new FindByUsername($userRepository)
    ],
    'POST'=>[
        '/users/create' => new CreateUser($userRepository),
        '/posts/create' => new CreatePost($postRepository, $userRepository),

//              http://localhost:8000/comments/create
//              {
//                  "post_uuid": "8ed1dd98-6602-4a8d-a307-b5d11d897e92",
//                  "author_uuid": "eccf0eb2-cae3-4763-8eca-06cbfa4115be",
//                  "text": "text"
//              }
        '/comments/create' => new CreateComment($commentRepository, $userRepository, $postRepository),
    ],
    'DELETE'=>[
//            http://localhost:8000/posts/delete?uuid=a9a59b28-a805-4b99-a270-eee7d697374b
        '/posts/delete' => new DeletePost($postRepository, $userRepository),
    ],
];

if (!array_key_exists($method, $routes)) {
    (new ErrorResponse('Не найдено'))->send();
    return;
}

if (!array_key_exists($path, $routes[$method])) {
    (new ErrorResponse('Не найдено'))->send();
    return;
}

$actions = $routes[$method][$path];

try {
    if (is_callable($actions)) {
        $response = $actions($_GET['uuid'] ?? '');
    } else {
        $response = $actions->handle($request);
    }
} catch (Exception $error) {
    (new ErrorResponse($error->getMessage()))->send();
    return;
}

$response->send();