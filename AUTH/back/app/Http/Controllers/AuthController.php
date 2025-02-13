<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use League\Config\Exception\ValidationException;
use App\Models\User;
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
            $user_google    = Socialite::driver('google')->user();
            $user           = User::where('email', $user_google->getEmail())->first();
            if ($user != null) {

                Auth::login($user, true);
                return redirect()->route('user.dashboard');
                // set session
                $userId = $user->id;
                $newSession = User::find('user_id', $userId);
                $newSession->google_id = $user_google->id;
                $newSession->google_token = $user_google->token;
                $newSession->save();
            } else {
                // ===================== IF INCLUDE REGISTER NEW USER ==========================
                // $newUser = new User();
                // $newUser->name = $user_google->name;
                // $newUser->email = $user_google->email;
                // $newUser->username = $user_google->email;
                // $newUser->google_id = $user_google->id;
                // $newUser->google_token = $user_google->token;
                // $newUser->password = Hash::make(12345678);
                // $newUser->save();
                
                // Auth::login($newUser, true);
                // return redirect()->route('user.dashboard');

                FacadesSession::flash('error', 'Email belum terdaftar di system, Harap registrasi melalui admin');
                return redirect()->route('user.login');
            }
        } catch (Exception $e) {
            dd($e);
            return redirect()->route('user.login');
        }
    }
}
