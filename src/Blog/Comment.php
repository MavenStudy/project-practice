<?php
namespace Maven\ProjectPractice\Blog;

class Comment{

    public function __construct(
        private int $id,
        private int $authorId,
        private int $postId,
        private string $text,
    )
    {
    }

    public function __toString()
    {
        return 'ID '. $this->authorId .' прокомментировал статью: '.  $this->postId .' <br>'. $this->text ;
    }

}