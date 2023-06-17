<?php

namespace App\Repositories;

use App\Models\Post;

class PostRepository
{
    /**
     * @param int $page
     * @param int $perPage
     *
     * @return array
     */
    public function list(int $page = 1, int $perPage = 10): array
    {
        // stub getting data
        $posts = [];
        foreach (range(1, $perPage) as $i) {
            $posts[] = new Post();
        }

        return $posts;
    }

    /**
     * @param int $id
     *
     * @return Post|null
     */
    public function find(int $id): ?Post
    {
        // stub getting data
        $post = new Post();
        $post->id = $id;
        return $post;
    }

    /**
     * @param array $data
     *
     * @return Post
     */
    public function create(array $data): Post
    {
        // stub creating data
        $post = new Post();
        $post->title = $data['title'] ?? $post->title;
        $post->body = $data['body'] ?? $post->body;
        return $post;
    }

    /**
     * @param int $id
     * @param array $data
     *
     * @return Post
     */
    public function update(int $id, array $data): Post
    {
        // stub updating data
        $post = new Post();
        $post->id = $id;
        $post->title = $data['title'] ?? $post->title;
        $post->body = $data['body'] ?? $post->body;
        return $post;
    }

    /**
     * @param int $id
     *
     * @return bool
     */
    public function delete(int $id): bool
    {
        // stub deleting data
        return true;
    }
}