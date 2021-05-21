<?php

namespace App\Http\Controllers;

use App\Models\Projects;
use App\Models\Projects_Users;
use App\Models\Users;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;

class ProjectController extends Controller
{

  /**
   * * show Projects List from current logged in user
   * todo: When User Login, it will generate token code after that this function will return Projects list of User is logged in.
   * @param Request $request
   */
  public function showProjectsList(Request $request)
  {
    //Validate request is right format input.
    $validator = Validator::make(
      $request->all(),
      [
        'token'        => 'required|string'
      ]
    );
    if ($validator->fails()) {
      return response()->json(
        [$validator->errors()],
        422
      );
    }
    //Get UserId from token 
    $User_id = Users::where('token', '=', $request->token)->select('id')->first();
    //Select Project List from Database
    $SelectProjectsList= DB::table('Projects')
                        ->leftJoin('Projects_Users','Projects.id','Projects_Users.idProject')
                        ->where('Projects_Users.idUser','=',$User_id->id)
                        ->get();
    return $SelectProjectsList;
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
        'UserId'        => 'required|numeric',
        'name'          => 'required|string|between:2,100',
        'urlAvatar'     => 'sometimes|string|nullable',
      ]
    );
    if ($validator->fails()) {
      return response()->json(
        [$validator->errors()],
        422
      );
    }
    $key_project = Str::random(60);
    $Project = new Projects();
    $Project->name = $request->name;
    $Project->key = $key_project;
    $Project->urlAvatar = $request->urlAvatar;
    $Project->save();
    // Add Owner when create Project to Projects_Users table
    $Add_ProjectOwner_to_Projects_Users = new Projects_Users();
    $Add_ProjectOwner_to_Projects_Users->idProject = $Project->id;
    $Add_ProjectOwner_to_Projects_Users->idUser = $request->UserId;
    $Add_ProjectOwner_to_Projects_Users->Type = "Owner";
    $Add_ProjectOwner_to_Projects_Users->save();
    return array(
      'ProjectName' => $request->name,
      'ProjectKey' => $key_project
    );
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
        'ProjectId'     => 'required|numeric',
        'name'          => 'required|string|between:2,100',
        'urlAvatar'     => 'sometimes|string|nullable',
      ]
    );
    if ($validator->fails()) {
      return response()->json(
        [$validator->errors()],
        422
      );
    }
    $Project = Projects::find($request->ProjectId);
    //If Project does not exist, return.
    if($Project == null){
      return response()->json(
        "Project does not exist.",
        422
      );
    }
    //Update Project fields.
    $Project->name = $request->name;
    $Project->urlAvatar = $request->urlAvatar;
    $Project->save();
    return array(
      'ProjectName' => $request->name,
      'ProjectKey' => $Project->key
    );
  }

  /**
   * * delete project
   * @param Request $request
   */
  private function StringArraytoArrayList($list){
    $list =str_replace('[', '', $list);
    $list =str_replace(']', '', $list);
    return array_map('intval',explode(",",$list));
  }
  public function addMembertoProject(Request $request)
  {
    //Validate request is right format input.
    $validator = Validator::make(
      $request->all(),
      [
        'UserIdList'         => 'required|string',
        'key'                => 'required|string',
        'token'              => 'required|string',
      ]
    );
    if ($validator->fails()) {
      return response()->json(
        [$validator->errors()],
        422
      );
    }
    //Select USerID from token
    $User_id = Users::where('token', '=', $request->token)->select('id')->first();
    //Select ProjectId from key
    $Project_id = Projects::where('key', '=', $request->key)->select('id')->first();
    //Check User is Project owner 
    $Project_Owner_check = Projects_Users::where('idUser','=',$User_id->id)
                        ->where('idProject','=',$Project_id->id)
                        ->select('Type')
                        ->first();
    //Handle UserIdList string to UserIdList array
    $UsersList = $this->StringArraytoArrayList($request->UserIdList);
    //Add member list id User is Project Owner
    if($Project_Owner_check->Type == 'Owner'){
      foreach($UsersList as $User){
        //Check User in List exist in Project
        $User_check = Projects_Users::where('idUser','=',$User)->first();
        //If User does not exist -> add User to Project with role Member
        if($User_check==null){
        $Add_User_to_Projects_Users = new Projects_Users();
        $Add_User_to_Projects_Users->idProject = $Project_id->id;
        $Add_User_to_Projects_Users->idUser = $User;
        $Add_User_to_Projects_Users->Type = "Member";
        $Add_User_to_Projects_Users->save();
      }
      //If User does exist -> return error string.
      else{
        return response()->json(
          "User have id=".$User." exist in Project.",
          422
        );
      }
      return "Successful.";
      }
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
        'token'         => 'required|string',
        'ProjectId'     =>  'required|numeric'
      ]
    );
    if ($validator->fails()) {
      return response()->json(
        [$validator->errors()],
        422
      );
    }
    //Select USerID from token
    $User_id = Users::where('token', '=', $request->token)->select('id')->first();
    //Check User is owner of project
    $Project_Owner_Check = Projects_Users::where('idProject', '=', $request->ProjectId)
      ->where('idUser', '=', $User_id->id)
      ->first();
    if ($Project_Owner_Check == null) {
      return response()->json(
        "You are not the owner of the project.",
        422
      );
    }else{
      $Project_Delete = Projects::find($request->ProjectId);
      $Project_Delete->delete();
      return "Successful";
    }
  }

 /**
   * * This is show Projects List from current logged in user swagger
   * todo: show Projects List Document 
   */
  /**
   * @SWG\POST(
   *     path="/api/project/showProjectsList",
   *     description="show Projects List",
   *     tags={"Project"},
   *     @SWG\Parameter(
   *         name="token",
   *         in="query",
   *         type="number",
   *         description="Enter your token when you logged in:",
   *         required=true,
   *     ),
   *     @SWG\Parameter(
   *         name="name",
   *         in="query",
   *         type="string",
   *         description="Enter your project name:",
   *         required=true,
   *     ),
   *     @SWG\Parameter(
   *         name="urlAvatar",
   *         in="query",
   *         type="string",
   *         description="Enter your urlAvatar",
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

  /**
   * * This is add project swagger
   * todo: Show add project Document 
   */
  /**
   * @SWG\POST(
   *     path="/api/project/add",
   *     description="Add Project",
   *     tags={"Project"},
   *     @SWG\Parameter(
   *         name="UserId",
   *         in="query",
   *         type="number",
   *         description="Enter your UserId:",
   *         required=true,
   *     ),
   *     @SWG\Parameter(
   *         name="name",
   *         in="query",
   *         type="string",
   *         description="Enter your project name:",
   *         required=true,
   *     ),
   *     @SWG\Parameter(
   *         name="urlAvatar",
   *         in="query",
   *         type="string",
   *         description="Enter your urlAvatar",
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

   /**
   * * This is delete swagger
   * todo: Show delete project Document 
   */
  /**
   * @SWG\POST(
   *     path="/api/project/delete",
   *     description="Delete Project",
   *     tags={"Project"},
   *     @SWG\Parameter(
   *         name="token",
   *         in="query",
   *         type="string",
   *         description="Enter your user token when logged in:",
   *         required=true,
   *     ),
   *     @SWG\Parameter(
   *         name="ProjectId",
   *         in="query",
   *         type="number",
   *         description="Enter your project id:",
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
   * * This is edit project swagger
   * todo: Show edit project Document 
   */
  
  /**
   * @SWG\POST(
   *     path="/api/project/edit",
   *     description="Edit Project",
   *     tags={"Project"},
   *     @SWG\Parameter(
   *         name="ProjectId",
   *         in="query",
   *         type="string",
   *         description="Enter your project name:",
   *         required=true,
   *     ),
   *     @SWG\Parameter(
   *         name="name",
   *         in="query",
   *         type="string",
   *         description="Enter your project name:",
   *         required=true,
   *     ),
   *     @SWG\Parameter(
   *         name="urlAvatar",
   *         in="query",
   *         type="string",
   *         description="Enter your urlAvatar",
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

   /**
   * * Function addMembertoProject Swaggger 
   * todo: add Member to Project Document 
   */
  /**
   * @SWG\POST(
   *     path="/api/project/addMembertoProject",
   *     description="add Member to Project",
   *     tags={"Project"},
   *     @SWG\Parameter(
   *         name="token",
   *         in="query",
   *         type="number",
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
   *         name="urlAvatar",
   *         in="query",
   *         type="string",
   *         description="Enter User Member List like [1,2,3,...]",
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
