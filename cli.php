<?php


use Faker\Factory;
use Maven\ProjectPractice\Blog\Commands\Comments\CreateComments;
use Maven\ProjectPractice\Blog\Commands\Posts\CreatePosts;
use Maven\ProjectPractice\Blog\Commands\Users\CreateUsers;
use Maven\ProjectPractice\Blog\Repositories\CommentRepository\SqliteCommentsRepository;
use Maven\ProjectPractice\Blog\Repositories\PostRepository\SqlitePostsRepository;
use Maven\ProjectPractice\Blog\Repositories\UserRepository\SqliteUsersRepository;
use Monolog\Handler\FilterHandler;
use Monolog\Handler\StreamHandler;
use Monolog\Logger;
use Psr\Log\LogLevel;

require_once __DIR__.'/vendor/autoload.php';

$connection = new PDO('sqlite:'.__DIR__.'/blog.sqlite');
$postRepository = new SqlitePostsRepository($connection);
$userRepository = new SqliteUsersRepository($connection);
$commentRepository = new SqliteCommentsRepository($connection);

$faker = Factory::create('ru_RU');

$logger = new Logger('blog');

$handler = new StreamHandler(__DIR__ . '/logs/blog.log');
$logger->pushHandler($handler);

$handler = new StreamHandler(__DIR__ . '/logs/blog.error.log');
$handler = new FilterHandler($handler, LogLevel::ERROR);
$logger->pushHandler($handler);

$handler = new StreamHandler('php://stdout');
$logger->pushHandler($handler);

$commandsMap = [
    'user:create' => function ($userRepository, $faker, $logger, $number) {
        $command = new CreateUsers($userRepository, $faker, $logger);
        $command->create($number);
    },
    'post:create' => function ($postRepository, $userRepository, $faker, $logger, $number) {
        $command = new CreatePosts($postRepository, $userRepository, $faker, $logger);
        $command->create($number);
    },
    'comment:create' => function ($commentRepository, $postRepository, $userRepository, $faker, $logger, $number) {
        $command = new CreateComments($commentRepository, $postRepository, $userRepository, $faker, $logger);
        $command->create($number);
    },
];

$command = $argv[1];
$number = $argv[2];

$function = $commandsMap[$command];

switch ($command) {
    case 'user:create':
        $function($userRepository, $faker, $logger, $number);
        break;
    case 'post:create':
        $function($postRepository, $userRepository, $faker, $logger, $number);
        break;
    case 'comment:create':
        $function($commentRepository,$postRepository, $userRepository, $faker, $logger, $number);
        break;
    default:
        echo 'Команда не найдена.';
        break;
}
