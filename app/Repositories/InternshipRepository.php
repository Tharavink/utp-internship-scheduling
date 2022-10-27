<?php

namespace App\Repositories;

use App\Repositories\BaseRepository;

use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;
use Illuminate\Auth\Events\Registered;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Notification;

use App\Notifications\NotifySupervisor;
use App\Notifications\InternshipAdded;
use App\Notifications\Scheduled;

use App\Models\Internship;

class InternshipRepository extends BaseRepository
{
    protected $model;

    public function __construct(Internship $model) {
        $this->model = $model;
    }

    public function index($request)
    {
        $user = $request->user();
        $internships = $this->model->with('term', 'supervisor');

        if ($request->keyword) {
            $keyword = $request->keyword;
            $internships->where(function ($q) use ($keyword) {
                $q->where('title', 'like', '%'.$keyword.'%')->orWhere('description', 'like', '%'.$keyword.'%');
            });
        }

        if ($user->role_id == 3) {
            $internships->where('student_id', $user->id);
        } elseif ($user->role_id == 2) {
            $internships->where('supervisor_id', $user->id);
        }

        if ($request->intern_term) {
            $internships->where('intern_term', $request->intern_term);
        }

        if ($request->has('is_completed')) {
            $internships->where('is_completed', $request->is_completed);
        }

        return $internships->orderBy('id', 'DESC')->get();
    }

    public function store($request)
    {
        if (!$request->id) {
            if ($this->model->where('term_id', $request->term_id)->where('student_id', $request->user()->id)->exists()) {
                return ['error' => 'You have already enroled for this term. Reload to see already submitted project details.'];
            }
        }

        $data = $request->all();
        $data['student_id'] = $request->user()->id;
        if ($request->id) {
            $internship = $this->model->find($request->id);
            $internship->update($data);
        } else {
            $internship = $this->model->create($data);
            if ($internship->host_email) {
                Notification::route('mail', $internship->host_email)->notify(new NotifySupervisor($internship));
            }
        }

        return ['message' => 'Internship project details updated successfully.', 'internship' => $internship];
    }

    public function show($id)
    {
        return $this->model->with('term', 'supervisor', 'assigned_by', 'requests.requestor')->find($id);
    }

    public function update($request, $id)
    {
        $initial = $this->model->find($id);
        
        $internship = $this->model->find($id);
        $internship->update($request->all());
        $internship = $this->show($id);

        if ($internship->supervisor_id && $initial->supervisor_id != $internship->supervisor_id) {
            $internship->supervisor->notify(new InternshipAdded($internship));
        }

        if ($internship->host_email && $initial->host_email != $internship->host_email) {
            Notification::route('mail', $internship->host_email)->notify(new NotifySupervisor($internship));
        }

        if ($internship->scheduled && $initial->scheduled != $internship->scheduled) {
            $internship->student->notify(new Scheduled($internship));
            Notification::route('mail', $internship->host_email)->notify(new Scheduled($internship));
        }

        return $internship;
    }

    public function destroy($id)
    {
        $internship = $this->model->find($id);
        if (is_null($internship)) {
            return ['error' => 'Internship record does not exist. Please reload.'];
        } else {
            $internship->delete();
            return ['message' => 'Internship record deleted successfully'];
        }
    }
}