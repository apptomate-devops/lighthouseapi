<?php

namespace App\Http\Controllers;

use App\Http\Requests\RegisterRequest;

use Exception;

use Illuminate\Http\JsonResponse;

use Illuminate\Http\Request;

use JWTAuth;

use Hash;

use App\User;

use Auth;

use Tymon\JWTAuth\Exceptions\JWTException;

use Adldap\Laravel\Facades\Adldap;

use Illuminate\Support\Facades\DB;

use Illuminate\Support\Facades\Http;

class UserController extends Controller
{
   public function __construct()

{

$this->data = [

'status' => false,

'code' => 401,

'data' => null,

'err' => [

'code' => 1,

'message' => 'Unauthorized'

]

];

}

/**

* With the request-> only method, we only take the email-password as a *parameter and perform the user authentication with the attempt *function of the auth method in the laravel. If the user is correct, we *return a token for the user to use for later processing.

*

* @param Request $request

* @return JsonResponse

*/

public function login(Request $request): JsonResponse

{ 
	$azure = new User();
	$azure_token = $azure->getAzureToken();
    $res = Http::withHeaders([
            'Authorization'=>$azure_token,
            'Content-Type' => 'application/json',
            'Accept' => 'application/json',
        ])->get('https://graph.microsoft.com/v1.0/users/'.$request->email);
        
    $user_res = json_decode($res,true);
    if(isset($user_res['error']))
    {
      $this->data = [
       'status' => "Error",
       'message' => 'User Not Found'
      ];
      
      return response()->json($this->data);
    }
    else
    {
 
      $credentials = $request->only(['email', 'password']);
      
      try {
      
      if (!$token = JWTAuth::attempt($credentials)) {
      
      throw new Exception('invalid_credentials');
      
      }
      
      
      $this->data = [
      
      'status' => "Success",
      
      'data' => [
      
      'token' => $token,
      
      'rm_id' => Auth::user()->id,
      
      'role' => Auth::user()->role,

      'user' => $user_res,

      'image' => Auth::user()->profile_picture,
      
      ],
      
      
      
      ]; } catch (Exception $e) {
      
      $this->data = [
       'status' => "Error",
       'message' => $e->getMessage()
      ];
      
      } catch (JWTException $e) {
      
      $this->data = [
       'status' => "Error",
       'message' => 'Could not create token'
      ];
      
      }
      
      return response()->json($this->data);
      }
      }/**

*I do not elaborate as the user registers method used here before, as described in RegisterRequest.

* @param RegisterRequest $request

* @return JsonResponse

*/


public function register(RegisterRequest $request): JsonResponse

{

// $user = User::create([

// 'name' => $request->post('name'),

// 'email' => $request->post('email'),

// 'password' => Hash::make($request->post('password'))

// ]);

// $this->data = [

// 'status' => true,

// 'code' => 200,

// 'data' => [

// 'User' => $user

// ],

// 'err' => null

// ];

// return response()->json($this->data, $this->data['code']);

}/**

* Bring the details of the verified user.

*

* @return JsonResponse

*/

public function verifyToken(Request $request): JsonResponse

{

$token=$request->token;
$this->data = [
'status' => true,
'code' => 200,
'data' => [
'user' => auth()->user(),
'token' => $token
],

'err' => null

];

return response()->json($this->data, $this->data['code']);

}


public function detail(): JsonResponse

{

$this->data = [

'status' => true,

'code' => 200,

'data' => [

'User' => auth()->user()

],

'err' => null

];

return response()->json($this->data);

}/**

*Log out the user and make the token unusable.

* @return JsonResponse

*/

public function logout(): JsonResponse

{

//auth()->logout();
Auth::guard('api')->logout();

$data = [

'status' => true,

'code' => 200,

'data' => [

'message' => 'Successfully logged out'

],

'err' => null

];

return response()->json($data);

}/**

* Renewal process to make JWT reusable after expiry date.

* @return JsonResponse

*/

public function refresh(): JsonResponse

{

$data = [

'status' => true,

'code' => 200,

'data' => [

'_token' => auth()->refresh()

],

'err' => null

];

return response()->json($data, 200);

}


}
