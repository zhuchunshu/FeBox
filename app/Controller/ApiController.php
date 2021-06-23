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
namespace App\Controller;

use App\Middleware\AdminMiddleware;
use Hyperf\HttpServer\Annotation\AutoController;
use Hyperf\HttpServer\Annotation\Middleware;
use Hyperf\HttpServer\Contract\RequestInterface;
use Illuminate\Support\Arr;

/**
 * @AutoController
 * Class ApiController
 */
class ApiController
{
    public function avatar(RequestInterface $request): array
    {
        $email = $request->input('email');
        return Json_Api(200, true, ['avatar' => 'https://dn-qiniu-avatar.qbox.me/avatar/' . md5($email)]);
    }

    /**
     * @Middleware(AdminMiddleware::class)
     */
    public function menu()
    {
        return Json_Api(200,true,menu()->get());
    }

    public function AdminErrorRedirect(){
        $list = [
            "/admin" => "/admin/login"
        ];
        if(request()->input("path",null)){
            $path = request()->input("path",null);
            if(Arr::has($list,$path)){
                return Json_Api(200,true,["data" => $list[$path]]);
            }else{
                return Json_Api(403,false,["data" => "#"]);
            }
        }else{
            return Json_Api(403,false,["data" => "#"]);
        }
    }
}
