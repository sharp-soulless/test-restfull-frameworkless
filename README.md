# Instruction

## How to run the program

### Generate autoload files

Run the following command in the root directory of the project for generate autoload files
```bash
composer dump-autoload
```

### Run server

Run the following command in the root directory of the project
```bash
php -S localhost:8000
```

For testing more convenient to use Postman or similar software.

## Basic authentication

To use the API you need to provide basic authentication.
For testing purposes you can use the any username and password: `password`.

For testing you can use the following header for pass successfully authentication: 
`Authorization: Basic dXNlcm5hbWU6cGFzc3dvcmQ=`

## Endpoints

### GET /posts - Get all posts
### GET /posts/{id} - Get post by id
### POST /posts - Create new post
### PUT /posts/{id} - Update post by id
### DELETE /posts/{id} - Delete post by id

## Body for POST and PUT requests

```json
{
    "title": "Post title",
    "body": "Post body"
}
```