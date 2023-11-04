<?php

class Review {
    private $id;
    private $productId;
    private $userId;
    private $rating;
    private $comment;

    public function __construct($id,$productId, $userId, $rating, $comment) {
        $this->id = $id;
        $this->productId = $productId;
        $this->userId = $userId;
        $this->rating = $rating;
        $this->comment = $comment;
    }
    public function __toString() {
        return
            "Пользователь: " . $this->getUserId() . "<br>" .
            "Продукт: " . $this->getProductId() . "<br>" .
            "Рейтинг: " . $this->getRating() . "<br>" .
            "Комментарий: " . $this->getComment() . "<br>";
    }
    public function getId() {
        return $this->id;
    }
    public function getProductId() {
        return $this->productId;
    }

    public function getUserId() {
        return $this->userId;
    }

    public function getRating() {
        return $this->rating;
    }

    public function getComment() {
        return $this->comment;
    }

}

