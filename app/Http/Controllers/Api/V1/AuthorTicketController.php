<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Filters\V1\TicketFilter;
use App\Http\Requests\Api\V1\StoreTicketRequest;
use App\Http\Resources\V1\TicketResource;
use App\Models\Ticket;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class AuthorTicketController extends Controller
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
}
