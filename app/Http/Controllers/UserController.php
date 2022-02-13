<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Tymon\JWTAuth\JWTAuth;
use Illuminate\Support\Str;

class UserController extends Controller
{
    public function __construct()
    {
        $this->middleware('admin', ['except' => ['profile', 'updateprofile', 'updatefoto', 'changepassword']]);
    }

    // admin

    public function index()
    {
        $all = User::all();
        $user = [];
        foreach ($all as $all_fetch) {
            if ($all_fetch->foto != null) {
                $all_fetch->foto = 'http://locahost:8000/'.$all_fetch->foto;
            }
            array_push($user, $all_fetch);
        }
        return response()->json([
            'status' => true,
            'message' => 'data user',
            'data' => $user,
            'error' => null,
        ], 200);
    }

    public function detail($id)
    {
        $user = User::find($id);
        if (!$user) {
            return response()->json([
                'status' => false,
                'message' => 'user tidak ditemukan',
                'data' => null,
                'error' => null,
            ], 200);
        }

        if ($user->foto != null) {
            $user->foto = "http://localhost:8000/" . $user->foto;
        }

        return response()->json([
            'status' => true,
            'message' => 'data user',
            'data' => $user,
            'error' => null,
        ], 200);
    }

    public function update(Request $request, $id)
    {
        $user = User::find($id);
        if (!$user) {
            return response()->json([
                'status' => false,
                'message' => 'user tidak ditemukan',
                'data' => null,
                'error' => null,
            ], 200);
        }

        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255',
            'username' => 'required|string|max:255',
            'fakultas' => 'required|string|max:255',
            'jurusan' => 'required|string|max:255',
        ]);

        if($validator->fails()){
            return response()->json([
                'status' => false,
                'message' => 'update gagal',
                'data' => null,
                'error' => $validator->errors()->toJson(),
            ], 200);
        }

        if ($user->username != $request->get('username')) {
            $validator = Validator::make($request->all(), [
                'username' => 'required|string|max:255|unique:users',
            ]);

            if($validator->fails()){
                return response()->json([
                    'status' => false,
                    'message' => 'update gagal',
                    'data' => null,
                    'error' => $validator->errors()->toJson(),
                ], 200);
            }
        }

        if ($user->email != $request->input('email')) {
            $validator = Validator::make($request->all(), [
                'email' => 'required|string|email|max:255|unique:users',
            ]);

            if($validator->fails()){
                return response()->json([
                    'status' => false,
                    'message' => 'update gagal',
                    'data' => null,
                    'error' => $validator->errors()->toJson(),
                ], 200);
            }
        }

        $user->name = $request->input('name');
        $user->email = $request->input('email');
        $user->username = $request->input('username');
        $user->fakultas = $request->input('fakultas');
        $user->jurusan = $request->input('jurusan');
        // $user->role = $request->get('role');

        if ($request->has('alamat')) {
            $user->alamat = $request->input('alamat');
        }

        if ($request->has('foto')) {
            $imageName = time().Str::slug($request->input('username')).'.'.$request->foto->extension();
            $request->foto->move(public_path('images/profile'), $imageName);

            $user->foto = "images/profile/$imageName";
        }

        $user->save();

        return response()->json([
            'status' => true,
            'message' => 'data berhasil diperbarui',
            'data' => null,
            'error' => null,
        ], 200);
    }

    public function destroy($id)
    {
        $user = User::find($id);
        if (!$user) {
            return response()->json([
                'status' => false,
                'message' => 'user tidak ditemukan',
                'data' => null,
                'error' => null,
            ], 200);
        }

        if ($user->role == '1') {
            $getadmin = User::where('role', '1')->get()->count();
            if ($getadmin == 1) {
                return response()->json([
                    'status' => false,
                    'message' => 'gagal hapus karena role admin tidak boleh dihapus secara keseluruhan',
                    'data' => null,
                    'error' => null,
                ], 200);
            }
        }

        $user->delete();

        return response()->json([
            'status' => true,
            'message' => 'data berhasil dihapus',
            'data' => null,
            'error' => null,
        ], 200);
    }

    // user

    public function profile()
    {
        if (!auth()->user()) {
            return response()->json([
                'status' => false,
                'message' => 'token invalid',
                'data' => null,
                'error' => null,
            ], 200);
        }

        $data = User::find(auth()->user()->id);
        if ($data->foto != null) {
            $data->foto = "http://localhost:8000/" . $data->foto;
        }

        return response()->json([
            'status' => true,
            'message' => 'data profile',
            'data' => $data,
            'error' => null,
        ], 200);
    }

    public function updateprofile(Request $request)
    {
        $account = auth()->user();
        $user = User::find($account->id);
        if (!$user) {
            return response()->json([
                'status' => false,
                'message' => 'user tidak ditemukan',
                'data' => null,
                'error' => null,
            ], 200);
        }

        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255',
            'username' => 'required|string|max:255',
            'fakultas' => 'required|string|max:255',
            'jurusan' => 'required|string|max:255',
        ]);

        if($validator->fails()){
            return response()->json([
                'status' => false,
                'message' => 'registrasi gagal',
                'data' => null,
                'error' => $validator->errors()->toJson(),
            ], 200);
        }

        if ($user->username != $request->get('username')) {
            $validator = Validator::make($request->all(), [
                'username' => 'required|string|max:255|unique:users',
            ]);

            if($validator->fails()){
                return response()->json([
                    'status' => false,
                    'message' => 'registrasi gagal',
                    'data' => null,
                    'error' => $validator->errors()->toJson(),
                ], 200);
            }
        }

        if ($user->email != $request->get('email')) {
            $validator = Validator::make($request->all(), [
                'email' => 'required|string|email|max:255|unique:users',
            ]);

            if($validator->fails()){
                return response()->json([
                    'status' => false,
                    'message' => 'update gagal',
                    'data' => null,
                    'error' => $validator->errors()->toJson(),
                ], 200);
            }
        }

        $user->name = $request->get('name');
        $user->email = $request->get('email');
        $user->username = $request->get('username');
        $user->fakultas = $request->get('fakultas');
        $user->jurusan = $request->get('jurusan');

        if ($request->has('alamat')) {
            $user->alamat = $request->get('alamat');
        }

        if ($request->has('foto')) {
            $imageName = time().Str::slug($request->get('username')).'.'.$request->image->extension();
            $request->get('foto')->move(public_path('images/profile'), $imageName);

            $user->foto = "images/profile/$imageName";
        }

        $user->save();

        return response()->json([
            'status' => true,
            'message' => 'data berhasil diperbarui',
            'data' => null,
            'error' => null,
        ], 200);
    }

    public function updatefoto(Request $request)
    {
        $account = auth()->user();
        $user = User::find($account->id);
        if (!$user) {
            return response()->json([
                'status' => false,
                'message' => 'user tidak ditemukan',
                'data' => null,
                'error' => null,
            ], 200);
        }

        $validator = Validator::make($request->all(), [
            'foto' => 'required|image|mimes:jpeg,png,jpg,gif,svg|max:1024',
        ]);

        if($validator->fails()){
            return response()->json([
                'status' => false,
                'message' => 'update gagal',
                'data' => null,
                'error' => $validator->errors()->toJson(),
            ], 200);
        }

        $imageName = time().Str::slug($request->get('username')).'.'.$request->foto->extension();
        $request->foto->move(public_path('images/profile'), $imageName);

        $user->foto = "images/profile/$imageName";

        $user->save();

        return response()->json([
            'status' => true,
            'message' => 'foto berhasil diupload',
            'data' => null,
            'error' => null,
        ], 200);
    }

    public function changepassword(Request $request)
    {
        $account = auth()->user();
        $user = User::find($account->id);
        if (!$user) {
            return response()->json([
                'status' => false,
                'message' => 'user tidak ditemukan',
                'data' => null,
                'error' => null,
            ], 200);
        }

        $validator = Validator::make($request->all(), [
            'password' => 'required|string|min:6',
            'newpassword' => 'required|string|min:6',
            'confirmpassword' => 'required|string|min:6',
        ]);

        if($validator->fails()){
            return response()->json([
                'status' => false,
                'message' => 'update gagal',
                'data' => null,
                'error' => $validator->errors()->toJson(),
            ], 200);
        }

        if (!Hash::check($request->get('password'), $user->password)) {
            return response()->json([
                'status' => false,
                'message' => 'password anda salah',
                'data' => null,
                'error' => null,
            ], 200);
        }

        if ($request->password == $request->get('newpassword')) {
            return response()->json([
                'status' => false,
                'message' => 'password baru tidak boleh sama dengan password lama',
                'data' => null,
                'error' => null,
            ], 200);
        }

        if ($request->get('newpassword') != $request->get('confirmpassword')) {
            return response()->json([
                'status' => false,
                'message' => 'password konfirmasi salah',
                'data' => null,
                'error' => null,
            ], 200);
        }

        $user->password = Hash::make($request->get('newpassword'));
        $user->save();

        return response()->json([
            'status' => true,
            'message' => 'password berhasil diperbarui',
            'data' => null,
            'error' => null,
        ], 200);

    }
}
