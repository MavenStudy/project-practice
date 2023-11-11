<?php

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


require_once __DIR__.'/vendor/autoload.php';

$connection = new PDO('sqlite:'.__DIR__.'/blog.sqlite');
$postRepository = new SqlitePostsRepository($connection);
$userRepository = new SqliteUsersRepository($connection);
$commentRepository = new SqliteCommentsRepository($connection);


$allUserUUIDs = $userRepository->getAllUUIDs();
$randomUserUUID = $allUserUUIDs[array_rand($allUserUUIDs)];

$allPostUUIDs = $postRepository->getAllUUIDs();
$randomPostUUID = $allPostUUIDs[array_rand($allPostUUIDs)];

$allCommentUUIDs = $commentRepository->getAllUUIDs();
$randomCommentUUID = $allCommentUUIDs[array_rand($allCommentUUIDs)];

#save для User
$command = new CreateUserCommand($userRepository);
try {
    $command->handle(Arguments::fromArgv($argv));
    echo "Пользователь успешно создан.\n";
} catch (CommandException $error) {
    echo $error->getMessage()."\n";
}

#get для User
//try {
//    $user = $userRepository->get(new UUID($randomUserUUID));
//    echo "User UUID: {$user->getUuid()},Username: {$user->getUsername()},first_name: {$user->getName()->getFirstName()},last_name: {$user->getName()->getLastName()}\n";
//} catch (UserNotFoundException $error) {
//    echo $error->getMessage()."\n";
//}

#save для Post
//$command = new CreatePostCommand($postRepository, $userRepository);
//try {
//    $command->handle(Arguments::fromArgv($argv));
//    echo "Пост успешно создан.\n";
//} catch (CommandException $error) {
//    echo $error->getMessage()."\n";
//}
#Получить все значения для Post
//try {
//    $posts = $postRepository->getAll();
//    foreach ($posts as $post) {
//        echo "Post UUID: {$post->getUuid()}, Author UUID: {$post->getAuthorUuid()}, Title: {$post->getTitle()}, Text: {$post->getText()}\n";
//    }
//} catch (PostNotFoundException $error) {
//    echo $error->getMessage()."\n";
//}

#get для Post
//try {
//    $post = $postRepository->get(new UUID($randomPostUUID));
//
//    echo "Post UUID: {$post->getUuid()}, Author UUID: {$post->getAuthorUuid()}, Title: {$post->getTitle()}, Text: {$post->getText()}\n";
//} catch (PostNotFoundException $error) {
//    echo $error->getMessage()."\n";
//}


#save для Comment
//$command = new CreateCommentCommand($commentRepository,$userRepository, $postRepository);
//try {
//    $command->handle(Arguments::fromArgv($argv));
//    echo "Комментарий успешно создан.\n";
//} catch (\Exception $error) {
//    echo "Ошибка: " . $error->getMessage() . "\n";
//}

#get для Comment
//try {
//    $comment = $commentRepository->get(new UUID($randomCommentUUID));
//    echo "Comment UUID: {$comment->getUuid()}
//Post UUID: {$comment->getPostUuid()}
//Author UUID: {$comment->getAuthorUuid()}
//Text: {$comment->getText()}\n";
//} catch (\Exception $error) {
//    echo "Ошибка: " . $error->getMessage() . "\n";
//}
