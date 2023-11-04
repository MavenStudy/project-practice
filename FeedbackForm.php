<?php

class FeedbackForm {
    private $user;
    private $message;

    public function __construct(User $user, $message) {
        $this->user = $user;
        $this->message = $message;
    }
    public function __toString() {
        return
            "Пользователь: " . $this->user->getUsername() . "<br>" .
            "Сообщение: " . $this->message . "<br>";

    }
    public function sendNotification() {
        return $this->user->getUsername() .", Ваше сообщение отправлено!";
    }
}


