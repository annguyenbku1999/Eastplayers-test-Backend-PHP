<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|


/**
 * * This is Group Route Authencation
 * todo: register, login, logout
 */
Route::group(
  [
    'middleware' => 'api',
    'namespace'  => 'App\Http\Controllers',
    'prefix'     => 'auth',
  ],
  function () {
    Route::post('register', 'AuthController@register');
    Route::post('login', 'AuthController@login');
    Route::post('logout', 'AuthController@logout');
  }
);

/**
 * * This is Group Route Project
 * todo: add, edit, delete, showProjectsList, addMembertoProject
 */
Route::group(
  [
    'middleware' => 'api',
    'namespace'  => 'App\Http\Controllers',
    'prefix'     => 'project',
  ],
  function () {
    Route::get('showProjectsList', 'ProjectController@showProjectsList');
    Route::post('add', 'ProjectController@add');
    Route::post('edit', 'ProjectController@edit');
    Route::post('delete', 'ProjectController@delete');
    Route::post('addMembertoProject', 'ProjectController@addMembertoProject');
  }
);

/**
 * * This is Group Route Session
 * todo: add, edit, delete
 */
Route::group(
  [
    'middleware' => 'api',
    'namespace'  => 'App\Http\Controllers',
    'prefix'     => 'session',
  ],
  function () {
    Route::post('add', 'SessionController@add');
    Route::post('edit', 'SessionController@edit');
    Route::post('delete', 'SessionController@delete');
  }
);

/**
 * * This is Group Route Task
 * todo: add, edit, delete
 */
Route::group(
  [
    'middleware' => 'api',
    'namespace'  => 'App\Http\Controllers',
    'prefix'     => 'task',
  ],
  function () {
    Route::post('add', 'TaskController@add');
    Route::post('edit', 'TaskController@edit');
    Route::post('delete', 'TaskController@delete');
  }
);
