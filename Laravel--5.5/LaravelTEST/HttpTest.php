<?php

Laravel 测试之：HTTP 测试#
简介#
<?php

namespace Tests\Feature;

use Tests\TestCase;
use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class ExampleTest extends TestCase
{
    /**
     * 一个基础的测试用例。
     *
     * @return void
     */
    public function testBasicTest()
    {
        $response = $this->get('/');

        $response->assertStatus(200);
    }
}

get 方法会创建一个 GET 请求来请求你的应用，而 assertStatus 方法断言返回的响应是指定的 HTTP 状态码。除了这个简单的断言之外，Laravel 也包含检查响应标头、内容、JSON 结构等各种断言



Session / 认证#
首先，你需要传递一个数组给 withSession 方法来设置 Seesion 数据。这让你在应用程序的测试请求发送之前，先给数据加载 Session 变得简单：
<?php
class ExampleTest extends TestCase
{
    public function testApplication()
    {
        $response = $this->withSession(['foo' => 'bar'])
                         ->get('/');
    }
}

当然，一般使用 Session 时都是用于维持用户的状态，如认证用户。actingAs 辅助函数提供了简单的方式来让指定的用户认证为当前的用户。例如，我们可以使用 模型工厂 来生成并认证用户：
<?php

use App\User;

class ExampleTest extends TestCase
{
    public function testApplication()
    {
        $user = factory(User::class)->create();

        $response = $this->actingAs($user)
                         ->withSession(['foo' => 'bar'])
                         ->get('/');
    }
}

你也可以通过传递 guard 名称作为 actingAs 的第二参数以指定用户通过哪种 guard 来认证:
$this->actingAs($user, 'api')



测试 JSON APIs#
Laravel 也提供了几个辅助函数来测试 JSON APIs 及其响应。例如，json，get，post，put，patch 和 delete 方法可以用于发出各种 HTTP 动作的请求

<?php

class ExampleTest extends TestCase
{
    /**
     * 一个基础的功能测试用例。
     *
     * @return void
     */
    public function testBasicExample()
    {
        $response = $this->json('POST', '/user', ['name' => 'Sally']);

        $response
            ->assertStatus(200)
            ->assertJson([
                'created' => true,
            ]);
    }
}


!!assertJson 方法会将响应转换为数组并且利用 PHPUnit::assertArraySubset 方法来验证传入的数组是否在应用返回的 JSON 中
也就是说，即使有其它的属性存在于该 JSON 响应中，但是只要指定的片段存在，此测试仍然会通过。

验证完全匹配#
如果你想验证传入的数组是否与应用返回的 JSON 完全 匹配，你可以使用 assertExactJson 方法
<?php

class ExampleTest extends TestCase
{
    /**
     * 一个基础的功能测试用例。
     *
     * @return void
     */
    public function testBasicExample()
    {
        $response = $this->json('POST', '/user', ['name' => 'Sally']);

        $response
            ->assertStatus(200)
            ->assertExactJson([
                'created' => true,
            ]);
    }
}



测试文件上传#
Illuminate\Http\UploadedFile 类提供了一个 fake 方法，可用其生成用于测试的模拟文件或图像
将其与 Storage facade 的 fake 方法结合使用，可极大地简化文件上传的测试
例如，你可以结合这两个功能轻松测试头像上传表单：
<?php

namespace Tests\Feature;

use Tests\TestCase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class ExampleTest extends TestCase
{
    public function testAvatarUpload()
    {
        Storage::fake('avatars');

        $response = $this->json('POST', '/avatar', [
            'avatar' => UploadedFile::fake()->image('avatar.jpg')
        ]);

        // 断言文件已存储...
        Storage::disk('avatars')->assertExists('avatar.jpg');

        // 断言文件不存在...
        Storage::disk('avatars')->assertMissing('missing.jpg');
    }
}

自定义模拟文件#
当使用 fake 方法创建文件时，你可以指定图片的宽度、高度和大小，以便更好地测试你的验证规则：
UploadedFile::fake()->image('avatar.jpg', $width, $height)->size(100);

除了创建图片，你还可以使用 create 方法创建任何其他类型的文件：
UploadedFile::fake()->create('document.pdf', $sizeInKilobytes);



可用的断言方法#
Laravel 为你的 PHPUnit 测试提供了各种各样的自定义断言方法。json，get，post，put 和 delete 这些测试方法返回的响应都可以使用这些断言方法：
$response->assertSuccessful();  断言该响应具有成功的状态码。

$response->assertStatus($code); 断言该响应具有指定的状态码。

$response->assertRedirect($uri);    断言该响应被重定向至指定的 URI。

$response->assertHeader($headerName, $value = null);    断言该响应存在指定的标头。

$response->assertCookie($cookieName, $value = null);    断言该响应包含了指定的 Cookie。


$response->assertPlainCookie($cookieName, $value = null);   断言该响应包含了指定的 Cookie（未加密）。


$response->assertSessionHas($key, $value = null);   断言该 Session 包含指定的数据。

$response->assertSessionHasErrors(array $keys, $errorBag = 'default');  断言该 Session 包含指定的字段的错误信息。

$response->assertSessionMissing($key);  断言该 Session 不包含指定的键。

$response->assertJson(array $data); 断言该响应包含指定的 JSON 数据。

$response->assertJsonFragment(array $data); 断言该响应包含指定的 JSON 片段。

$response->assertJsonMissing(array $data);  断言该响应不包含指定的 JSON 片段

$response->assertExactJson(array $data);    断言该响应包含完全匹配指定的 JSON 数据。

$response->assertJsonStructure(array $structure);   断言该响应存在指定的 JSON 结构。

$response->assertViewIs($value);    断言该视图响应的视图名称为指定的值。

$response->assertViewHas($key, $value = null);  断言该视图响应存在指定的数据。