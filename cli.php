<?php


use Maven\ProjectPractice\Blog\Repositories\CommentRepository\SqliteCommentsRepository;
use Maven\ProjectPractice\Blog\Repositories\PostRepository\SqlitePostsRepository;
use Maven\ProjectPractice\Blog\Repositories\UserRepository\SqliteUsersRepository;

use Faker\Factory;
use Maven\ProjectPractice\Comments\CreateComments;
use Maven\ProjectPractice\Posts\CreatePosts;
use Maven\ProjectPractice\Users\CreateUsers;
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

$number_users = (int)$argv[1];
$number_posts = (int)$argv[2];
$number_comments = (int)$argv[3];

$users = new CreateUsers($userRepository, $faker, $logger);
$users->createUsers($number_users);

$posts = new CreatePosts($postRepository,$userRepository,  $faker, $logger);
$posts->createPosts($number_posts);

$comments = new CreateComments($commentRepository,$postRepository,$userRepository,  $faker, $logger);
$comments->createComments($number_comments);

