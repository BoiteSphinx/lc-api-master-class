<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Filters\V1\TicketFilter;
use App\Http\Requests\Api\V1\ReplaceTicketRequest;
use App\Http\Requests\Api\V1\StoreTicketRequest;
use App\Http\Requests\Api\V1\UpdateTicketRequest;
use App\Http\Resources\V1\TicketResource;
use App\Models\Ticket;
use App\Policies\V1\TicketPolicy;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class AuthorTicketController extends ApiController
{
    protected $policyClass = TicketPolicy::class;

    /**
     * Display a listing of the resource.
     */
    public function index(int $author_id, TicketFilter $filters): JsonResource
    {
        return TicketResource::collection(
            Ticket::query()
                ->where('user_id', $author_id)
                ->filter($filters)
                ->paginate()
        );
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreTicketRequest $request, int $author_id)
    {
        if ($this->isAble('store', Ticket::class)) {
            return new TicketResource(Ticket::create($request->mappedAttributes([
                'author' => 'user_id',
            ])));
        }

        return $this->error('You are not authorized to create that resource', 401);
    }

    public function replace(ReplaceTicketRequest $request, int $author_id, int $ticket_id)
    {
        // PUT
        try {
            $ticket = Ticket::query()->where('id', $ticket_id)
                ->where('user_id', $author_id)
                ->firstOrFail();

            if ($this->isAble('replace', $ticket)) {
                $ticket->update($request->mappedAttributes());

                return new TicketResource($ticket);
            }

            return $this->error('You are not authorized to update that resource', 401);
        } catch (ModelNotFoundException $exception) {
            return $this->error('Ticket cannot be found.', 404);
        }
    }

    public function update(UpdateTicketRequest $request, int $author_id, int $ticket_id)
    {
        // PATCH
        try {
            $ticket = Ticket::query()->where('id', $ticket_id)
                ->where('user_id', $author_id)
                ->firstOrFail();

            if ($this->isAble('update', $ticket)) {
                $ticket->update($request->mappedAttributes());

                return new TicketResource($ticket);
            }

            return $this->error('You are not authorized to update that resource', 401);
        } catch (ModelNotFoundException $exception) {
            return $this->error('Ticket cannot be found.', 404);
        }
    }

     /**
     * Remove the specified resource from storage.
     */
    public function destroy(int $author_id, int $ticket_id): JsonResponse
    {
        try {
            $ticket = Ticket::query()->where('id', $ticket_id)
                ->where('user_id', $author_id)
                ->firstOrFail();

            if ($this->isAble('delete', $ticket)) {
                $ticket->delete();

                return $this->ok('Ticket successfully deleted');
            }

            return $this->error('You are not authorized to delete that resource', 401);
        } catch (ModelNotFoundException $exception) {
            return $this->error('Ticket cannot be found', 404);
        }
    }
}
