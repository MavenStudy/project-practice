<?php

namespace Maven\ProjectPractice\Blog\Http;
use Maven\ProjectPractice\Blog\Exceptions\HttpException;

class Request{
    public function __construct(
        private array $get,
        private array $server,
        private string $body,

    )
    {

    }
    public function method():string{
        if(!array_key_exists('REQUEST_METHOD', $this->server)){
            throw new HttpException("Не удается получить метод запроса");
        }
        return $this->server['REQUEST_METHOD'];
    }
    public function jsonBody():array{
        try {
            $data =json_decode(
                $this->body,
                associative: true,
                flags: JSON_THROW_ON_ERROR
            );
        }catch (\JsonException $exception)
        {
            throw new HttpException("Не получаетcя декодировать body");
        }
        if(!is_array($data))
        {
            throw new HttpException("Не array/object в body");
        }
        return $data;
    }

    public function jsonBodyField(string $field):mixed{

        $data = $this->jsonBody();

        if(!array_key_exists($field,$data)){
            throw new HttpException("Отсутсвует поле: $field");
        }
        if(empty($data[$field])){
            throw new HttpException("Поле: $field не заполнено");
        }
        return $data[$field];
    }

    public function path(): string
    {
        if(!array_key_exists('REQUEST_URI', $this->server)){
            throw new HttpException("Не удается получить запрос");
        }
        $components = parse_url($this->server['REQUEST_URI']);
        if(!is_array($components) || !array_key_exists('path',$components)){
            throw new HttpException("Не удается получить запрос");
        }
        return $components['path'];

    }
    public function query(string $param): string
    {
        if(!array_key_exists($param, $this->get)){
            throw new HttpException("Параметр: $param отсутсвует");
        }

        $value = trim($this->get[$param]);

        if(empty($value)){
            throw new HttpException("Параметр: $param не заполнен ");
        }
        return $value;
    }
    public  function header(string $header): string
    {
        $headerName = mb_strtoupper("http_".str_replace('-','_',$header));

        if(!array_key_exists($headerName, $this->server)){
            throw new HttpException("Отсутсвует заголовок: $header");
        }

        $value = trim($this->server[$headerName]);

        if(empty($value)){
            throw new HttpException("Пустой заголовок: $header ");
        }
        return $value;
    }
}
