<?php
require_once __DIR__.'/vendor/autoload.php';
use Faker\Factory;

$faker = Factory::create('ru_RU');

$connection = new PDO('sqlite:'.__DIR__.'/blog.sqlite');

$connection->exec(
    "INSERT INTO users (first_name,last_name) VALUES ($faker->firstNameMale(), $faker->lastName())"
);
