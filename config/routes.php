<?php

declare(strict_types=1);
/**
 * CodeFec - Hyperf
 *
 * @link     https://github.com/zhuchunshu
 * @document https://codefec.com
 * @contact  laravel@88.com
 * @license  https://github.com/zhuchunshu/CodeFecHF/blob/master/LICENSE
 */

use App\Controller\AdminController;
use Hyperf\HttpServer\Router\Router;

Router::addRoute(['GET', 'POST', 'HEAD'], '/', 'App\Controller\IndexController@index',[
    "middleware" => [\App\Middleware\AdminMiddleware::class]
]);

Router::get('/test', function () {
    return \App\CodeFec\Header\functions::header()->get();
});

// 后台登陆
Router::addRoute(['GET'], '/admin/login', [AdminController::class,"login"]);
Router::addRoute(['POST'], '/admin/login', [AdminController::class,"loginPost"]);

// 后台路由组
Router::addGroup("/admin",function(){
    Router::get("",[AdminController::class,"index"]);
},[
    "middleware" => [\App\Middleware\AdminMiddleware::class]
]);

