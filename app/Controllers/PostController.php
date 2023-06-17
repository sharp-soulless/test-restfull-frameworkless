<?php

namespace App\Controllers;

use App\Exceptions\UnauthorizedException;
use App\Facades\Http\Request;
use App\Facades\Http\Response;
use App\Repositories\PostRepository;

class PostController extends Controller
{
    /** @var PostRepository */
    protected $repository;

    /**
     * @param PostRepository $repository
     */
    public function __construct(PostRepository $repository)
    {
        $this->repository = $repository;
    }

    /**
     * @return Controller
     *
     * @throws UnauthorizedException
     */
    public function authorize(): Controller
    {
        $authData = $this->request->getBasicAuth();
        if (
            ! isset($authData['user'], $authData['password'])
            || (
                isset($authData['user'], $authData['password'])
                && $authData['user'] !== $authData['password']
            )
        ) {
            throw new UnauthorizedException();
        }

        return parent::authorize();
    }

    /**
     * @param Request $request
     *
     * @return Response
     */
    public function index(Request $request): Response
    {
        return new Response(
            $this->repository->list($request->get('page', 1), $request->get('perPage', 10)),
            Response::HTTP_OK,
            [Response::CONTENT_TYPE_JSON]
        );
    }

    /**
     * @param Request $request
     * @param int $id
     *
     * @return Response
     */
    public function show(Request $request, int $id): Response
    {
        return new Response(
            $this->repository->find($id)->toArray(),
            Response::HTTP_OK,
            [Response::CONTENT_TYPE_JSON]
        );
    }

    /**
     * @param Request $request
     *
     * @return Response
     */
    public function store(Request $request): Response
    {
        return new Response(
            $this->repository->create($request->all())->toArray(),
            Response::HTTP_CREATED,
            [Response::CONTENT_TYPE_JSON]
        );
    }

    /**
     * @param Request $request
     * @param int $id
     *
     * @return Response
     */
    public function update(Request $request, int $id): Response
    {
        return new Response(
            $this->repository->update($id, $request->all())->toArray(),
            Response::HTTP_ACCEPTED,
            [Response::CONTENT_TYPE_JSON]
        );
    }

    /**
     * @param int $id
     *
     * @return Response
     */
    public function delete(int $id): Response
    {
        if ($this->repository->delete($id)) {
            return new Response(
                ['message' => 'Post deleted'],
                Response::HTTP_ACCEPTED,
                [Response::CONTENT_TYPE_JSON]
            );
        } else {
            return new Response(
                ['message' => 'Post not found'],
                Response::HTTP_NOT_FOUND,
                [Response::CONTENT_TYPE_JSON]
            );
        }
    }
}