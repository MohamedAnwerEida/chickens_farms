<?php

namespace App\Http\Controllers\dashboard;

use App\User;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Hash;
use Intervention\Image\Facades\Image;

class UserController extends Controller
{
    /**
     * Create a new controller instance.
     *  $this->middleware('auth:api');

     * @return void
     */
    public function __construct()
    {
        $this->middleware('admin');
    }
     /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
        return User::latest()->paginate(12);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
        
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //
        $this->validate($request, [
            'name' => ['required', 'string', 'max:255'],
            'phone' => ['required', 'regex:/(01)[0-9]{9}/'],
            'type' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users'],
            'password' => ['required', 'string', 'min:8'],
        ]);

        return User::create([
            'name' => $request['name'],
            'phone' => $request['phone'],
            'email' => $request['email'],
            'type' => $request['type'],
            'password' => Hash::make($request['password'])
        ]);
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\User  $User
     * @return \Illuminate\Http\Response
     */
    public function show(User $User)
    {
        //
    }

    public function profile(Request $request)
    {
        //
        return $request->user();
    }

    public function search(Request $request)
    {
        if ($search = $request->q) {
            $users = User::where('name', 'LIKE', "%$search%")
                ->orWhere('email', 'LIKE', "%$search%")
                ->orWhere('type', 'LIKE', "%$search%")
                ->paginate(12);
        } else {
            $users = User::latest()->paginate(12);
        }
        return $users;
    }

    public function updateProfile(Request $request)
    {
        $User = $request->user();
        $this->validate($request, [
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:191|unique:users,email,'.$User->id,
            'phone' => 'required|regex:/(01)[0-9]{9}/',
            //'type' => 'required|string|max:255',
            'password' => 'sometimes|min:8',
            //'photo' => 'image|mimes:jpeg,png,jpg,gif,svg|max:2048'
        ]);
        $data = [
            'name' => $request['name'],
            'phone' => $request['phone'],
            'email' => $request['email'],
            'type' => $request['type']
        ];
        if ($request['password'] != null) {
            $data['password'] =  Hash::make($request['password']);
        }
        if ($request['photo'] != null) {
            $currentPhoto = $User->photo;
            if ($request->photo != $currentPhoto) {
                $name = time().'.' . explode('/', explode(':', substr($request->photo, 0, strpos($request->photo, ';')))[1])[1];
                Image::make($request->photo)->save(public_path('img/profile/').$name);
                $data['photo'] = $name;
                $UserPhoto = public_path('img/profile/').$currentPhoto;
                if (file_exists($UserPhoto)) {
                    @unlink($UserPhoto);
                }
            }
        }
        $User->update($data);
        return ['message' => 'Updated the user info'];
    }
    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\User  $User
     * @return \Illuminate\Http\Response
     */
    public function edit(User $User)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\User  $User
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, User $User)
    {
        //
        $this->validate($request, [
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:191|unique:users,email,'.$User->id,
            'phone' => 'required|regex:/(01)[0-9]{9}/',
            'type' => 'required|string|max:255',
            'password' => 'sometimes|min:8'
        ]);
        $data = [
            'name' => $request['name'],
            'phone' => $request['phone'],
            'email' => $request['email'],
            'type' => $request['type']
        ];
        if ($request['password'] != null) {
            $data['password'] =  Hash::make($request['password']);
        }
        $User->update($data);
        return ['message' => 'Updated the user info'];

    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\User  $User
     * @return \Illuminate\Http\Response
     */
    public function destroy(User $User)
    {
        //
        $User->delete();
        return ['message' => 'User Deleted'];
    }
}
