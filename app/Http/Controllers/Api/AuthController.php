<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use Hash;
use Auth;
use Exception;
use Validator;
use Carbon\Carbon;
use App\Models\User;
use App\Models\UniqueUserModel;
use ResponseFormatter;
use Socialite;

class AuthController extends Controller
{
    //
    
    // method login
    public function login(Request $request){
        try {
            // validation
            $validated = Validator::make($request->all(),[
                    'email' => 'required|email',
                    'password' => 'required'
                ]);
                if($validated->fails()){
                    return ResponseFormatter::success($validated->errors()->all(), 'unprocessable entity', 422);
                }
            // cek user exist
            $user = User::where('email', $request->email)->first();
            if(!$user){
                return ResponseFormatter::error(['message' => 'Unauthorized'], 'email tidak terdaftar', 500);
            }
            
            // cek credentials
            $credentials = request(['email','password']);
            if(!Auth::attempt($credentials)){
                return ResponseFormatter::error(['message' => 'Unauthorized'], 'periksa kembali email dan password', 500);
            }

            // jika berhasil maka
            // cek password
            if(!Hash::check($request->password, $$user->password)){
                return ResponseFormatter::error(['message' => 'Unauthorized'], 'Password salah', 500);
            }
            // update last login
            $user = Auth::user();
            $user->update([
                'last_login' => Carbon::now()->toDateTimeString()
            ]);
            // jika behasil maka berikan token
            $tokenResult = $user->createToken('authToken')->plainTextToken;
            return ResponseFormatter::success(
                [
                    'access_token' => $tokenResult,
                    'token_type' => 'Bearer',
                    'user' => $user
                ], 'Authenticated'
            );
        } catch (Exception $error) {
            return ResponseFormatter::error([
                'message' => 'Something went wrong',
                'error' => $error
            ], 'Authentication Failed', 500);
        }
    }

    // method register
    public function register(Request $request){
        // validasi username exist
        $validated = Validator::make($request->all(),[
            'email' => 'required|email|unique:users',
            'password' => 'required'
        ]);
        if($validated->fails()){
            return ResponseFormatter::success($validated->errors()->all(), 'unprocessable entity', 422);
        }

        $user = User::create([
            'name' => $request->email,
            'email' => $request->email,
            'password' => Hash::make($request->password),
        ]);

        // save data ke ms_peserta
        $uniqueUserModel = UniqueUserModel::create([
            'user_id' => $user->id,
            'status' => 'inactive',
            'created_by' => $user->id,
            'updated_by' => $user->id,
            'created_at' => Carbon::now()->toDateTimeString(),
            'updated_at' => Carbon::now()->toDateTimeString(),
        ]);
        $tokenResult = $user->createToken('authToken')->plainTextToken;
        return ResponseFormatter::success([
            'access_token' => $tokenResult,
            'token_type' => 'Bearer',
        ], 'register successfully', 200);
    }

    // method logout
    public function logout(Request $request){
        //revoking token
        $token = $request->user()->currentAccessToken()->delete();
        
        // update last login
        $user = Auth::user();
        $user->update([
            'last_login' => Carbon::now()->toDateTimeString()
        ]);
        return ResponseFormatter::success($user, "berhasil logout, token revoked",200);
    }

     // redirect by provider
     public function redirectToProvider($driver){
        return Socialite::driver($driver)->stateless()->redirect();
     }

     // handle provider callback
    public function handleProviderCallback($driver) {
        $userprovider = Socialite::driver($driver)->user();
        $finduser = User::where($driver.'_id', $userprovider->id)->first();
            if($finduser){
         
                // update last login
                $user = User::find($finduser->id);
                $user->update([
                    'last_login' => Carbon::now()->toDateTimeString()
                ]);
                // jika behasil maka berikan token
                $tokenResult = $user->createToken('authToken')->plainTextToken;
                Auth::login($finduser);
                return ResponseFormatter::success(
                    [
                        'access_token' => $tokenResult,
                        'token_type' => 'Bearer',
                        'user' => $user
                    ], 'Authenticated'
                );         
            }else{
                switch ($driver) {
                    case 'google':
                        $data_driver = [
                            'google_id' => $userprovider->id
                        ];
                        break;
                    
                    default:
                        $data_driver = [
                            'facebook_id' => $userprovider->id
                        ];
                        break;
                }
                $user = User::create(array_merge([
                    'name' => $userprovider->name,
                    'email' => $userprovider->email,
                    'password' => Hash::make($userprovider->email),
                ], $data_driver));
        
                // save data ke ms_peserta
                $uniqueUserModel = UniqueUserModel::create([
                    'user_id' => $user->id,
                    'status' => 'inactive',
                    'created_by' => $user->id,
                    'updated_by' => $user->id,
                    'created_at' => Carbon::now()->toDateTimeString(),
                    'updated_at' => Carbon::now()->toDateTimeString(),
                ]);
                $tokenResult = $user->createToken('authToken')->plainTextToken;                
                return ResponseFormatter::success([
                    'access_token' => $tokenResult,
                    'token_type' => 'Bearer',
                ], 'register successfully', 200);
            }
    }
}
