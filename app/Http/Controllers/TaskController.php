<?php

namespace App\Http\Controllers;

use App\Models\Projects;
use App\Models\Projects_Users;
use App\Models\Tasks;
use App\Models\Users_Tasks;
use App\Models\Users;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class TaskController extends Controller
{
  private function checkTokenAndKey($request){
      //Select USerID from token
    $User_id = Users::where('token', '=', $request->token)->select('id', 'username')->first();
    //If Token USer does not exist -> return false log.
    if ($User_id == null) {
      return response()->json(
        "Check Token User false.",
        422
      );
    }
    $User_username = $User_id->username;
    $User_id = $User_id->id;
    //Select ProjectId from key
    $Project_id = Projects::where('key', '=', $request->key)->select('id')->first();
    //If can not find Project from key-> return false log.
    if ($Project_id == null) {
      return response()->json(
        "Check Key Project false.",
        422
      );
    }
    $Project_id = $Project_id->id;
    return Array(
      'User_id' => $User_id,
      'Username' => $User_username,
      'Project_id' => $Project_id,
    );
  }
  /**
   * * add project
   * @param Request $request
   */
  public function add(Request $request)
  {
    //Validate request is right format input.
    $validator = Validator::make(
      $request->all(),
      [
        'key'                => 'required|string',
        'token'              => 'required|string',
        'title'              => 'required|string',
        'description'        => 'sometimes|string|nullable',
        'SessionId'          => 'required|numeric',
        'urlAvatar'          => 'sometimes|string|nullable',
      ]
    );
    if ($validator->fails()) {
      return response()->json(
        [$validator->errors()],
        422
      );
    }
    //Check Key and Token from DB
    $resultCheck = $this->checkTokenAndKey($request);
    $User_id = $resultCheck['User_id'];
    $Project_id = $resultCheck['Project_id'];
    $Username = $resultCheck['Username'];
    //Check User is in Project  
    $Project_Owner_check = Projects_Users::where('idUser', '=', $User_id)
      ->where('idProject', '=', $Project_id)
      ->select('Type')
      ->first();
      return "Successful";
    //Case the user has joined the project
    if ($Project_Owner_check != null) {
      $addSession = new Sessions();
      $addSession->name = $request->name;
      $addSession->idProject = $Project_id;
      $addSession->save();
      return "Successful";
    }
    //Case the user does not have joined the project
    else {
      return response()->json(
        $Username . " does not have joined the project",
        422
      );
    }
  }
  /**
   * * edit project
   * @param Request $request
   */
  public function edit(Request $request)
  {
    //Validate request is right format input.
    $validator = Validator::make(
      $request->all(),
      [
        'SessionId'          => 'required|string',
        'key'                => 'required|string',
        'token'              => 'required|string',
        'name'               => 'required|string',
      ]
    );
    if ($validator->fails()) {
      return response()->json(
        [$validator->errors()],
        422
      );
    }
    //Check Key and Token from DB
    $resultCheck = $this->checkTokenAndKey($request);
    $User_id = $resultCheck['User_id'];
    $Project_id = $resultCheck['Project_id'];
    $Username = $resultCheck['Username'];
    //Check User is in Project  
    $Project_Owner_check = Projects_Users::where('idUser', '=', $User_id)
      ->where('idProject', '=', $Project_id)
      ->select('Type')
      ->first();
    //Case the user has joined the project
    if ($Project_Owner_check != null) {
      $editSession = Sessions::find($request->SessionId);
      //Check SessionId exist from Session Table DB
      if ($editSession == null) {
        return response()->json(
          "SessionId does not exist.",
          422
        );
      }
      $editSession->name = $request->name;
      $editSession->save();
      return "Successful";
    }
    //Case the user does not have joined the project
    else {
      return response()->json(
        $Username . " does not have joined the project",
        422
      );
    }
  }
  /**
   * * delete project
   * @param Request $request
   */
  public function delete(Request $request)
  {
    //Validate request is right format input.
    $validator = Validator::make(
      $request->all(),
      [
        'SessionId'          => 'required|string',
        'key'                => 'required|string',
        'token'              => 'required|string'
      ]
    );
    if ($validator->fails()) {
      return response()->json(
        [$validator->errors()],
        422
      );
    }
    //Check Key and Token from DB
    $resultCheck = $this->checkTokenAndKey($request);
    $User_id = $resultCheck['User_id'];
    $Project_id = $resultCheck['Project_id'];
    $Username = $resultCheck['Username'];
    //Check User is in Project  
    $Project_Owner_check = Projects_Users::where('idUser', '=', $User_id)
      ->where('idProject', '=', $Project_id)
      ->select('Type')
      ->first();
    //Case the user has joined the project
    if ($Project_Owner_check != null) {
      $deleteSession = Sessions::find($request->SessionId);
      //Check SessionId exist from Session Table DB
      if ($deleteSession == null) {
        return response()->json(
          "SessionId does not exist.",
          422
        );
      }
      $deleteSession->delete();
      return "Successful";
    }
    //Case the user does not have joined the project
    else {
      return response()->json(
        $Username . " does not have joined the project",
        422
      );
    }
  }

  /**
   * * This is function add session swagger
   * todo: Show add session Document 
   */
  /**
   * @SWG\POST(
   *     path="/api/session/add",
   *     description="add Session",
   *     tags={"Session"},
   *     @SWG\Parameter(
   *         name="token",
   *         in="query",
   *         type="string",
   *         description="Enter your token when you logged in:",
   *         required=true,
   *     ),
   *     @SWG\Parameter(
   *         name="key",
   *         in="query",
   *         type="string",
   *         description="Enter key project get from showProjectsList api:",
   *         required=true,
   *     ),
   *     @SWG\Parameter(
   *         name="name",
   *         in="query",
   *         type="string",
   *         description="Enter name session:",
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
   * * This is function edit session swagger
   * todo: Show edit session Document 
   */
    /**
   * @SWG\POST(
   *     path="/api/session/edit",
   *     description="edit Session",
   *     tags={"Session"},
   *     @SWG\Parameter(
   *         name="SessionId",
   *         in="query",
   *         type="number",
   *         description="Enter session ID:",
   *         required=true,
   *     ),
   *     @SWG\Parameter(
   *         name="token",
   *         in="query",
   *         type="string",
   *         description="Enter your token when you logged in:",
   *         required=true,
   *     ),
   *     @SWG\Parameter(
   *         name="key",
   *         in="query",
   *         type="string",
   *         description="Enter key project get from showProjectsList api:",
   *         required=true,
   *     ),
   *     @SWG\Parameter(
   *         name="name",
   *         in="query",
   *         type="string",
   *         description="Enter name session:",
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
   * * This is function delete session swagger
   * todo: Show delete session Document 
   */
  /**
   * @SWG\POST(
   *     path="/api/session/delete",
   *     description="edit Session",
   *     tags={"Session"},
   *     @SWG\Parameter(
   *         name="token",
   *         in="query",
   *         type="string",
   *         description="Enter your token when you logged in:",
   *         required=true,
   *     ),
   *     @SWG\Parameter(
   *         name="key",
   *         in="query",
   *         type="string",
   *         description="Enter key project get from showProjectsList api:",
   *         required=true,
   *     ),
   *     @SWG\Parameter(
   *         name="SessionId",
   *         in="query",
   *         type="number",
   *         description="Enter session ID:",
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
}
