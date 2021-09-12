<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Validator;


class AuthController extends Controller
{
    
    public function register(Request $request)
    {

        $validator = Validator::make($request->all(), [
            'name' => 'required|max:55',
            'email' => 'email|required|unique:users',
            'password' => 'required|min:6|confirmed',
        ]);

        if ($validator->fails()) {
            return response(['errors' => $validator->errors()->all()], 422);
        }

        $request['password'] = bcrypt($request->password);

        $user = User::create($request->all());

        $accessToken = $user->createToken('authToken')->accessToken;

        return response()->json([
            'user' => $user,
            'access_token' => $accessToken,
        ]);

    }

    public function login(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email' => 'email|required|exists:users',
            'password' => 'required|min:6',
        ]);

        if ($validator->fails()) {
            return response(['errors' => $validator->errors()->all()], 422);
        }

        if (!auth()->attempt($request->all())) {
            return response()->json([
                'message' => 'Invalid email or password.',
            ], 401);
        }

        $accessToken = auth()->user()->createToken('authToken')->accessToken;

        return response()->json([
            'user' => auth()->user(),
            'access_token' => $accessToken,
        ]);
    }

    public function profile(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|max:55',
            'contact_no' => ['required', 'regex:/^(?:\+88|01)?\d{11}\r?$/'],
            'avatar' => 'image|mimes:png,jpg,jpeg|max:1024',
        ]);

        if ($validator->fails()) {
            return response(['errors' => $validator->errors()->all()], 422);
        }

        $requestData = array('name' => $request->name, 'contact_no' => $request->contact_no);

        if ($request->hasFile('avatar')) {

            if (auth()->user()->avatar) {
                $this->delete(auth()->user()->avatar);
            }

            $newFileName = $this->upload($request->avatar);
            $requestData['avatar'] = $newFileName;
        }

        auth()->user()->update($requestData);

        return response()->json(['message' => 'Profile Successfully updated']);
    }

    public function logout()
    {
        auth()->user()->tokens->each(function ($token, $key) {
            $token->delete();
        });

        return response()->json([
            'message' => 'Logout successfully',
        ]);
    }

    public function userList()
    {
        $list = User::whereNotIn('id', [auth()->user()->id])
            ->select('name', 'id as value')
            ->orderBy('name', 'asc')
            ->get();

        return response()->json($list);
    }

    private function upload($file)
    {
        $filename = $file->getClientOriginalName();
        $name = explode(".", $file)[0];
        $extension = $file->getClientOriginalExtension();
        $newFileName = auth()->user()->name . '_' . auth()->user()->id . '.' . $extension;

        $file->storeAs('images', $filename, 'public');
        Storage::move('/public/images/' . $filename, '/public/images/' . $newFileName);
        return $newFileName;
    }

    private function delete($filename)
    {
        Storage::delete('/public/images/' . $filename);
    }
}
