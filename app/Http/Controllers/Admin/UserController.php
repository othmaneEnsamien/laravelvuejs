<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;

class UserController extends Controller
{

    public function index()
    {
        $users = User::latest()->paginate(2);

        return response()->json($users); // Utilisez response()->json pour retourner des données JSON
    }

    public function store()
    {
        request()->validate([
            'name' => 'required',
            'email' => 'required|unique:users,email',
            'password' => 'required|min:8',
        ]);

        return User::create([
            'name' => request('name'),
            'email' => request('email'),
            'password' => bcrypt(request('password')),
        ]);
    }

    public function update(User $user)
    {
        request()->validate([
            'name' => 'required',
            'email' => 'required|unique:users,email,' . $user->id,
            'password' => 'sometimes|min:8',
        ]);

        $user->update([
            'name' => request('name'),
            'email' => request('email'),
            'password' => request('password') ? bcrypt(request('password')) : $user->password,
        ]);

        return $user;
    }

    public function destroy(User $user)
    {
        $user->delete();
        return response()->json(['message' => 'User deleted']);
    }


    public function changeRole(User $user)
    {


        $user->update([
            'role' => request('role'),
        ]);


        return response()->json(['success' => true]);
    }

    public function search()
    {
        $searchQuery = request('query');

        $users =
            User::where('name', 'LIKE', "%{$searchQuery}%")
            ->orWhere('email', 'LIKE', "%{$searchQuery}%")
            ->paginate(2);

        return response()->json($users);
    }

    public function bulkDelete()
    {
        $ids = request('ids');
        User::whereIn('id', $ids)->delete();
        return response()->json(['message' => 'Users deleted']);
    }
}
