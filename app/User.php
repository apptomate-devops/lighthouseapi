<?php

namespace App;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Tymon\JWTAuth\Contracts\JWTSubject;

class User extends Authenticatable  implements JWTSubject
{
    use Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name', 'email', 'password',
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password', 'remember_token',
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    public function getJWTIdentifier()

{

return $this->getKey();

}

public function getJWTCustomClaims()

{

return [];

}

public function getAzureToken()
{

    $grant_type = 'client_credentials';
    $client_id = '6807fd39-ae44-42dc-ab85-5b971ababa73';
    $client_secret = '1~K6AO_QxA3u4nZK2A28q.j50rb-jzp7cm';
    $resource = 'https://graph.microsoft.com/';
     

    $url = 'https://login.microsoftonline.com/c24167bc-ce54-479c-b126-59e68ab033ea/oauth2/token';
    $ch = curl_init($url);

    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query(
     array('grant_type' => $grant_type, 'client_id'=> $client_id, 'client_secret'=>$client_secret, 'resource'=>$resource)
    ));

    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

    $result = curl_exec($ch);
    if ($result)
    {
      $azure_res = json_decode($result,true);
    } else {
      echo "something went wrong";
    }
    curl_close($ch);
    return $azure_res['access_token'];
}

}
