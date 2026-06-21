<?php
use think\facade\Route;

Route::group(function () {
    require app_path() . 'api/route/app.php';
});
