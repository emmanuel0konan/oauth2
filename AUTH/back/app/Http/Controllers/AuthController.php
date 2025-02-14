<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use League\Config\Exception\ValidationException;
use App\Models\User;
use Illuminate\Support\Str;
use Exception;
use Illuminate\Support\Facades\Session as FacadesSession;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Laravel\Socialite\Facades\Socialite;

class AuthController extends Controller
{
   //
   public function login(Request $request)
   {
     $user = User::where('email',$request->email)->first();
     if(Hash::check($request->password,$user->password)){
      return response()->json([
         'token'=>$user->createToken(time())->plainTextToken
      ]);
     }
   }
   public function register(Request $request){
    // $request->validate([
    //     'name' => 'required|max:50',
    //     'email' => 'required|email|max:250',
    //     'password' => 'required|confirmed',
    // ]);
    //return("hello word") ;
    //create new user
    $user = User::create([
        'name' => $request->name,
        'email' => $request->email,
        'password' => Hash::make($request->password),
    ]);
    return response()->json($user);
   }
   public function redirectToGoogle()
    {
        return Socialite::driver('google')->redirect();
    }
    public function handleGoogleCallback(Request $request)
    {
        try {
            $user_google = Socialite::driver('google')->stateless()->user();
            //dd($user_google);
            $user = User::where('email', $user_google->getEmail())->first();
            if ($user != null) {
                Auth::login($user);
                $token = $user->createToken(time())->plainTextToken ;
                return redirect()->route('redirect',compact('token'));
                // set session
            } else {
                // ===================== IF INCLUDE REGISTER NEW USER ==========================
                $newUser = new User();
                $newUser->name = $user_google->name;
                $newUser->email = $user_google->email;
                $newUser->google_id = $user_google->id;
                $newUser->google_token = $user_google->token;
                $newUser->password = Hash::make(Str::random(8));
                $newUser->save();
                $token = $user->createToken(time())->plainTextToken ;
                Auth::login($newUser);
                return redirect()->route('redirect',compact('token'));
            }
        } catch (Exception $e) {
            dd($e);
            return redirect()->route('user.login');
        }
    }
}
