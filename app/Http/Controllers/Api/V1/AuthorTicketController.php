<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Filters\V1\TicketFilter;
use App\Http\Requests\Api\V1\ReplaceTicketRequest;
use App\Http\Requests\Api\V1\StoreTicketRequest;
use App\Http\Resources\V1\TicketResource;
use App\Models\Ticket;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class AuthorTicketController extends ApiController
{
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
    public function store(int $author_id, StoreTicketRequest $request): JsonResource
    {
        $model = [
            'title' => $request->input('data.attributes.title'),
            'description' => $request->input('data.attributes.description'),
            'status' => $request->input('data.attributes.status'),
            'user_id' => $author_id
        ];

        return new TicketResource(Ticket::create($model));
    }

    public function replace(ReplaceTicketRequest $request, int $author_id, int $ticket_id)
    {
        // PUT
        try {
            $ticket = Ticket::findOrFail($ticket_id);

            if ($ticket->user_id == $author_id) {

                $model = [
                    'title' => $request->input('data.attributes.title'),
                    'description' => $request->input('data.attributes.description'),
                    'status' => $request->input('data.attributes.status'),
                    'user_id' => $request->input('data.relationships.author.data.id')
                ];

                $ticket->update($model);
                return new TicketResource($ticket);
            }
            // TODO: ticket doesn't belong to user

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
            $ticket = Ticket::findOrFail($ticket_id);

            if ($ticket->user_id == $author_id) {
                $ticket->delete();

                return $this->ok('Ticket successfully deleted');
            }

            return $this->error('Ticket cannot be found', 404);

        } catch (ModelNotFoundException $exception) {
            return $this->error('Ticket cannot be found', 404);
        }
    }
}
