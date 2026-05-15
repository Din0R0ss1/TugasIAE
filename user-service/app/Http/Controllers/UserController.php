<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use App\Models\LoanHistory;

class UserController extends Controller
{
    public function index()
    {
        return response()->json(User::all());
    }

    public function show($id)
    {
        $user = User::find($id);
        if (!$user) {
            return response()->json(['message' => 'User tidak ditemukan'], 404);
        }
        return response()->json($user);
    }

    public function store(Request $request)
    {
        $request->validate([
            'name'  => 'required',
            'email' => 'required|email|unique:users,email|regex:/@gmail\.com$/'
        ]);

        $user = User::create($request->all());
        return response()->json($user, 201);
    }

    public function update(Request $request, $id)
    {
        $user = User::find($id);
        if (!$user) {
            return response()->json(['message' => 'User tidak ditemukan'], 404);
        }

        $request->validate([
            'name'  => 'required',
            'email' => "required|email|unique:users,email,{$id}|regex:/@gmail\.com$/"
        ]);

        $user->update($request->only('name', 'email'));
        return response()->json([
            'message' => 'User berhasil diupdate',
            'data'    => $user
        ]);
    }

    // ✅ GET riwayat peminjaman per user
    public function history($id)
    {
        $user = User::find($id);
        if (!$user) {
            return response()->json(['message' => 'User tidak ditemukan'], 404);
        }

        $histories = LoanHistory::where('user_id', $id)
            ->orderBy('created_at', 'desc')
            ->get();

        return response()->json([
            'user'    => $user,
            'history' => $histories
        ]);
    }
}