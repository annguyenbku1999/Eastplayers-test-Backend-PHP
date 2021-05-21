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
  private function checkTokenAndKey($request)
  {
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
    return array(
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
    //Case the user has joined the project
    if ($Project_Owner_check != null) {
      //Add Task to Database
      $addTask = new Tasks();
      $addTask->title = $request->title;
      $addTask->description = $request->description;
      $addTask->idSession = $request->SessionId;
      $addTask->urlAvatar = $request->urlAvatar;
      $addTask->save();
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
        'key'                => 'required|string',
        'token'              => 'required|string',
        'TaskId'             => 'required|numeric',
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
    //Case the user has joined the project
    if ($Project_Owner_check != null) {
      //Add Task to Database
      $editTask = Tasks::find($request->TaskId);
      if ($editTask == null) {
        return response()->json(
          "Task does not exist.",
          422
        );
      }
      $editTask->title = $request->title;
      $editTask->description = $request->description;
      $editTask->idSession = $request->SessionId;
      $editTask->urlAvatar = $request->urlAvatar;
      $editTask->save();
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
        'key'                => 'required|string',
        'token'              => 'required|string',
        'TaskId'             => 'required|numeric'
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
      //Add Task to Database
      $deleteTask = Tasks::find($request->TaskId);
      if ($deleteTask == null) {
        return response()->json(
          "Task does not exist.",
          422
        );
      }
      $deleteTask->delete();
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
   * * show Tasks List
   * @param Request $request
   */
  public function showTasksList(Request $request)
  {
    //Validate request is right format input.
    $validator = Validator::make(
      $request->all(),
      [
        'key'                => 'required|string',
        'token'              => 'required|string',
        'SessionId'          => 'required|numeric'
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
      $Tasks_List = Tasks::where('idSession', '=', $request->SessionId)
        ->get();
      return $Tasks_List;
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
   * * This is function add task swagger
   * todo: Show add task Document 
   */
  /**
   * @SWG\POST(
   *     path="/api/task/add",
   *     description="add task",
   *     tags={"Task"},
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
   *      @SWG\Parameter(
   *         name="SessionId",
   *         in="query",
   *         type="number",
   *         description="Enter SessionId:",
   *         required=true,
   *     ),
   *     @SWG\Parameter(
   *         name="title",
   *         in="query",
   *         type="string",
   *         description="Enter Title Task:",
   *         required=true,
   *     ),
   *     @SWG\Parameter(
   *         name="description",
   *         in="query",
   *         type="string",
   *         description="Enter description:"
   *     ),
   *     @SWG\Parameter(
   *         name="urlAvatar",
   *         in="query",
   *         type="string",
   *         description="Enter urlAvatar:"
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
   * * This is function edit task swagger
   * todo: Show edit task Document 
   */
  /**
   * @SWG\POST(
   *     path="/api/task/edit",
   *     description="edit task",
   *     tags={"Task"},
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
   *         description="Enter SessionId:",
   *         required=true,
   *     ),
   *     @SWG\Parameter(
   *         name="TaskId",
   *         in="query",
   *         type="number",
   *         description="Enter TaskId:",
   *         required=true,
   *     ),
   *     @SWG\Parameter(
   *         name="title",
   *         in="query",
   *         type="string",
   *         description="Enter Title Task:",
   *         required=true,
   *     ),
   *     @SWG\Parameter(
   *         name="description",
   *         in="query",
   *         type="string",
   *         description="Enter description:",
   * 
   *     ),
   *     @SWG\Parameter(
   *         name="urlAvatar",
   *         in="query",
   *         type="string",
   *         description="Enter urlAvatar:",
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
   * todo: Show delete task Document 
   */
  /**
   * @SWG\POST(
   *     path="/api/task/delete",
   *     description="delete task",
   *     tags={"Task"},
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
   *         name="TaskId",
   *         in="query",
   *         type="number",
   *         description="Enter Task ID:",
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
   * * This is function show Tasks list swagger
   * todo: Show Task list Document 
   */
  /**
   * @SWG\GET(
   *     path="/api/task/showTasksList",
   *     description="show Tasks List",
   *     tags={"Task"},
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
