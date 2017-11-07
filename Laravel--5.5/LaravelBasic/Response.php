<?php

请求响应#

创建响应#
字符串 & 数组#
所有路由和控制器都会返回一个响应并发送给用户的浏览器。Laravel 提供了几种不同的方式来返回响应。最基本的响应就是从路由或控制器返回一个字符串。框架会自动将字符串转换为一个完整的 HTTP 响应：
Route::get('/', function () {
    return 'Hello World';
});

除了从路由和控制器返回字符串之外，还可以返回数组。框架也会自动地将数组转为 JSON 响应：
Route::get('/', function () {
    return [1, 2, 3];
});


响应对象#
一般来说，你不会只从路由行为返回简单的字符串或数组。你也许会返回完整的 Illuminate\Http\Response 实例或 视图。
返回完整的 Response 实例允许你自定义响应的 HTTP 状态码和响应头信息
Response 实例继承自 Symfony\Component\HttpFoundation\Response 类，该类提供了各种构建 HTTP 响应的方法：
Route::get('home', function () {
    return response('Hello World', 200)
                  ->header('Content-Type', 'text/plain');
});

为响应增加头信息#
大部分的响应方法都是可链式调用的，使得创建响应实例的过程更具可读性
return response($content)
            ->header('Content-Type', $type)
            ->header('X-Header-One', 'Header Value')
            ->header('X-Header-Two', 'Header Value');

或者，你可以使用 withHeaders 方法来指定要添加到响应的头信息数组：
return response($content)
            ->withHeaders([
                'Content-Type' => $type,
                'X-Header-One' => 'Header Value',
                'X-Header-Two' => 'Header Value',
            ]);


为响应增加 Cookies#
return response($content)
                ->header('Content-Type', $type)
                ->cookie('name', 'value', $minutes);

cookie 方法还接受一些不太频繁使用的参数。通常，这些参数与原生 PHP 的 setcookie 方法的参数有着相同的目的和含义：
->cookie($name, $value, $minutes, $path, $domain, $secure, $httpOnly)

Cookies & 加密#
默认情况下，Laravel 生成的所有 Cookie 都是经过加密和签名，因此不能被客户端修改或读取
如果你想要应用程序生成的部分 Cookie 不被加密，那么可以使用在 app/Http/Middleware 目录中 App\Http\Middleware\EncryptCookies 中间件的 $except 属性
/**
 * 不需要加密的 Cookie 名称。
 *
 * @var array
 */
protected $except = [
    'cookie_name',
];

重定向#
重定向响应是 Illuminate\Http\RedirectResponse 类的实例
并且包含用户需要重定向至另一个 URL 所需的头信息。Laravel 提供了几种方法用于生成 RedirectResponse 实例。其中最简单的方法是使用全局辅助函数 redirect：
Route::get('dashboard', function () {
    return redirect('home/dashboard');
});


有时候你可能希望将用户重定向到之前的位置，比如提交的表单无效时。这时你可以使用全局辅助函数 back 来执行此操作。
由于这个功能利用了 Session，请确保调用 back 函数的路由使用 web 中间件组或所有 Session 中间件：
Route::post('user/profile', function () {
    // 验证请求...

    return back()->withInput();
});



重定向至命名路由#
当你不带参数调用辅助函数 redirect 时，会返回 Illuminate\Routing\Redirector 实例
这个实例允许你调用 Redirector 上的任何方法。例如为命名路由生成 RedirectResponse，可以使用 route 方法：
return redirect()->route('login');

如果你的路由有参数，你可以把它们作为 route 方法的第二个参数来传递：
// 对于具有以下 URI 的路由: profile/{id}

return redirect()->route('profile', ['id' => 1]);


通过 Eloquent 模型填充参数#
如果你要重定向到使用从 Eloquent 模型填充「ID」参数的路由，可以简单地传递模型本身。ID 会被自动提取：
// 对于此路由: profile/{id}

return redirect()->route('profile', [$user]);

如果要自定义路由参数中的值，那么应该覆盖 Eloquent 模型里面的 getRouteKey 方法：
/**
 * 获取模型的路由键.
 *
 * @return mixed
 */
public function getRouteKey()
{
    return $this->slug;
}



重定向至控制器行为#
return redirect()->action('HomeController@index');

如果你的控制器路由需要参数，你可以将它们作为第二个参数传递给 action 方法：
return redirect()->action(
    'UserController@profile', ['id' => 1]
);

重定向并使用闪存的 Session 数据#
可以创建一个 RedirectResponse 的实例并链式调用 with 方法将数据闪存在 Session 中
Route::post('user/profile', function () {
    // 更新用户的信息...

    return redirect('dashboard')->with('status', 'Profile updated!');
});

用户重定向后，你可以从 session 中读取闪存的信息。例如，使用 Blade 语法:
@if (session('status'))
    <div class="alert alert-success">
        {{ session('status') }}
    </div>
@endif

其他响应类型#
使用辅助函数 response 可以用来生成其他类型的响应实例。当不带参数调用辅助函数 response 时，会返回 Illuminate\Contracts\Routing\ResponseFactory 契约 的实例。 契约提供了几种辅助生成响应的方法



视图响应#
如果你需要控制响应的状态和标题，还需要返回 视图 作为响应的内容，则应使用 view 方法：
return response()
            ->view('hello', $data, 200)
            ->header('Content-Type', $type);
// 当然，如果你不需要传递自定义 HTTP 状态码或者自定义头信息，则应该使用全局辅助函数 view。

JSON 响应#
json 方法会自动把 Content-Type 响应头信息设置为 application/json，并使用 PHP函数 json_encode 将给定的数组转换为 JSON：
return response()->json([
    'name' => 'Abigail',
    'state' => 'CA'
]);

如果要创建一个 JSONP 响应，你可以使用 json 方法并与 withCallback 方法配合使用：
return response()
            ->json(['name' => 'Abigail', 'state' => 'CA'])
            ->withCallback($request->input('callback'));

文件下载#
download 方法可以用来生成强制用户浏览器下载指定路径文件的响应
download 方法的第二个参数接受一个文件名，它将作为用户下载的时所看见的文件名。最后，你可以传递一个 HTTP 响应头数组最为该方法的第三个参数：
return response()->download($pathToFile);

return response()->download($pathToFile, $name, $headers);

return response()->download($pathToFile)->deleteFileAfterSend(true);



文件响应#
file 方法可以直接在用户浏览器中显示文件（不是发起下载）
例如图像或者 PDF。此方法接受文件的路径作为其第一个参数和头信息数组作为其第二个参数：
return response()->file($pathToFile);

return response()->file($pathToFile, $headers);

响应宏#
如果要定义可以在各种路由和控制器中重复使用的自定义响应，可以在 Response Facade 上使用 macro 方法
例如，你可以在 服务提供器 的 boot 方法中这样写：
<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Response;

class ResponseMacroServiceProvider extends ServiceProvider
{
    /**
     * 注册应用程序的响应宏。
     *
     * @return void
     */
    public function boot()
    {
        Response::macro('caps', function ($value) {
            return Response::make(strtoupper($value));
        });
    }
}

macro 函数接受一个名称作为其第一个参数，闭包作为第二个参数。当宏名称从 ResponseFactory 实现或者辅助函数 response 调用时，其闭包函数才会被执行：
return response()->caps('foo');
