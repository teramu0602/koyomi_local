<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\CalendarController;
use App\Http\Controllers\PostController;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\RegisterController;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\CreateGroupController;
use App\Http\Controllers\JoinController;
use App\Http\Controllers\GroupController;
use App\Http\Controllers\ScheduleController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

// Route::get('/', function () {
//     return view('welcome');
// });

Route::get('/admin', function () {
    return view('layouts/admin_calender');
});



Route::post('/register', [RegisterController::class, 'register'])->name('register');


//ログイン状態を見て、用意したログインページへいく
Route::post('/custom-login', [LoginController::class, 'login'])->name('custom.login.submit');
Route::get('/custom-login', function () {
    return view('admin/login');
})->name('custom.login');

Route::get('/signup', function () {
    return view('admin/signup');
});

Route::get('/admin/home', function () {
    return view('calender/home');
});

Route::get('/', [CalendarController::class, 'index'])
    ->name('calendar')
    ->middleware('auth');



Route::get('/group_home',function () {
    return view('calender.group_home');
})->name('grouphome');
Route::get('/group_home/{id}', [GroupController::class, 'show'])->name('group.home');
Route::get('/group_home/{id}/{year}/{month}', [GroupController::class, 'show'])->name('admin.group.calendar.show');

Route::get('/admin/home', [CalendarController::class, 'index']);
Route::get('/admin/home/{year}/{month}', [CalendarController::class, 'show' ])->name('admin.calendar.show');


Route::get('/grouplist', [CreateGroupController::class, 'index'])->name('groups.list');
Route::delete('/groups/{group}/leave', [GroupController::class, 'leaveGroup'])->name('groups.leave');



Route::get('/create_group', function () {
    return view('admin/create_group');
});

//カレンダーグループ作成
Route::post('/groups', [CalendarController::class, 'store'])->name('groups.store');
Route::get('/groups', function () {
    return view('admin/create_group');
})->name('groupcreate');

Auth::routes();

//ドロップダウンのログアウトでログインへ行く
Route::post('/logout', function () {
    Auth::logout();
    return redirect()->route('custom.login');
})->name('logout');


//ジョイン画面　試し
Route::post('/join', [JoinController::class, 'store'])->name('join.store');
Route::get('/join', function () {
    return view('admin.join_group');
})->name('groupjoin');

Route::get('/groups/{group}/edit', [GroupController::class, 'edit'])->name('groups.edit');
Route::put('/groups/{group}', [GroupController::class, 'update'])->name('groups.update');
Route::delete('/groups/{group}/users/{user}', [GroupController::class, 'removeUser'])->name('groups.removeUser');



Route::get('/edit', function () {
    return view('admin.edit_group');
})->name('groupedit');

//スケジュール作成画面
Route::get('/create_schedule', function () {
    return view('admin/create_schedule');
})->name('createschedule');
Route::post('/create_schedule/store', [ScheduleController::class, 'store'])->name('schedule.store');
//グループ用のスケジュール作成画面
Route::get('/create_schedule_group/{group_id}', [CalendarController::class, 'groupCalendarAdd'])->name('groupCalendarAdd');

Route::post('/create_schedule/store/group', [ScheduleController::class, 'storeGroup'])->name('group.schedule.store');

//show1 名前変更
Route::get('/event/{id}', [GroupController::class, 'showEvent'])->name('group.details');

// 編集画面の表示（フォームを出す）
Route::get('/event/{id}/edit', [ScheduleController::class, 's_edit'])->name('group.edit');

// 編集内容を保存する（フォーム送信先）
Route::put('/event/{id}', [ScheduleController::class, 's_update'])->name('group.update');


Route::delete('/event/{id}', [ScheduleController::class, 'destroy'])->name('group.destroy');

Route::get('/login', function () {
    return redirect('/custom-login');
})->name('login');
