<?php
namespace Maven\ProjectPractice\Blog\Http;

namespace Maven\ProjectPractice\Blog\Http;

class SuccessfulResponse extends Response{
    protected const SUCCESS = true;
    public function __construct(
        private array $data=[]
    )
    {
    }

    protected function payload(): array
    {
        return ['data'=>$this->data];
    }
}