<?php

use Maven\ProjectPractice\Blog\Exceptions\PostNotFoundException;
use Maven\ProjectPractice\Blog\Http\Actions\Likes\AddLikeToPost;
use Maven\ProjectPractice\Blog\Http\Actions\Posts\CreatePost;
use Maven\ProjectPractice\Blog\Http\Actions\Posts\DeletePost;
use Maven\ProjectPractice\Blog\Http\ErrorResponse;
use Maven\ProjectPractice\Blog\Http\Request;
use Maven\ProjectPractice\Blog\Http\SuccessfulResponse;
use Maven\ProjectPractice\Blog\Exceptions\HttpException;
use Maven\ProjectPractice\Blog\Http\Actions\Users\FindByUsername;
use Maven\ProjectPractice\Blog\Repositories\LikeRepository\SqliteLikesRepository;
use Maven\ProjectPractice\Blog\Repositories\UserRepository\SqliteUsersRepository;
use Maven\ProjectPractice\Blog\Repositories\CommentRepository\SqliteCommentsRepository;
use Maven\ProjectPractice\Blog\Repositories\PostRepository\SqlitePostsRepository;
use Maven\ProjectPractice\Blog\Http\Actions\Users\CreateUser;
use Maven\ProjectPractice\Blog\Http\Actions\Comments\CreateComment;
use Monolog\Handler\FilterHandler;
use Monolog\Handler\StreamHandler;
use Monolog\Logger;
use Psr\Log\LogLevel;

require_once __DIR__.'/vendor/autoload.php';

$commentRepository = new SqliteCommentsRepository(new PDO('sqlite:' . __DIR__ . '/blog.sqlite'));
$userRepository = new SqliteUsersRepository(new PDO('sqlite:' . __DIR__ . '/blog.sqlite'));
$postRepository = new SqlitePostsRepository(new PDO('sqlite:' . __DIR__ . '/blog.sqlite'));
$likeRepository = new SqliteLikesRepository(new PDO('sqlite:' . __DIR__ . '/blog.sqlite'));

$request = new Request($_GET, $_SERVER,file_get_contents('php://input'));

$logger = new Logger('blog');

$handler = new StreamHandler(__DIR__ . '/logs/blog.log');
$logger->pushHandler($handler);

$handler = new StreamHandler(__DIR__ . '/logs/blog.error.log');
$handler = new FilterHandler($handler, LogLevel::ERROR);
$logger->pushHandler($handler);

$handler = new StreamHandler('php://stdout');
$logger->pushHandler($handler);

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
        '/users/create' => new CreateUser($userRepository,$logger),
        '/posts/create' => new CreatePost($postRepository, $userRepository),

//              http://localhost:8000/comments/create
//              {
//                  "post_uuid": "8ed1dd98-6602-4a8d-a307-b5d11d897e92",
//                  "author_uuid": "eccf0eb2-cae3-4763-8eca-06cbfa4115be",
//                  "text": "text"
//              }
        '/comments/create' => new CreateComment($commentRepository, $userRepository, $postRepository,$logger),
    //          http://localhost:8000/posts/like/add
    //          {
    //              "post_uuid": "8ed1dd98-6602-4a8d-a307-b5d11d897e92",
    //              "author_uuid": "5515a65a-c928-4c67-aa67-3f20e54a2d53"
    //           }
        '/posts/like/add' => new AddLikeToPost($likeRepository, $userRepository, $postRepository),
    ],
    'DELETE'=>[
//            http://localhost:8000/posts/delete?uuid=a9a59b28-a805-4b99-a270-eee7d697374b
        '/posts/delete' => new DeletePost($postRepository, $userRepository),
    ],
];

if (!array_key_exists($method, $routes)||!array_key_exists($path, $routes[$method])) {
    $message = "Не найден путь: $method $path ";
    $logger->notice($message);
    (new ErrorResponse($message))->send();
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
    $logger->error($error->getMessage());
    (new ErrorResponse())->send();
    return;
}

$response->send();