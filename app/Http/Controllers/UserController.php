<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class UserController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
{
    $search = request('search');

    $users = User::when($search, function ($query) use ($search) {
        $query->where('name', 'like', "%{$search}%")
              ->orWhere('email', 'like', "%{$search}%");
    })->get();

    return view('users.index', compact('users'));
}

    /**
     * Show the form for creating a new resource.
     */
    public function create()
{
    return view('users.create');
}

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
{
    $request->validate([
        'name' => 'required',
        'email' => 'required|email|unique:users',
        'password' => 'required|min:6',
    ]);

    User::create([
    'name' => $request->name,
    'email' => $request->email,
    'password' => Hash::make($request->password),
    'role' => $request->role,
]);

    return redirect('/users');
}

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
{
    $user = User::findOrFail($id);

    return view('users.edit', compact('user'));
}

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
{
    $user = User::findOrFail($id);

    $request->validate([
        'name' => 'required',
        'email' => 'required|email|unique:users,email,' . $user->id,
    ]);

    $user->name = $request->name;
    $user->email = $request->email;

    if ($request->password) {
        $user->password = Hash::make($request->password);
    }

    $user->save();

    return redirect('/users');
}

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
{
    $user = User::findOrFail($id);

    $user->delete();

    return redirect('/users');
}
}
