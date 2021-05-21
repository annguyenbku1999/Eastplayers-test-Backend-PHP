<?php

namespace App\Http\Controllers;

use App\Models\Users;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class AuthController extends Controller
{
  /**
   * * register user
   * @param Request $request
   */
  public function register(Request $request)
  {
    //Validate request is right format input.
    $validator = Validator::make(
      $request->all(),
      [
        'firstName'     => 'required|string|between:2,100',
        'lastName'      => 'required|string|between:2,100',
        'username'      => 'required|string|between:2,100|unique:Users',
        'email'         => 'required|email|unique:Users',
        'password'      => 'required|string|min:6',
        'dateOfBirth'   => 'required|date',
        'gender'        => 'in:male,female,non-binary',
        'urlAvatar'     => 'sometimes|string|nullable',
        'companyWork'   => 'sometimes|string|nullable'
      ]
    );
    if ($validator->fails()) {
      return response()->json(
        [$validator->errors()],
        422
      );
    }
    //Check gender must bo 'male', 'female' or 'non-binary' 
    $gender = $request->gender;
    if ($gender != 'male' && $gender != 'female' && $gender != 'non-binary') {
      return response()->json(
        "Gender is not correct.",
        422
      );
    }
    //Add request user info to model Users
    $User = new Users();
    $User->firstName = $request->firstName;
    $User->lastName = $request->lastName;
    $User->username = $request->username;
    $User->email = $request->email;
    $User->password = Hash::make($request->password);
    $User->gender = $request->gender;
    $User->dateOfBirth = $request->dateOfBirth;
    $User->urlAvatar = $request->urlAvatar;
    $User->companyWork = $request->companyWork;
    $User->save();
    return response()->json(
      "Successful.",
      200
    );
  }
  /**
   * * login user
   * @param Request $request
   */
  public function login(Request $request)
  {
    //Validate request is right format input.
    $validator = Validator::make(
      $request->all(),
      [
        "username" => 'required|string',
        "password" => 'required|string'
      ]
    );
    if ($validator->fails()) {
      return response()->json(
        [$validator->errors()],
        422
      );
    }
    //Check username or email exist in Users table
    $User = Users::where('username','=',$request->username)
                  ->orWhere('email','=',$request->username)
                  ->select('id','password','token')
                  ->first();
    if($User==null){
      return response()->json(
        ['Username does not exist.'],
        422
      );
    }
    if($User->token!=null){
      return Array(
        'id' => $User->id,
        'token' => $User->token
      );
    }
    /**
     * * Check password
     * todo: Check if password input is correct, it will generate token code
     */
    if (Hash::check($request['password'], $User->password) == true) {
      $token = Str::random(60);
      $UserUpadte = Users::find($User->id);
      $UserUpadte->token = $token;
      $UserUpadte->save();
      return Array(
        'id' => $User->id,
        'token' => $token
      );
    }
    return $User->password;
    return response()->json(
      "Successful.",
      200
    );
  }
/**
   * * logout user
   * @param Request $request
   */
  public function logout(Request $request)
  {
    //Validate request is right format input.
    $validator = Validator::make(
      $request->all(),
      [
        "UserId" => 'required|string',
        "token" => 'required|string'
      ]
    );
    if ($validator->fails()) {
      return response()->json(
        [$validator->errors()],
        422
      );
    }
    //Check username or email exist in Users table
    $User = Users::where('id','=',$request->UserId)
                  ->select('token')
                  ->first();
    if($User==null){
      return response()->json(
        ['User are not logged in.'],
        422
      );
    }
    /**
     * * Check password
     * todo: Check if token is correct, it will delete token
     */
    if ($request['token']==$User->token) {
     $UserUpdate = USers::find($request->UserId);
     $UserUpdate->token = NULL;
     $UserUpdate->save();
      return "You are loggout.";
    }
    else{
      return response()->json(
        ['token is incorrect.'],
        422
      );
    }
    return $User->password;
    return response()->json(
      "Successful.",
      200
    );
  }

  /**
   * * This is login swagger
   * todo: Show login Document 
   */
  /**
   * @SWG\POST(
   *     path="/api/auth/login",
   *     description="Login User",
   *     tags={"Authencation"},
   *     @SWG\Parameter(
   *         name="username",
   *         in="query",
   *         type="string",
   *         description="Enter your username or email",
   *         required=true,
   *     ),
   *     @SWG\Parameter(
   *         name="password",
   *         in="query",
   *         type="string",
   *         description="Enter your password",
   *         required=true,
   *     ),
   *     @SWG\Response(
   *         response=200,
   *         description="Successful.",
   *     ),
   *     @SWG\Response(
   *         response=422,
   *         description="Missing Data or Data is incorrect."
   *     )
   * )
   */

  /**
   * * This is logout swagger
   * todo: Show logout Document 
   */
  /**
   * @SWG\POST(
   *     path="/api/auth/logout",
   *     description="Logout User",
   *     tags={"Authencation"},
   *     @SWG\Parameter(
   *         name="UserId",
   *         in="query",
   *         type="string",
   *         description="Enter your username or email:",
   *         required=true,
   *     ),
   *     @SWG\Parameter(
   *         name="token",
   *         in="query",
   *         type="string",
   *         description="Enter the token issued when you login:",
   *         required=true,
   *     ),
   *     @SWG\Response(
   *         response=200,
   *         description="Successful.",
   *     ),
   *     @SWG\Response(
   *         response=422,
   *         description="Missing Data or Data is incorrect."
   *     )
   * )
   */

  /**
   * * This is register swagger
   * todo: Show register Document 
   */
  /**
   * @SWG\POST(
   *     path="/api/auth/register",
   *     description="Register User",
   *     tags={"Authencation"},
   *     @SWG\Parameter(
   *         name="firstName",
   *         in="query",
   *         type="string",
   *         description="Your first name",
   *         required=true,
   *     ),
   *     @SWG\Parameter(
   *         name="lastName",
   *         in="query",
   *         type="string",
   *         description="Your last name",
   *         required=true,
   *     ),
   *      @SWG\Parameter(
   *         name="username",
   *         in="query",
   *         type="string",
   *         description="Your username",
   *         required=true,
   *     ),
   *      @SWG\Parameter(
   *         name="email",
   *         in="query",
   *         type="string",
   *          format="email",
   *         description="Your Email",
   *         required=true,
   *     ),
   *      @SWG\Parameter(
   *         name="password",
   *         in="query",
   *         type="string",
   *         description="Your password",
   *         required=true,
   *     ),
   *      @SWG\Parameter(
   *         name="gender",
   *         in="query",
   *         type="string",
   *         enum={"male", "female", "non-binary"},
   *         description="Select gender",
   *         required=true,
   *     ),
   *      @SWG\Parameter(
   *         name="dateOfBirth",
   *         in="query",
   *         type="string",
   *         format="date",
   *         description="enter dateOfBirth with format: yyyy-mm-dd",
   *         required=true,
   *     ),
   *     @SWG\Parameter(
   *         name="urlAvatar",
   *         in="query",
   *         type="string",
   *         description="Your urlAvatar",
   *         required=false,
   *     ),
   *     @SWG\Parameter(
   *         name="companyWork",
   *         in="query",
   *         type="string",
   *         description="Your companyWork",
   *         required=false,
   *     ),
   *     @SWG\Response(
   *         response=200,
   *         description="Successful.",
   *     ),
   *     @SWG\Response(
   *         response=422,
   *         description="Missing Data or Data is incorrect."
   *     )
   * )
   */
}
