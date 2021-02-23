<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use App\Rules\MatchOldPassword;
use Illuminate\Support\Str;
use App\User;
use Hash;
use DB;

class RmController extends Controller
{
    public function create(Request $request)
    {
     $validatedData = $request->validate([
        'email' => 'required|unique:users,email|email',
        'password' => 'required',
        'role'=> 'required',
        'profile_picture' => 'nullable|image|max:1024'
      ]);

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
     $image = $request->file('profile_picture');
     $uploadfile= 'http://localhost/lighthouse/public/profile/'.Str::random(10).'.'.$image->getClientOriginalExtension();
     $destinationPath = public_path('profile');
     $image->move($destinationPath, $uploadfile);

     DB::beginTransaction();
     $post = new User();
     $post->email = $request->email;
     $post->password = Hash::make($request->password);
     $post->role = $request->role;
     $post->profile_picture = $uploadfile;
     $post->save();

     if($request->role == "rm")
     {

        foreach(explode(',', $request->clients) as $row) 
        {
        DB::table('lc_client')->where('client_id', $row)->update(
            [
            'client_rm_id' => $post->id
            ]);
        }
            
     }
      DB::commit();
   
      if($post){
     return response()->json([
      'status' => "Success",
      'message' => "RM created successfully"
       ]);
      }else{
     return response()->json([
        "status"  => "Error",
        "message" => "failed to create RM record"
    ]);
    }
    }
  }

 public function list(Request $request)
  {
  if($request->searchterm == '')
  {
  $skip = $request->index * $request->count;
  $data=DB::table('users')
       ->select('id','email','role','profile_picture')
       ->skip($skip)
       ->take($request->count)
       ->get();
  $user_count =DB::table('users')->count();

  return response()->json([
             'status'=>"Success",
              'totalrecords'=> $user_count,
              'data' => $data
        ], 200);
  }  
  else
  {
       $searchterm = $request->searchterm;
        $data = DB::table('users')
           ->select('id','email','role','profile_picture')
           ->orwhere('email','Like',"%{$searchterm}%")
           ->orwhere('role','Like',"%{$searchterm}%")
           ->get();

           return response()->json([
             'status'=>"Success",
             'data' => $data
        ], 200);
       
  }
 }

 public function userlist(Request $request)
 {
 $data = DB::table('users')
           ->select('id','email','role','profile_picture')
           ->where('role','=',$request->role)
           ->get();

           return response()->json([
             'status'=>"Success",
             'data' => $data
        ], 200);
 }

 public function edit($id)
 {
 	    $data = DB::table('users')
           ->select('role','profile_picture','id')
           ->where('id','=',$id)
           ->get();
      $arr = json_decode($data,true);
          foreach($arr as $row)
           {
           $row['client'] = DB::table('lc_client')->where('client_rm_id',$id)->select('client_id','client_rm_id','client_name')->get();
           }

           return response()->json([
          'status' => "Success",
          'data'=>$row
           ],200);
    
 }

 public function update(Request $request,$id)
 {
 	 $validatedData = $request->validate([
       'role' => 'required',
       'profile_picture' => 'nullable|image|max:1024',
       'is_image_update' =>'required'
       ]);
     
     $uploadfile = "";
     if($request->is_image_update == "yes")
     {
     $image = $request->file('profile_picture');
     $uploadfile= 'http://localhost/lighthouse/public/profile/'.Str::random(10).'.'.$image->getClientOriginalExtension();
     $destinationPath = public_path('profile');
     $image->move($destinationPath, $uploadfile);
     }
     

     DB::beginTransaction();
     $post = User::find($id);
     $post->role = $request->role;
     if($request->is_image_update == "yes")
     {
     $post->profile_picture = $uploadfile;
     }
     $post->save();

     if($request->role == "rm")
     {
      foreach(explode(',', $request->clients) as $row) 
        {
        DB::table('lc_client')->where('client_id', $row)->update(
            [
            'client_rm_id' => $post->id
            ]);
        }
            
     }
      DB::commit();
   
      if($post){
     return response()->json([
      'status' => "Success",
      'message' => "RM updated successfully",
      'uploadfile'=>$uploadfile
       ]);
      }else{
     return response()->json([
        "status"  => "Error",
        "message" => "failed to update RM record"
    ]);
    }
    }
 

 public function change_password(Request $request,$id)
 {
  $validatedData =  $request->validate([
            'current_password' => ['required', new MatchOldPassword],
            'new_password' => ['required'],
            'new_confirm_password' => ['same:new_password'],
        ]);
  $update = User::find($id)->update(['password'=> Hash::make($request->new_password)]);

  if($update)
  {
  return response()->json([
             'status'  => 'success',
             'data' => 'password updated successfully'
        ], 200);
  }
  else
  {
   return response()->json([
             'status'  => 'success',
             'data' => 'try again later'
        ], 200);
  }
 }

 public function delete($id)
   {
    DB::table('users')->where('id',$id)->delete();
     return response()->json([
             'status'  => 'success',
             'data' => 'RM record deleted successfully'
        ], 200);
   }
}
