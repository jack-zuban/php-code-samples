<?php

namespace App\Http\Controllers\Api;

use App\Event;
use App\EventRepository;
use App\Http\Requests\Api\IndexEventRequest;
use App\Http\Requests\Api\StoreUpdateEventRequest;
use App\Transformers\EventTransformer;
use App\Http\Controllers\Controller;

class EventsController extends Controller
{
    /**
     * @var \App\Transformers\EventTransformer
     */
    protected $transformer;

    /**
     * @var \App\EventRepository
     */
    protected $repository;

    /**
     * @param \App\Transformers\EventTransformer $transformer
     * @param \App\EventRepository $repository
     */
    public function __construct(
        EventTransformer $transformer,
        EventRepository $repository
    ) {
        $this->transformer = $transformer;

        $this->repository = $repository;
    }

    /**
     * Return all events using filters from query parameters if any.
     *
     * @param \App\Http\Requests\Api\IndexEventRequest $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function index(IndexEventRequest $request)
    {
        $filters = $request->getFiltersFromParameters();

        $allEvents = $this->repository
            ->getAllEventsUsingFilters($filters)->map(function (Event $event) {
                return $this->transformer->transform($event);
            });

        return response()->json($allEvents);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param \App\Http\Requests\Api\StoreUpdateEventRequest $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(StoreUpdateEventRequest $request)
    {
        $event = $this->repository->createNewEventFromArray($request->all());

        return response()->json([
            'status' => 'Event has been created.',
            'created_event_id' => $event->getId(),
        ], 201);
    }

    /**
     * Display the specified resource.
     *
     * @param int $id
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function show($id)
    {
        $event = $this->repository->getEventById($id);

        return response()->json($this->transformer->transform($event));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param \App\Http\Requests\Api\StoreUpdateEventRequest $request
     * @param $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function update(StoreUpdateEventRequest $request, $id)
    {
        $this->repository->updateEventFromArrayById($id, $request->all());

        return response()->json(['status' => 'Event has been updated.']);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param int $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $this->repository->deleteEventById($id);

        return response()->json(['status' => 'Event has been deleted.']);
    }
}
