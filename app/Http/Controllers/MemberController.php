<?php

namespace App\Http\Controllers;

use App\Http\Requests\RegisterRequest;

use Exception;

use Illuminate\Http\JsonResponse;

use Illuminate\Http\Request;

use JWTAuth;

use Hash;

use App\User;

use Tymon\JWTAuth\Exceptions\JWTException;

use Adldap\Laravel\Facades\Adldap;

use Illuminate\Support\Facades\DB;


class MemberController extends Controller
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

{ $credentials = $request->only(['email', 'password']);

try {

if (!$token = JWTAuth::attempt($credentials)) {

throw new Exception('invalid_credentials');

}

$this->data = [

'status' => true,

'code' => 200,

'data' => [

'_token' => $token

],

'err' => null

]; } catch (Exception $e) {

$this->data['err']['message'] = $e->getMessage();

$this->data['code'] = 401;

} catch (JWTException $e) {

$this->data['err']['message'] = 'Could not create token';

$this->data['code'] = 500;

}

return response()->json($this->data, $this->data['code']);

}/**

*I do not elaborate as the user registers method used here before, as described in RegisterRequest.

* @param RegisterRequest $request

* @return JsonResponse

*/


public function register(RegisterRequest $request): JsonResponse

{

$user = User::create([

'name' => $request->post('name'),

'email' => $request->post('email'),

'password' => Hash::make($request->post('password'))

]);

$this->data = [

'status' => true,

'code' => 200,

'data' => [

'User' => $user

],

'err' => null

];

return response()->json($this->data, $this->data['code']);

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



public function create(Request $request){

   	  $validatedData = $request->validate([
            'user_firstname' => 'required',
            'user_lastname'=>'required',
            'user_name'=>'required',
            'user_username'=>'required',
            'user_password'=>'required',
            'user_opassword'=>'required',
            'user_country'=>'required',
            'user_contactno'=>'required',
            'user_type'=>'required',
            'user_role'=>'required',
            'user_status'=>'required',
        ]);

   $data=DB::table('lc_user')->insert([
    ['user_firstname' => $request->user_firstname,'user_lastname' => $request->user_lastname,'user_name'=>$request->user_name,'user_username'=>$request->user_username,'user_password' => Hash::make($request->user_password), 'user_opassword' => $request->user_opassword,'user_country'=>$request->user_country,'user_contactno'=>$request->user_contactno,'user_type'=>$request->user_type,'user_role'=>$request->user_role,'user_status'=>$request->user_status]
]);

   		if($data){
  return response()->json([
        "message" => "Client Created Successfully" 
    ], 200);
}else{
	return response()->json([
        "message" => "Client record error"
    ], 201);
}
   	
   }
public function list()
   {
     $data=DB::table('lc_user')->get();
     return response()->json([
             'data' => $data
        ], 200);   
   }

   public function delete($id)
   {
    DB::table('lc_user')->where('user_id',$id)->delete();
     return response()->json([
             'message' => 'record deleted successfully'
        ], 200);
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
