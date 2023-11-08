<?php
require_once __DIR__.'/vendor/autoload.php';

use Maven\ProjectPractice\Blog\Name;
use Maven\ProjectPractice\Blog\User;
use Maven\ProjectPractice\Blog\Post;
use Maven\ProjectPractice\Blog\Comment;
use Faker\Factory;

$faker = Factory::create('ru_RU');

$user= new User(1, new Name($faker->firstNameMale(), $faker->lastName()));
echo "Пользователь: ".$user.PHP_EOL;
echo PHP_EOL;

$post = new Post(1,1, "Краткие цитаты:", $faker->realText());
echo $post.PHP_EOL;
echo PHP_EOL;

$comment = new Comment(1,1, 1, $faker->realText());
echo $comment.PHP_EOL;

