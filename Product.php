<?php

class Product {
    private $id;
    private $name;
    private $price;
    private $description;
    private $imagePath;

    public function __construct($id, $name, $price, $description, $imagePath) {
        $this->id = $id;
        $this->name = $name;
        $this->price = $price;
        $this->description = $description;
        $this->imagePath = $imagePath;
    }
    public function __toString() {
        return
            "Название: " . $this->getName() . "<br>" .
            "Цена: " . $this->getPrice() . "<br>" .
            "Описание: " . $this->getDescription() . "<br>" .
            "Изображение: <img src='" . $this->getImagePath() . "' alt='Изображение продукта' width='150' height='150'>"."<br>";
    }

    public function getId() {
        return $this->id;
    }

    public function getName() {
        return $this->name;
    }

    public function getPrice() {
        return $this->price;
    }

    public function getDescription() {
        return $this->description;
    }

    public function getImagePath() {
        return $this->imagePath ;
    }

}

