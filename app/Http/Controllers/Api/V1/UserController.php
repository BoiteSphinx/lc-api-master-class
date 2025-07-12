<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Filters\V1\AuthorFilter;
use App\Http\Requests\Api\V1\ReplaceUserRequest;
use App\Models\User;
use App\Http\Requests\Api\V1\StoreUserRequest;
use App\Http\Requests\Api\V1\UpdateUserRequest;
use App\Http\Resources\V1\UserResource;
use App\Policies\V1\UserPolicy;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Resources\Json\JsonResource;

class UserController extends ApiController
{
    protected string $policyClass = UserPolicy::class;

    /**
     * Display a listing of the resource.
     */
    public function index(AuthorFilter $filters): JsonResource
    {
        return UserResource::collection(
            User::query()
                ->filter($filters)
                ->paginate()
        );
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreUserRequest $request)
    {
        try {
            $this->isAble('store', User::class);

            return new UserResource(User::create($request->mappedAttributes()));
        } catch (AuthorizationException $exception) {
            return $this->error('You are not authorized to create that resource', 403);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(User $user): JsonResource
    {
        if ($this->include('tickets')) {
            return new UserResource($user->load('tickets'));
        }

        return new UserResource($user);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateUserRequest $request, int $user_id)
    {
        // PATCH
        try {
            $user = User::findOrFail($user_id);

            $this->isAble('update', $user);

            $user->update($request->mappedAttributes());

            return new UserResource($user);
        } catch (ModelNotFoundException $exception) {
            return $this->error('User cannot be found.', 404);
        } catch (AuthorizationException $exception) {
            return $this->error('You are not authorized to update that resource', 403);
        }
    }

    public function replace(ReplaceUserRequest $request, int $user_id)
    {
        // PUT
        try {
            $user = User::findOrFail($user_id);

            $this->isAble('replace', $user);

            $user->update($request->mappedAttributes());

            return new UserResource($user);
        } catch (ModelNotFoundException $exception) {
            return $this->error('User cannot be found.', 404);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(int $user_id)
    {
        try {
            $user = User::query()->findOrFail($user_id);

            $this->isAble('delete', $user);

            $user->delete();

            return $this->ok('User successfully deleted');
        } catch (ModelNotFoundException $exception) {
            return $this->error('User cannot be found', 404);
        }
    }
}
