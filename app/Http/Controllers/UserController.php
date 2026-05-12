<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Kelurahan;
use App\Models\RtRw;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class UserController extends Controller
{
    public function index()
    {
        $users = User::with(['kelurahan', 'rtRw'])->get();
        $kelurahans = Kelurahan::all();
        $rtRws = RtRw::all();
        return view('users.index', compact('users', 'kelurahans', 'rtRws'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users',
            'password' => 'required|min:6',
            'role' => 'required|in:admin,supervisor,petugas',
            'kelurahan_id' => 'nullable|exists:kelurahans,id',
            'rt_rw_id' => 'nullable|exists:rt_rws,id',
        ]);

        User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'role' => $request->role,
            'kelurahan_id' => $request->kelurahan_id,
            'rt_rw_id' => $request->rt_rw_id,
        ]);

        return redirect()->route('user.index')->with('status', 'Pengguna berhasil ditambahkan!');
    }

    public function edit($id)
    {
        $user = User::with(['kelurahan', 'rtRw'])->findOrFail($id);
        return response()->json($user);
    }

    public function update(Request $request, $id)
    {
        $user = User::findOrFail($id);

        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email,' . $id,
            'role' => 'required|in:admin,supervisor,petugas',
            'kelurahan_id' => 'nullable|exists:kelurahans,id',
            'rt_rw_id' => 'nullable|exists:rt_rws,id',
        ]);

        $data = [
            'name' => $request->name,
            'email' => $request->email,
            'role' => $request->role,
            'kelurahan_id' => $request->kelurahan_id,
            'rt_rw_id' => $request->rt_rw_id,
        ];

        if ($request->filled('password')) {
            $data['password'] = Hash::make($request->password);
        }

        $user->update($data);

        return redirect()->route('user.index')->with('status', 'Pengguna berhasil diupdate!');
    }

    public function destroy($id)
    {
        $user = User::withCount('families')->findOrFail($id);

        if ($user->families_count > 0) {
            return redirect()->route('user.index')->with('error', 'Tidak dapat menghapus pengguna! Pengguna ini memiliki data tag keluarga yang terkait.');
        }

        $user->delete();
        return redirect()->route('user.index')->with('status', 'Pengguna berhasil dihapus!');
    }
}
