<?php

namespace App\Repositories;

use App\Repositories\BaseRepository;

use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;
use Illuminate\Auth\Events\Registered;
use Illuminate\Support\Arr;

use App\Notifications\RescheduleRequested;

use App\Models\RescheduleRequest;

class RescheduleRepository extends BaseRepository
{
    protected $model;

    public function __construct(RescheduleRequest $model) {
        $this->model = $model;
    }

    public function index($request)
    {
        if ($request->internship_id) {
            return $this->model->where('internship_id', $request->internship_id)->get();
        } else {
            return [];
        }
    }

    public function store($request)
    {
        $reschedule = $this->model->create($request->all());

        $internship = $reschedule->internship;
        $internship->load(['student']);

        $internship->supervisor->notify(new RescheduleRequested($internship));

        return $reschedule->load(['requestor']);
    }

    public function show($id)
    {
        // 
    }

    public function update($request, $id)
    {
        // 
    }

    public function destroy($id)
    {
        // 
    }
}