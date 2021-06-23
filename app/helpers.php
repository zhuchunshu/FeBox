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

use Illuminate\Support\Arr;
use Illuminate\Support\Str;
use Hyperf\View\RenderInterface;
use App\CodeFec\Menu\MenuInterface;
use Hyperf\Utils\ApplicationContext;
use Illuminate\Support\Facades\File;
use Hyperf\Contract\SessionInterface;
use Hyperf\HttpServer\Contract\ResponseInterface;

function public_path($path = ''): string
{
    if ($path != '') {
        return config('server.settings.document_root') . '/' . $path;
    }
    return config('server.settings.document_root');
}

if (!function_exists('mix_manifest')) {
    function mix_manifest()
    {
        return file_get_contents(public_path('mix-manifest.json'));
    }
}

if (!function_exists('mix')) {
    function mix($path)
    {
        $list = mix_manifest();
        $result = json_decode($list, true);
        if (Arr::has($result, '/' . $path)) {
            return $result['/' . $path];
        }
        return null;
    }
}

if (!function_exists("arr_has")) {
    function arr_has($array, $keys)
    {
        return Arr::has($array, $keys);
    }
}

/**
 * 容器实例
 */
if (!function_exists('container')) {
    function container()
    {
        return ApplicationContext::getContainer();
    }
}

/**
 * redis 客户端实例
 */
if (!function_exists('redis')) {
    function redis()
    {
        return container()->get(Redis::class);
    }
}

/**
 * server 实例 基于 swoole server
 */
if (!function_exists('server')) {
    function server()
    {
        return container()->get(ServerFactory::class)->getServer()->getServer();
    }
}

/**
 * websocket frame 实例
 */
if (!function_exists('frame')) {
    function frame()
    {
        return container()->get(Frame::class);
    }
}

/**
 * websocket 实例
 */
if (!function_exists('websocket')) {
    function websocket()
    {
        return container()->get(WebSocketServer::class);
    }
}

/**
 * 缓存实例 简单的缓存
 */
if (!function_exists('cache')) {
    function cache()
    {
        return container()->get(Psr\SimpleCache\CacheInterface::class);
    }
}

/**
 * 控制台日志
 */
if (!function_exists('stdLog')) {
    function stdLog()
    {
        return container()->get(StdoutLoggerInterface::class);
    }
}

/**
 * 文件日志
 */
if (!function_exists('logger')) {
    function logger()
    {
        return container()->get(LoggerFactory::class)->make();
    }
}

if (!function_exists('response')) {
    function response()
    {
        return container()->get(ResponseInterface::class);
    }
}

if (!function_exists("request")) {
    function request()
    {
        return new Hyperf\HttpServer\Request();
    }
}

if (!function_exists("path_class")) {
    function path_class()
    {
        $path = request()->path();
        $result = str_replace("/", "-", $path);
        $result = Str::before($result, '.');
        if ($result == "-") {
            return "main";
        }
        return $result;
    }
}

if (!function_exists("menu")) {
    function menu()
    {
        $container = \Hyperf\Utils\ApplicationContext::getContainer();
        return $container->get(MenuInterface::class);
    }
}

if (!function_exists("view")) {
    function view(string $view, array $data = [])
    {
        $container = \Hyperf\Utils\ApplicationContext::getContainer();
        return $container->get(RenderInterface::class)->render($view, $data);
    }
}

if (!function_exists("menu_pd")) {
    function menu_pd($id)
    {
        $i = 0;
        foreach (menu()->get() as $key => $value) {
            if (arr_has($value, "parent_id")) {
                if ($value['parent_id'] == $id) {
                    $i++;
                }
            }
        }
        return $i;
    }
}

if (!function_exists("menu_pdArr")) {
    function menu_pdArr($id)
    {
        $arr = [];
        foreach (menu()->get() as $key => $value) {
            if (arr_has($value, "parent_id")) {
                if ($value['parent_id'] == $id) {
                    $arr[] = $value;
                }
            }
        }
        return $arr;
    }
}

if (!function_exists("Json_Api")) {
    function Json_Api(int $code = 200, bool $success = true, array $result = [])
    {
        return [
            "code" => $code,
            "success" => $success,
            "result" => $result
        ];
    }
}

if (!function_exists("session")) {
    function session()
    {
        $container = \Hyperf\Utils\ApplicationContext::getContainer();
        return $container->get(SessionInterface::class);
    }
}

// 获取目录下的所有文件夹
if (!function_exists("getPath")) {
    function getPath($path)
    {
        if (!is_dir($path)) {
            return false;
        }
        $arr = array();
        $data = scandir($path);
        foreach ($data as $value) {
            if ($value != '.' && $value != '..') {
                $arr[] = $value;
            }
        }
        return $arr;
    }
}

if (!function_exists("plugin_path")) {
    function plugin_path($path = null)
    {
        if (!$path) {
            return BASE_PATH . "/app/Plugins";
        }
        return BASE_PATH . "/app/Plugins/" . $path;
    }
}

if (!function_exists("read_file")) {
    function read_file($file_path)
    {
        if (file_exists($file_path)) {
            $str = File::get($file_path);
            return $str;
        } else {
            return null;
        }
    }
}

if (!function_exists("read_plugin_data")) {
    /**
     * 读取插件data.json文件
     *
     * @param string 插件目录名 $name
     */
    function read_plugin_data(string $name, $bool = true)
    {
        if ($bool === true) {
            return json_decode(@read_file(plugin_path($name . "/data.json")));
        } else {
            return json_decode(@read_file(plugin_path($name . "/data.json")), true);
        }
    }
}

if(!function_exists("admin_abort")){
    function admin_abort($array){
        if(request()->isMethod("POST") or request()->input("data")=="json"){
            return response()->json(Json_Api(403,false,$array));
        }
        return view('admin.error');
    }
}