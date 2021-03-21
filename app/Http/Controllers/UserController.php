<?php

namespace App\Http\Controllers;

use App\User;
use App\CategoryType;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use JWTAuth;
use DB;
use Tymon\JWTAuth\Exceptions\JWTException;

class UserController extends Controller
{
    public function login(Request $request)
    {
        $credentials = $request->only('email', 'password');

        try {
            if (! $token = JWTAuth::attempt($credentials)) {
                return response()->json(['error' => 'invalid_credentials'], 400);
            }
        } catch (JWTException $e) {
            return response()->json(['error' => 'could_not_create_token'], 500);
        }

        return response()->json(compact('token'));
    }

    public function register(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'username' => 'required|string|max:255',
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:6',
        ]);

        if($validator->fails()){
            // return response()->json($validator->errors()->toJson(), 400);
            return response()->json([
                'status' => 'error validation',
                'message' => $validator->errors()->toJson() 
            ],400);
        }

        if ($request->get('password')!=$request->get('repassword')) {
            return response()->json([
                'status' => 'error validation',
                'message' => 'Password doesnot match'
            ],400);
        }

        $profileImage = $request->file('image');
        $profileImageSaveAsName = time() . "-admin." . 
        $profileImage->getClientOriginalExtension();
        $upload_path = 'image';
        $profile_image_url = $profileImageSaveAsName;
        $success = $profileImage->move($upload_path, $profileImageSaveAsName);

        $user = User::create([
            'name' => $request->get('name'),
            'email' => $request->get('email'),
            'password' => Hash::make($request->get('password')),
            'image' => $profile_image_url
        ]);

        // $token = JWTAuth::fromUser($user);
        // return response()->json(compact('user','token'),201);
        return response()->json([
                'status' => 'success',
                'message' => 'Data added successfully'
        ],201);
    }

    public function getAuthenticatedUser()
    {
        try {

            if (! $user = JWTAuth::parseToken()->authenticate()) {
                return response()->json(['user_not_found'], 404);
            }

        } catch (Tymon\JWTAuth\Exceptions\TokenExpiredException $e) {

            return response()->json(['token_expired'], $e->getStatusCode());

        } catch (Tymon\JWTAuth\Exceptions\TokenInvalidException $e) {

            return response()->json(['token_invalid'], $e->getStatusCode());

        } catch (Tymon\JWTAuth\Exceptions\JWTException $e) {

            return response()->json(['token_absent'], $e->getStatusCode());

        }

        // $user = DB::table('provinsi')->leftJoin('kota','provinsi.id','=','kota.id_prov')->paginate(10);

        // return response()->json(compact('user'));
        $data = [
            "status"=> "OK",
            "data"=> [ 
                        "user" => [
                                   "name"=> "Shriyansh",
                                   "email"=>"some@email.com",
                                   "contact"=>"1234567890",
                                   "fcmToken"=>"Token@123"
                        ],
                        "event"=> [
                                   "status" => "successful",
                                   "status_code" => 4
                         ]
               ]
       ];

       return response()->json($data, 200);
    }

    public function fetchCategory()
    {
        $categories = CategoryType::with(['kota', 'kecamatan'])->get();
        return response()->json($categories, 200);
    }

    public function getProfileUser(Request $request)
    {
        $this->validate($request, [
            'token' => 'required'
        ]);
 
        $user = JWTAuth::authenticate($request->token);
 
        return response()->json(['user' => $user]);
    }

    public function show($id)
    {
        $user = User::find($id);
    
        if (!$user) {
            return response()->json([
                'status' => 'error',
                'message' => 'Sorry, data with id ' . $id . ' cannot be found'
            ], 400);
        }
    
        return $user;
    }

    public function update(Request $request, $id)
    {
        $user = User::find($id);
    
        if (!$user) {
            return response()->json([
                'status' => 'error',
                'message' => 'Sorry, product with id ' . $id . ' cannot be found'
            ], 400);
        }
    
        $updated = $user->fill($request->all())
            ->save();
    
        if ($updated) {
            return response()->json([
                'status' => 'success',
                'message' => 'Data successfully changed'
            ]);
        } else {
            return response()->json([
                'status' => 'error',
                'message' => 'Sorry, data could not be updated'
            ], 500);
        }
    }

    public function destroy($id)
    {
        $user = User::find($id);
    
        if (!$user) {
            return response()->json([
                'status' => 'error',
                'message' => 'Sorry, product with id ' . $id . ' cannot be found'
            ], 400);
        }
    
        if ($user->delete()) {
            return response()->json([
                'status' => 'success',
                'message' => 'Deleted successfully'
            ]);
        } else {
            return response()->json([
                'status' => 'error',
                'message' => 'Data could not be deleted'
            ], 500);
        }
    }

    public function logout(Request $request)
    {
        $this->validate($request, [
            'token' => 'required'
        ]);
 
        try {
            JWTAuth::invalidate($request->token);
 
            return response()->json([
                'success' => true,
                'message' => 'User logged out successfully'
            ]);
        } catch (JWTException $exception) {
            return response()->json([
                'success' => false,
                'message' => 'Sorry, the user cannot be logged out'
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }
}