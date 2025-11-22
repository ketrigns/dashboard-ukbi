<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class UserController extends Controller
{
    public function index()
    {
        $users = User::all();
        return view('pages.admin.manajemen-pengguna.index', compact('users'));
    }

    // TAMPILKAN FORM TAMBAH
    public function create()
    {
        return view('pages.admin.manajemen-pengguna.create');
    }

    // SIMPAN USER BARU
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name'        => 'required|string|max:255',
            'email'       => 'required|email|unique:users',
            'nip'         => 'required|string',
            'password'    => 'required|string|min:6',
            'role'        => 'required|in:admin,petugas',
            'profile_pic' => 'nullable|image'
        ]);

        $profilePic = null;

        // Jika ada file foto
        if ($request->hasFile('profile_pic')) {

            // Generate nama file baru
            $filename = time() . '_' . $request->file('profile_pic')->getClientOriginalName();

            // Simpan ke storage/app/public/profile_pics/
            $path = $request->file('profile_pic')->storeAs(
                'profile_pics',
                $filename,
                'public'
            );

            // $path = "profile_pics/nama_file.png"
            $profilePic = $path;
        }

        User::create([
            'name'        => $validated['name'],
            'email'       => $validated['email'],
            'nip'         => $validated['nip'],
            'password'    => bcrypt($validated['password']),
            'role'        => $validated['role'],
            'profile_pic' => $profilePic,
        ]);

        return redirect()->route('users.index')
            ->with('success', 'User berhasil ditambahkan');
    }



    // TAMPILKAN FORM EDIT
    public function edit($id)
    {
        $user = User::findOrFail($id);
        return view('pages.admin.manajemen-pengguna.edit', compact('user'));
    }

    // UPDATE USER
    public function update(Request $request, User $user)
    {
        $validated = $request->validate([
            'name'        => 'required|string|max:255',
            'email'       => 'required|email|unique:users,email,' . $user->id,
            'nip'         => 'required|string',
            'password'    => 'nullable|string|min:5',
            'role'        => 'required|in:admin,petugas',
            'profile_pic' => 'nullable|image|mimes:jpg,jpeg,png|max:4096'
        ]);

        $profilePic = $user->profile_pic; // path lama (mis. profile_pics/xxx.png) atau null

        if ($request->hasFile('profile_pic')) {
            // Hapus file lama jika ada
            if ($user->profile_pic && Storage::disk('public')->exists($user->profile_pic)) {
                Storage::disk('public')->delete($user->profile_pic);
            }

            // Simpan file baru ke disk 'public' di folder profile_pics
            $filename = time() . '_' . $request->file('profile_pic')->getClientOriginalName();
            $path = $request->file('profile_pic')->storeAs('profile_pics', $filename, 'public');

            // storeAs mengembalikan 'profile_pics/filename.ext'
            $profilePic = $path;
        }

        $user->update([
            'name'        => $validated['name'],
            'email'       => $validated['email'],
            'nip'         => $validated['nip'],
            'password'    => $validated['password'] ? bcrypt($validated['password']) : $user->password,
            'role'        => $validated['role'],
            'profile_pic' => $profilePic,
        ]);

        return redirect()->route('users.index')->with('success', 'User berhasil diperbarui');
    }

    public function destroy(User $user)
    {
        // Hapus file foto jika ada
        if ($user->profile_pic && Storage::disk('public')->exists($user->profile_pic)) {
            Storage::disk('public')->delete($user->profile_pic);
        }

        $user->delete();

        return redirect()->route('admin.users.index')->with('success', 'User berhasil dihapus');
    }

    public function profile()
    {
        return view('pages.admin.profile.index', [
            'user' => auth()->user()
        ]);
    }

    public function updateProfile(Request $request)
    {
        $user = auth()->user();

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email,' . $user->id,
            'nip' => 'required|string',
            'password' => 'nullable|string|min:6|confirmed',
            'profile_pic' => 'nullable|image|mimes:jpg,jpeg,png'
        ]);

        $profilePic = $user->profile_pic;

        if ($request->hasFile('profile_pic')) {

            // hapus file lama
            if ($profilePic && Storage::disk('public')->exists($profilePic)) {
                Storage::disk('public')->delete($profilePic);
            }

            // upload baru dan simpan folder + nama file
            $profilePic = $request->file('profile_pic')->store('profile_pics', 'public');
        }

        $user->update([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'nip' => $validated['nip'],
            'password' => $validated['password'] ? bcrypt($validated['password']) : $user->password,
            'profile_pic' => $profilePic
        ]);

        return back()->with('success', 'Profil berhasil diperbarui!');
    }
}
