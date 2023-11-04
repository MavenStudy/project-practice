<?php
namespace Maven\ProjectPractice\Blog;

class Post{

    public function __construct(
        private int $id,
        private int $authorId,
        private string $title,
        private string $text,
    )
    {
    }

    public function __toString()
    {
        return 'ID '. $this->authorId .' пишет: <br>'.  $this->title .' <br>'. $this->text ;
    }

}
