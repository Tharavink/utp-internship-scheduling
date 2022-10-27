<?php

namespace App\Repositories;

use App\Repositories\BaseRepository;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;
use Illuminate\Auth\Events\Registered;
use Illuminate\Support\Arr;

use App\Notifications\InviteSupervisor;

use App\Models\User;

class UserRepository extends BaseRepository
{
    protected $model;

    public function __construct(User $model) {
        $this->model = $model;
    }

    public function index($request)
    {
        $users = $this->model->with('role')->where('role_id', 2);

        if ($request->keyword) {
            $keyword = $request->keyword;
            $users->where(function ($q) use ($keyword) {
                $q->where('name', 'like', '%'.$keyword.'%')->orWhere('email', 'like', '%'.$keyword.'%');
            });
        }

        if ($request->role_id) {
            $users->where('role_id', $request->role_id);
        }

        return $users->orderBy('id', 'DESC')->get();
    }

    public function store($request)
    {
        $user = $this->model->where('email', $request->email)->first();
        if (!is_null($user)) {
            return ['error' => 'Email have already been taken by another user.'];
        }

        $chars = "ABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789abcdefghijklmnopqrstuvwxyz@#$&*"; 
        $size = strlen( $chars );  
        $password = '';
        for( $i = 0; $i < 8; $i++ ) {  
            $password .= $chars[ rand( 0, $size - 1 ) ];  
        }

        $supervisor = $this->model->create([
            'name' => $request->name, 
            'email' => $request->email, 
            'password' => Hash::make($password), 
            'role_id' => 2
        ]);

        $supervisor->notify(new InviteSupervisor($password));
        $supervisor->sendEmailVerificationNotification();

        return $supervisor;
    }

    public function destroy($id)
    {
        $user = $this->model->find($id);
        if (is_null($user)) {
            return ['error' => 'User not found.'];
        } else {
            $user->delete();
            return ['message' => 'User deleted successfully'];
        }
    }
}