<?php

namespace App\Http\Controllers;

use App\Models\User;

use Validator;
use Throwable;
use Tymon\JWTAuth\Facades\JWTAuth;
use Tymon\JWTAuth\Exceptions\JWTException as JWTException;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use Carbon\Carbon;
use \Symfony\Component\Console\Output\ConsoleOutput;

class AuthController extends Controller
{
        /**
         * Store a newly created resource in storage.
         *
         * @param  \Illuminate\Http\Request  $request
         * @return \Illuminate\Http\Response
         */
    public function register(Request $request) {
        try {
            $postParams = $request->post();

            $userValidator = Validator::make($request->all(), [
                'email' => 'required|string|email|max:255|unique:users',
                'username' => 'required|unique:users',
                'password' => 'required|string|min:6',
            ]);

            if($userValidator->fails()){
                return response()->json([
                    'status' => false,
                    'message' => 'validation failed',
                    'errors' => $userValidator->errors()
                ], 401);
            };

            $user = User::create([
                'id' => Str::uuid(),
                'email' => $postParams['email'],
                'username' => $postParams['username'],
                'password' => Hash::make($postParams['password']),
                'created_at' => Carbon::now()->timestamp,
                'updated_at' => Carbon::now()->timestamp
            ]);

            return response()->json([
                'message' => 'The user is successfully registered',
            ], 200);
        } catch (Throwable $expception) {
            return response()->json([
                'status' => false,
                'message' => $expception->getMessage()
            ], 500);
        }
    }
        /**
         * Display the specified resource.
         *
         * @param  int  $id
         * @return \Illuminate\Http\Response
         */
    public function login(Request $request) {
        try {
            $credentials = $request->only('email', 'password', 'id');
            $validator = Validator::make($credentials, [ //$request->all()
                'email' => 'required|string|email|max:150',
                // 'username' => 'required',
                'password' => 'required|string|min:6|max:150',
            ]);

            if ($validator->fails()) {
                return response()->json($validator->errors(), 422);
            }
            $out = new ConsoleOutput();

            $userId = User::select('id')->where('email', $credentials['email'])->get();
            foreach ($userId as $item) {
                // $out->writeln(var_dump($item->id));
                $credentials['id'] = $item->id;
            }
            // $credentials['id'] = $userId['id'];
            // $out->writeln($userId);
            // if (! $token = auth()->attempt($credentials)) {
            if (! $token = JWTAuth::attempt($credentials)) {
                return response()->json(['error' => 'Unauthorized'], 401);
            }
            return $this->createNewToken($token);
        } catch (Throwable $expception) {
            return response()->json([
                'status' => false,
                'message' => $expception->getMessage()
            ], 500); 
        }
    }

    public function profile(Request $request ) {
        try {
            // $out->writeln($user);
            // $out = new ConsoleOutput();

            if (! $user = JWTAuth::parseToken()->authenticate()) {
                return response()->json(['errors' => $user], 404);
            }

            return response()->json(['user' => $user]);
        } catch (JWTException\TokenExpiredException $e) {
            return response()->json(['token_expired'], $e->getStatusCode());
        } catch (JWTException\TokenInvalidException $e) {
            return response()->json(['token_invalid'], $e->getStatusCode());
        } catch (JWTException\JWTException $e) {
            return response()->json(['token_absent'], $e->getStatusCode());
        }
    }

    public function logout() {
        auth()->logout();
        return response()->json(['message' => 'User succesfully signed out']);
    }

    public function refresh() {
        return $this->createNewToken(auth()->refesh());
    }

    protected function createNewToken($token){
        return response()->json([
            'access_token' => $token,
            'token_type' => 'bearer',
            'expires_in' => auth('api')->factory()->getTTL() * 60,
            'user' => auth()->user()
        ]);
    }
}
