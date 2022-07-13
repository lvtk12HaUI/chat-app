<?php

use Illuminate\Support\Facades\Route;
use App\Models\Message;
use Illuminate\Support\Facades\Auth;

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
Auth::routes();

Route::group(['middleware' => ['auth']], function () {
    Route::get('/chat', function() {
        return view('chat');
    })->name('chat');
    
    Route::get('/getUserLogin', function() {
        return Auth::user();
    });
    
    Route::get('/messages', function() {
        return Message::with('user')->get();
    });
    
    Route::post('/messages', function() {
       $user = Auth::user();
    
      $message = new Message();
      $message->message = request()->get('message', '');
      $message->user_id = $user->id;
      $message->save();

      broadcast(new App\Events\MessagePosted($message, $user))->toOthers();
    
      return ['message' => $message->load('user')];
    });
    
    Route::get('/home', [App\Http\Controllers\HomeController::class, 'index'])->name('home');
});
