<?php

namespace App\Models;

class Post
{
    public $id;
    public $title;
    public $body;
    public $createdAt;
    public $updatedAt;

    public function __construct()
    {
        // stub data
        $this->id = rand(1, 100);
        $this->title = str_shuffle(implode(range('a', 'z')));
        $this->body = str_shuffle(implode(range('a', 'z')));
        $this->createdAt = 1687026299;
        $this->updatedAt = 1687026299;
    }

    public function toArray(): array
    {
        return [
            'id' => $this->id,
            'title' => $this->title,
            'body' => $this->body,
            'createdAt' => $this->createdAt,
            'updatedAt' => $this->updatedAt,
        ];
    }
}