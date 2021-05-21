<?php

namespace App\Http\Controllers;

use App\Models\Projects;
use App\Models\Projects_Users;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use SebastianBergmann\CodeCoverage\Report\Xml\Project;

class ProjectController extends Controller
{

  /**
   * * show List of project
   * @param Request $request
   */
  public function showProjectsList(Request $request)
  {
    //Validate request is right format input.
    $validator = Validator::make(
      $request->all(),
      [
        'UserId'        => 'required|string|between:2,100',
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
    // Add Project to Project table
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
    return Array(
      'ProjectName' => $request->name,
      'ProjectKey' => $key_project
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
    return Array(
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
    $key_project = Str::random(60);
    $Project = Projects::find($request->ProjectId);
    $Project->name = $request->name;
    $Project->key = $key_project;
    $Project->urlAvatar = $request->urlAvatar;
    $Project->save();
    return Array(
      'ProjectName' => $request->name,
      'ProjectKey' => $key_project
    );
  }

  /**
   * * delete project
   * @param Request $request
   */
  public function delete(Request $request)
  {
   
  }
 
 

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
   * * This is delete swagger
   * todo: Show edit project Document 
   */
  
}
