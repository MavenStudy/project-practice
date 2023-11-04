<?php
include 'Product.php';
include 'Cart.php';
include 'DiscountedProduct.php';
include "User.php";
include "Review.php";
include "FeedbackForm.php";

$product1 = new Product(1, "Звезда", 100, "Пятиугольная", "1.jpg");
$product2 = new Product(2, "Звезда", 200, "Пятиугольная", "1.jpg");
$discountedProduct = new DiscountedProduct($product2, 0.2);

$cart = new Cart();
$cart->addProduct($product1, 1);
$cart->addProduct($product2, 8);
$cart->addProduct($discountedProduct , 4);
$cart->removeProduct($product1);

$cartContents = $cart->getProducts();

echo $product2;
foreach ($cartContents as $item) {
    $product = $item['product'];
    $quantity = $item['quantity'];
    echo "Продукт: " . $product->getName() . "<br> Количество: " . $quantity . "<br>";
}

echo "Общая стоимость: " . $cart->calculateTotalPrice(). "<br>";
echo $discountedProduct;

$user = new User(1, "babus", "babus@ex.com");

echo $user;
$review = new Review(1,1, 1, 5, "Отличный");
echo $review;


$feedback = new FeedbackForm($user, "Здравствуйте, всё понравилось");
echo $feedback;
$result = $feedback->sendNotification();
echo $result;
