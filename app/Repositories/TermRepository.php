<?php

namespace App\Repositories;

use App\Repositories\BaseRepository;

use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;
use Illuminate\Auth\Events\Registered;
use Illuminate\Support\Arr;

use App\Models\Term;
use App\Models\Internship;

class TermRepository extends BaseRepository
{
    protected $model;

    public function __construct(Term $model) {
        $this->model = $model;
    }

    public function index($request)
    {
        $terms = $this->model;

        if ($request->keyword) {
            $keyword = $request->keyword;
            $terms->where(function ($q) use ($keyword) {
                $q->where('name', 'like', '%'.$keyword.'%');
            });
        }

        return $terms->orderBy('id', 'DESC')->get();
    }

    public function store($request)
    {
        $term = $this->model->create([
            'name' => $request->name, 
        ]);

        return $term;
    }

    public function show($id, $request)
    {
        $user = $request->user();
        $term = $this->model->find($id);
        $data['term'] = $term;

        if ($user->role_id == 3) {
            $data['Internship'] = Internship::where('term_id', $term->id)->first();
        }

        return $data;
    }

    public function destroy($id)
    {
        $term = $this->model->find($id);
        if (is_null($term)) {
            return ['error' => 'Term not found.'];
        } else {
            $term->delete();
            return ['message' => 'Term deleted successfully'];
        }
    }
}