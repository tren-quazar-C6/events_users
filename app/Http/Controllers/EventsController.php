<?php

namespace App\Http\Controllers;

use App\Services\EventService;

class EventsController extends Controller
{
    public function __construct(private EventService $events)
    {
    }

    public function getAllEvents()
    {
        return response()->json($this->events->all());
    }
}
