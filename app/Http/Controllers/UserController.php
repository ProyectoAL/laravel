<?php

namespace App\Http\Controllers;

use App\Models\user;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;

class UserController extends Controller
{
    use AuthorizesRequests, ValidatesRequests;

    public function signup(Request $request)
    {
        $request->validate([
            'mote' => 'required | unique:users',
            'name' => 'required',
            'lastname' => 'required',
            'date',
            'centro',
            'email' => 'required | email | unique:users',
            'password' => 'required',
            'role',
        ]);


        $user = new user();
        $user->mote = $request->mote;
        $user->name = $request->name;
        $user->lastname = $request->lastname;
        $user->date = $request->date;
        $user->centro = $request->centro;
        $user->email = $request->email;
        $user->password = Hash::make($request->password);
        $user->role = $request->role;

        if (!empty($user->date)) {
            $user->role = "student";
        } else {
            $user->role = "teacher";
        }
        $user->save();
        // Auth::login($user);
        return response()->json([
            "status" => 1,
            'message' => 'Successfully created user!',
            "value" => $user
        ]);
    }
    public function login(Request $request)
    {
        $request->validate([
            "mote" => "required",
            "password" => "required"
        ]);
        $user = user::where("mote", "=", $request->mote)->first();
        if (isset($user->id)) {
            if (Hash::check($request->password, $user->password)) {
                // $token = $user->createToken("auth_token")->plainTextToken;
                return response()->json(
                    $user
                );
            } else {
                return response()->json(
                    "Password_bad"
                );
            }
        } else {
            return response()->json(
                "Username_bad"
            );
        }
    }
    public function update(Request $request, $id)
    {

        $user_id = User::find($id);
        if ($user_id) {
            $user_id->password = Hash::make($request->input('password'));
            $user_id->save();
            return response()->json([
                'message' => 'Contraseña actualizada correctamente.'
            ]);
        } else {
            return response()->json([
                'error' => 'Usuario no encontrado.'
            ], 404);
        }
        /*$user_id = $id;
        if (user::where(["id" => $user_id])->exists()) {
            $update = user::find($user_id);
            $update->password = Hash::make($request->password);
            $update->save();
            return response()->json([
                "status" => 1,
                "message" => "Actualizado correctamente",
            ]);
        } else {
            return response()->json([
                "status" => 1,
                "message" => "No se pùdo actucalizar",
            ]);
        }*/
    }
}
