<?php

use Maven\ProjectPractice\Blog\Commands\CreateLikeCommand;
use Maven\ProjectPractice\Blog\Exceptions\PostNotFoundException;
use Maven\ProjectPractice\Blog\Exceptions\UserNotFoundException;
use Maven\ProjectPractice\Blog\Http\Request;
use Maven\ProjectPractice\Blog\Repositories\LikeRepository\SqliteLikesRepository;
use Maven\ProjectPractice\Blog\Repositories\PostRepository\SqlitePostsRepository;
use Maven\ProjectPractice\Blog\Repositories\UserRepository\SqliteUsersRepository;
use Maven\ProjectPractice\Blog\Repositories\CommentRepository\SqliteCommentsRepository;
use Maven\ProjectPractice\Blog\UUID;
use Maven\ProjectPractice\Blog\Exceptions\CommandException;
use Maven\ProjectPractice\Blog\Commands\CreatePostCommand;
use Maven\ProjectPractice\Blog\Commands\CreateCommentCommand;
use Maven\ProjectPractice\Blog\Commands\CreateUserCommand;
use Maven\ProjectPractice\Blog\Commands\Arguments;
use \Maven\ProjectPractice\Blog\Post;
use Monolog\Handler\FilterHandler;
use Monolog\Handler\StreamHandler;
use Monolog\Logger;
use Psr\Log\LogLevel;


require_once __DIR__.'/vendor/autoload.php';

$connection = new PDO('sqlite:'.__DIR__.'/blog.sqlite');
$postRepository = new SqlitePostsRepository($connection);
$userRepository = new SqliteUsersRepository($connection);
$commentRepository = new SqliteCommentsRepository($connection);
$likeRepository = new SqliteLikesRepository($connection);

$allUserUUIDs = $userRepository->getAllUUIDs();
$randomUserUUID = $allUserUUIDs[array_rand($allUserUUIDs)];

$allPostUUIDs = $postRepository->getAllUUIDs();
$randomPostUUID = $allPostUUIDs[array_rand($allPostUUIDs)];

$allCommentUUIDs = $commentRepository->getAllUUIDs();
$randomCommentUUID = $allCommentUUIDs[array_rand($allCommentUUIDs)];

$logger = new Logger('blog');

$handler = new StreamHandler(__DIR__ . '/logs/blog.log');
$logger->pushHandler($handler);

$handler = new StreamHandler(__DIR__ . '/logs/blog.error.log');
$handler = new FilterHandler($handler, LogLevel::ERROR);
$logger->pushHandler($handler);

$handler = new StreamHandler('php://stdout');
$logger->pushHandler($handler);


#save для User
//$command = new CreateUserCommand($userRepository,$logger);
//try {
//    $command->handle(Arguments::fromArgv($argv));
//} catch (CommandException $error) {
//    $logger->error($error->getMessage(),['exception'=>$error]);
//}

#get для User
//try {
//    $user = $userRepository->get(new UUID($randomUserUUID));
//    echo "Получен пользователь: User UUID: {$user->getUuid()},Username: {$user->getUsername()},first_name: {$user->getName()->getFirstName()},last_name: {$user->getName()->getLastName()}";
//} catch (UserNotFoundException $error) {
//    $logger->error($error->getMessage(),['exception'=>$error]);
//}

#save для Post
$command = new CreatePostCommand($postRepository, $userRepository, $logger);
try {
    $command->handle(Arguments::fromArgv($argv));
} catch (CommandException $error) {
    $logger->error($error->getMessage(),['exception'=>$error]);
}

#Получить все значения для Post
//try {
//    $posts = $postRepository->getAll();
//    foreach ($posts as $post) {
//        echo "Получен пост: Post UUID: {$post->getUuid()}, Author UUID: {$post->getAuthorUuid()}, Title: {$post->getTitle()}, Text: {$post->getText()}";
//    }
//} catch (PostNotFoundException $error) {
//    $logger->error($error->getMessage(),['exception'=>$error]);
//}

#get для Post
//try {
//    $post = $postRepository->get(new UUID($randomPostUUID));
//    echo "Получен пост: Post UUID: {$post->getUuid()}, Author UUID: {$post->getAuthorUuid()}, Title: {$post->getTitle()}, Text: {$post->getText()}";
//} catch (PostNotFoundException $error) {
//    $logger->error($error->getMessage(),['exception'=>$error]);
//}


#save для Comment
//$command = new CreateCommentCommand($commentRepository,$userRepository, $postRepository);
//try {
//    $command->handle(Arguments::fromArgv($argv));
//} catch (\Exception $error) {
//    $logger->error($error->getMessage(),['exception'=>$error]);
//}

#get для Comment
//try {
//    $comment = $commentRepository->get(new UUID($randomCommentUUID));
//    echo "Comment UUID: {$comment->getUuid()}
//      Post UUID: {$comment->getPostUuid()}
//      Author UUID: {$comment->getAuthorUuid()}
//      Text: {$comment->getText()}\n";
//} catch (\Exception $error) {
//    $logger->error($error->getMessage(),['exception'=>$error]);
//}
