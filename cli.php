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

$commandClasses = [
    CreateUsers::class,
    CreatePosts::class,
    CreateComments::class,
];

$commandDependencies = [
    CreateUsers::class => [$userRepository, $faker, $logger],
    CreatePosts::class => [$postRepository, $userRepository, $faker, $logger],
    CreateComments::class => [$commentRepository, $postRepository, $userRepository, $faker, $logger],
];

$commandNumbers = [
    CreateUsers::class =>  (int)$argv[1],
    CreatePosts::class => (int)$argv[2],
    CreateComments::class => (int)$argv[3],
];

foreach ($commandClasses as $commandClass) {
    $command = new $commandClass(...$commandDependencies[$commandClass]);
    $number = $commandNumbers[$commandClass];
    $command->create($number);
}

