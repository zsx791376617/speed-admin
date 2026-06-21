<?php
use think\facade\Route;

Route::post('user/login', 'app\api\controller\alpha\User@login');

Route::group(function () {
    Route::get('user/info', 'app\api\controller\alpha\User@info');

    Route::get('avatar/list', 'app\api\controller\alpha\Avatar@list');
    Route::post('avatar/switch', 'app\api\controller\alpha\Avatar@switch');

    Route::get('level/list', 'app\api\controller\alpha\Level@list');
    Route::post('level/settle', 'app\api\controller\alpha\Level@settle');

    Route::post('minigame/stack/settle', 'app\api\controller\alpha\Minigame@stackSettle');
    Route::post('minigame/puzzle/save', 'app\api\controller\alpha\Minigame@puzzleSave');
    Route::post('minigame/puzzle/settle', 'app\api\controller\alpha\Minigame@puzzleSettle');
    Route::get('minigame/data', 'app\api\controller\alpha\Minigame@data');

    Route::get('item/list', 'app\api\controller\alpha\Item@list');
    Route::post('item/use', 'app\api\controller\alpha\Item@use');

    Route::get('stamina/get', 'app\api\controller\alpha\Stamina@get');

    Route::get('vip/info', 'app\api\controller\alpha\Vip@info');
    Route::post('vip/daily_gift', 'app\api\controller\alpha\Vip@dailyGift');

    Route::get('achievement/list', 'app\api\controller\alpha\Achievement@list');
    Route::post('achievement/reward', 'app\api\controller\alpha\Achievement@reward');

    Route::get('collection/list', 'app\api\controller\alpha\Collection@list');

    Route::post('share/create', 'app\api\controller\alpha\Share@create');
    Route::get('share/parse', 'app\api\controller\alpha\Share@parse');
    Route::post('share/help', 'app\api\controller\alpha\Share@help');

    Route::get('ranking/list', 'app\api\controller\alpha\Ranking@list');
    Route::get('ranking/my', 'app\api\controller\alpha\Ranking@myRank');
})->middleware([
    \app\api\middleware\AuthToken::class
]);