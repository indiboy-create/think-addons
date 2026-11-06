# youvision/think-addons

面向 ThinkPHP 8 的插件（Addons）扩展包，提供插件发现、钩子注册、独立路由、插件配置、视图渲染、地址生成与伪静态等基础能力。

插件代码位于项目根目录的 `addons/` 下，按目录隔离，使用 `addons\{插件名}\...` 命名空间，业务项目与插件之间保持低耦合。

## 特性

- 插件目录自动发现，`Plugin.php` 中的钩子方法可自动注册
- 插件路由独立注册，默认仅在 `index` 应用中生效
- 插件配置双层存储：`config.php` 提供结构与默认值，`addon_config` 数据表保存用户值
- `addon_url()` 统一生成插件地址，支持伪静态规则与原生路由回退
- 控制器基类 `think\addons\Controller` 内置跳转、响应、视图能力
- 支持插件级 `route.php`、`middleware.php`、`service.json`、`fun.php`、`command.php`、`menu.php`
- `addon_config` 表不存在时自动降级为纯文件配置，不影响已有插件

## 环境要求

- PHP >= 8.0.0
- ThinkPHP ^8.0
- `topthink/think-view` ^2.0
- `topthink/think-helper` ^3.0

## 安装

```bash
composer require youvision/think-addons
```

安装后：

- `think\addons\Service` 通过 composer 的 `extra.think.services` 自动注册
- 默认配置由包内 `src/config.php` 提供，项目可在 `config/addons.php` 中覆盖
- 首次访问时自动创建项目根目录下的 `addons/` 目录

> 手工复制到 `vendor` 的部署方式，请保持目录名为 `vendor/youvision/think-addons`。语言包与默认配置按该路径读取。

## 快速开始

### 1. 创建插件目录

在项目根目录的 `addons/` 下创建插件目录，目录名即插件标识：

```text
addons/test/
├─ Plugin.php          # 插件入口类，必须存在
├─ info.json           # 插件基础信息，必须存在
├─ config.php          # 插件配置，可选
├─ controller/         # 控制器目录
│  └─ Index.php
├─ view/               # 模板目录
│  └─ info.html
└─ ...
```

### 2. 编写 info.json

```json
{
  "name": "test",
  "title": "插件测试",
  "description": "ThinkPHP 8 插件测试",
  "website": "https://github.com/youvision/think-addons",
  "status": "1",
  "is_admin": "0",
  "is_index": "1",
  "install": "1",
  "author": "youvision",
  "version": "1.0.0"
}
```

常用字段：

| 字段 | 说明 |
|------|------|
| `name` | 插件标识，建议与目录名一致 |
| `title` | 插件名称 |
| `description` | 插件描述 |
| `website` | 插件站点 |
| `status` | 启用状态，`1` 启用、`0` 禁用；禁用后插件路由返回 404 |
| `is_admin` | 是否在后台显示，由宿主系统读取 |
| `is_index` | 是否在前台显示，由宿主系统读取 |
| `install` | 安装状态，由宿主系统读取 |
| `author` | 作者 |
| `version` | 插件版本 |

可选的版本兼容性声明字段：

| 字段 | 说明 |
|------|------|
| `require_php` | 插件要求的最低 PHP 版本 |
| `require_crm` | 插件要求的最低宿主系统版本 |
| `max_crm` | 插件兼容的最高宿主系统版本 |
| `require_framework` | 插件要求的最低插件框架版本，对比 `think\addons\Version::VERSION` |

以上字段均为可选，未声明时跳过对应检查。兼容性检查由宿主项目在插件安装、升级、启用时执行，本扩展包只提供版本标记，不包含校验逻辑。

### 3. 编写 Plugin.php

```php
<?php

declare(strict_types=1);

namespace addons\test;

use think\Addons;

class Plugin extends Addons
{
    public function install()
    {
        return true;
    }

    public function uninstall()
    {
        return true;
    }

    /**
     * testhook 钩子的实现
     */
    public function testhook($params)
    {
        // 钩子参数
        // print_r($params);

        // 插件配置
        // print_r($this->getConfig());

        // 渲染插件模板，模板文件默认位于插件 view 目录
        return $this->fetch('info');
    }
}
```

说明：

- `install()` 与 `uninstall()` 是抽象方法，插件必须实现
- `addons.autoload` 为 `true`（默认）时，`Plugin` 中除 `think\Addons` 基类方法外的 `public` 方法会自动注册为钩子
- 也可以关闭自动注册，在 `config/addons.php` 的 `hooks` 中手动配置插件与钩子的映射

### 4. 插件配置（可选）

在插件目录创建 `config.php`，声明配置项结构与默认值：

```php
<?php

declare(strict_types=1);

return [
    'rewrite' => [
        'title'  => '伪静态',
        'type'   => 'array',
        'value'  => [
            // 键 = URL 规则模板，值 = 目标地址「插件/控制器/操作」
            '/'          => 'test/index/index',
            '/link'      => 'test/index/link',
            '/link/<id>' => 'test/index/link',
        ],
        'rule'   => 'required',
        'msg'    => '',
        'tip'    => '',
        'ok'     => '',
        'extend' => '',
    ],
];
```

`rewrite` 用于伪静态地址生成，详见下文「地址生成与伪静态」。

### 5. 编写控制器

```php
<?php

declare(strict_types=1);

namespace addons\test\controller;

use think\addons\Controller;

class Index extends Controller
{
    public function index()
    {
        return $this->fetch('info');
    }

    public function link()
    {
        return 'hello link';
    }
}
```

控制器继承 `think\addons\Controller` 后可直接使用 `fetch()`、`display()`、`assign()`、`engine()` 以及统一跳转方法。

### 6. 编写模板

在插件 `view/` 目录创建 `info.html`：

```html
<h1>hello tpl</h1>

<a href="{:addon_url('test/index/link')}">link test</a>
<a href="{:addon_url('test/index/link', ['id' => 1])}">link test with id</a>
```

### 7. 访问插件

默认路由规则为：

```text
/addons/{插件}/{控制器}/{操作}
```

上面的示例可通过 `/addons/test/index/index` 访问，也可通过 `addon_url('test/index/index')` 生成地址。

## 插件扩展文件

插件目录下的以下文件会在插件系统启动时按约定加载：

| 文件 | 作用 | 加载时机 |
|------|------|----------|
| `info.json` | 插件基础信息 | 读取插件信息时 |
| `Plugin.php` | 插件入口类 | 插件发现、钩子注册、安装/卸载时 |
| `config.php` | 插件配置结构与默认值 | 读取插件配置时 |
| `route.php` | 插件自定义路由 | 服务注册阶段 |
| `middleware.php` | 插件路由中间件 | 插件路由注册时 |
| `service.json` | 容器绑定映射 | 服务注册阶段 |
| `fun.php` | 插件函数文件 | 服务注册阶段 |
| `command.php` | 插件命令列表 | 服务注册阶段 |
| `console.php` | 插件命令配置（`commands` 键） | 服务注册阶段 |
| `menu.php` | 插件菜单 | 调用 `get_addons_menu()` 时 |
| `controller/` | 控制器目录 | 路由执行时 |
| `view/` | 模板目录 | 视图渲染时 |

插件命名空间统一为 `addons\{插件名}`，无需额外配置 autoload。

## 配置

包内 `src/config.php` 提供默认配置：

```php
<?php

declare(strict_types=1);

return [
    // 是否自动扫描并注册插件钩子
    'autoload' => true,

    // 手动配置钩子：钩子名 => 插件名（字符串、数组或逗号分隔）
    'hooks' => [],

    // 自定义插件路由
    'route' => [],

    // 预留配置项
    'service' => [],
];
```

如需覆盖默认值，在项目 `config/addons.php` 中返回同结构配置。

### 钩子配置

开启 `autoload` 后通常无需手动配置。关闭自动注册时，使用 `hooks` 显式指定：

```php
return [
    'autoload' => false,
    'hooks' => [
        'testhook' => 'test',
        'anotherhook' => ['test', 'demo'],
    ],
];
```

### 自定义路由

`route` 的键为 URL 规则，值为目标地址 `插件/控制器/操作`：

```php
return [
    'route' => [
        '/test' => 'test/index/index',
    ],
];
```

支持按域名分组：

```php
return [
    'route' => [
        'demo.example.com' => [
            'domain' => 'demo.example.com',
            'rule' => [
                '/info' => 'test/index/info',
            ],
        ],
    ],
];
```

路由注册时会检查插件是否存在且 `status` 为启用；已禁用或不存在的插件会被跳过。

## 钩子

### 自动注册

`autoload` 为 `true` 时，系统扫描 `addons/*/Plugin.php`，将插件类中除基类方法外的公共方法注册为钩子。多个插件实现同一钩子时都会被执行。

### 调用钩子

模板中：

```html
<div>{:hook('testhook', ['id' => 1])}</div>
```

PHP 业务中：

```php
$result = hook('testhook', ['id' => 1]);
```

函数签名：

```php
function hook(string $event, $params = null, bool $once = false);
```

`hook()` 会将所有钩子返回值拼接为字符串返回。`$once = true` 时只执行第一个监听器并返回其结果。

### 插件生命周期事件

| 事件 | 触发时机 |
|------|----------|
| `AddonsInit` | 插件事件加载完成后，对注册了该钩子的插件逐个触发 |
| `addons_begin` | 插件路由参数校验通过后 |
| `addon_module_init` | 插件存在且已启用后 |
| `addons_action_begin` | 插件控制器方法调用前，参数为 `[$instance, $action]` |
| `addon_middleware` | 插件路由中间件执行时 |

## 路由与多应用

### 默认路由

插件路由统一由 `think\addons\Route::execute()` 执行：

```text
/addons/:addon/[:controller]/[:action]
```

执行流程：

1. 校验当前应用是否为 `index`
2. 校验插件、控制器、操作参数是否为空
3. 触发 `addons_begin` 事件
4. 读取插件信息，校验插件是否存在且已启用
5. 触发 `addon_module_init` 事件
6. 尝试伪静态 301 规范化
7. 解析控制器类并实例化
8. 调用控制器方法，调用前触发 `addons_action_begin`

### 多应用隔离

插件路由仅在 `index` 应用中注册和执行。在 `admin`、`api`、`install` 等多应用场景中：

- `admin` 等应用不会加载插件路由
- 通过其他应用访问插件路由会返回 404
- 已禁用或不存在的插件路由会被跳过

如果后台需要调用插件能力，应通过钩子、插件服务或业务层调用，而不是直接访问插件控制器路由。

### 插件自定义路由

插件目录下的 `route.php` 会在服务注册阶段加载，可使用 ThinkPHP 原生路由写法。

## 地址生成与伪静态

### addon_url()

```php
function addon_url($url, $params = [], $domain = false, $suffix = false);
```

示例：

```php
addon_url('test');                          // /addons/test
addon_url('test/index/link');               // /addons/test/index/link
addon_url('test/index/link', ['id' => 1]); // /addons/test/index/link/id/1
addon_url('test/index/link', [], true);     // 带域名的完整地址
```

行为说明：

- 插件名必须显式写在第一段，不依赖当前请求上下文
- 仅传插件名时返回插件目录，便于拼接插件内文件路径
- 参数按「键/值」逐段追加，不做 `rawurlencode`
- 参数值包含 `/`、`?`、`#` 时改走查询串，避免被路由解析成新的键值对
- `$suffix` 为兼容参数，当前不追加 URL 后缀
- 插件配置了 `rewrite` 且规则已注册时优先输出伪静态地址，否则回退原生路由

### 伪静态规则

插件 `config.php` 中的 `rewrite` 结构如下：

```php
return [
    'rewrite' => [
        'title' => '伪静态',
        'type'  => 'array',
        'value' => [
            // 键 = URL 规则模板，支持 <name> 必填段与 [<name>] 可选段
            // 值 = 目标地址「插件/控制器/操作」
            '/'          => 'test/index/index',
            '/link'      => 'test/index/link',
            '/link/<id>' => 'test/index/link',
        ],
        'rule'  => 'required',
    ],
];
```

生成规则：

```php
addon_url('test/index/link');               // /link
addon_url('test/index/link', ['id' => 1]); // /link/1
```

说明：

- 同一目标地址配置多条规则时，占位符越多的规则越优先
- 必填占位符缺参或参数值不符合 `[\w\.]+` 时，该条规则不可用
- 未被占位符使用的参数会追加到查询串
- 规则需同步到项目 `config/addons.php` 的 `route` 后才会注册；同步动作由宿主的插件配置保存/启用流程完成，本包只负责读取与生成
- 生成前会回查 `config('addons.route')`，确认「规则模板 + 目标地址」确实已注册；未注册时回退原生路由，避免生成不可访问的地址

### 301 规范化

插件配置了可用的伪静态规则后，通过原生地址 `/addons/...` 访问的 GET/HEAD 请求会 301 重定向到对应的伪静态地址，使搜索引擎只收录一份规范地址。

- 仅处理 GET/HEAD，POST 等写操作不跳转
- 仅在目标规则已注册且目标路径与当前路径不同时跳转，避免循环重定向
- 伪静态地址直接访问不受影响

## 控制器基类与响应

`think\addons\Controller` 提供：

- `fetch($template, $vars)`：渲染模板
- `display($content, $vars, $config = [], $options = [])`：渲染内容，后两个参数用于兼容旧调用形式
- `assign($name, $value)`：模板变量赋值
- `engine($engine)`：切换模板引擎

开发者模式下，`fetch()` 会在输出内容前注入当前模板文件路径注释，便于定位模板（该能力依赖宿主提供 `resolve_template_path()` 函数）。

### 跳转与响应

`think\addons\Controller` 默认引入 `think\addons\traits\Jump`：

```php
// 成功跳转
$this->success('操作成功', addon_url('test/index/index'), $data, 3);

// 失败跳转
$this->error('参数错误');

// 返回 API 数据
$this->result($data);

// URL 重定向
$this->redirect(addon_url('test/index/index'));
```

规则：

- `success()` / `error()` 返回结构为 `code`、`msg`、`data`、`url`、`wait`
- `result()` 返回结构为 `code`、`msg`、`time`、`data`
- HTML 请求渲染跳转模板：优先读取项目 `config/jump.php` 的 `dispatch_success_tmpl` / `dispatch_error_tmpl`，未配置时使用包内 `src/addons/tpl/dispatch_jump.tpl`
- Ajax/JSON 请求直接输出 JSON，不渲染模板；`error()` 在 Ajax 请求下会自动向 `data` 注入 `token`
- 跳转方法通过 `HttpResponseException` 抛出响应，在 `initialize()` 中同样可用

> 插件如果自行重写 `display()`、`success()`、`error()` 等方法，签名不能比基类更窄，否则会产生兼容性错误。

## 插件配置管理

插件配置采用「双层存储」：

| 存储位置 | 作用 |
|----------|------|
| 插件目录 `config.php` | 配置结构定义与默认值 |
| `addon_config` 数据表 | 用户修改后的实际值，优先于文件默认值 |

这样插件升级覆盖 `config.php` 时，用户配置仍可从数据表恢复；插件目录只读时也能正常保存配置。

### 读取配置

```php
// 完整结构，适合后台配置页渲染表单
$config = get_addons_config('test');
echo $config['rewrite']['value'];

// 简化键值映射，适合业务代码直接取值
$values = get_addons_config('test', true);
echo $values['rewrite'];
```

合并规则：

1. 先读取插件 `config.php`
2. 再用 `addon_config` 表中的值覆盖
3. 仅覆盖 `config.php` 中已存在的配置项；数据库中已删除配置项的残留字段会被忽略

### 保存配置

```php
$config = get_addons_config('test');
$config['rewrite']['value'] = '1';
set_addons_config('test', $config);
```

`set_addons_config()` 会：

1. 将完整结构写入插件 `config.php`
2. 将各配置项的 `value` 同步到 `addon_config` 表
3. 清除 `addon_config_{插件名}` 缓存

### 单个配置项读写

```php
// 读：优先数据表，其次文件默认值，最后返回 $default
get_addons_config_value('test', 'rewrite', '0');

// 写：仅写数据表，不修改 config.php
set_addons_config_value('test', 'rewrite', '1');
set_addons_config_value('test', 'options', ['a' => 1, 'b' => '中文']);
```

`set_addons_config_value()` 支持数组值自动 JSON 序列化；`addon_config` 表不存在时返回 `false`。

### 数据表结构

表名实际为 `{前缀}addon_config`，前缀由项目数据库配置决定：

```sql
CREATE TABLE IF NOT EXISTS `addon_config` (
  `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT,
  `addon` varchar(50) NOT NULL DEFAULT '' COMMENT '插件标识名',
  `field` varchar(100) NOT NULL DEFAULT '' COMMENT '配置键',
  `value` text COMMENT '配置值(JSON)',
  `create_time` int(11) NOT NULL DEFAULT 0 COMMENT '创建时间',
  `update_time` int(11) NOT NULL DEFAULT 0 COMMENT '更新时间',
  PRIMARY KEY (`id`),
  UNIQUE KEY `uk_addon_field` (`addon`, `field`),
  KEY `idx_addon` (`addon`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='插件配置表';
```

未创建该表时，所有配置函数自动降级为纯文件模式，不报错，也不影响已有插件。

### 使用建议

| 场景 | 推荐方式 |
|------|----------|
| 插件定义配置项结构与默认值 | 随插件提供 `config.php` |
| 后台配置页整体保存 | `set_addons_config()` |
| 业务代码读取配置 | `get_addons_config($name, true)` 或 `get_addons_config_value()` |
| 运行时记录状态或开关 | `set_addons_config_value()` |
| 卸载插件清理配置 | 删除 `addon_config` 表中对应记录，并清除 `addon_config_{插件名}` 缓存 |

## 公共函数

| 函数 | 说明 |
|------|------|
| `hook($event, $params = null, $once = false)` | 执行插件钩子 |
| `get_addons_info($name)` | 读取插件基础信息 |
| `set_addons_info($name, $array)` | 写入插件基础信息 |
| `get_addons_config($name, $type = false)` | 读取插件配置，`$type=true` 返回简化键值映射 |
| `set_addons_config($name, $array)` | 保存插件配置，文件与数据表双写 |
| `get_addons_db_config($name)` | 读取数据表配置，带缓存 |
| `get_addons_config_value($name, $field, $default = null)` | 读取单个配置项 |
| `set_addons_config_value($name, $field, $value)` | 写入单个配置项，仅写数据表 |
| `get_addons_instance($name)` | 获取插件单例 |
| `get_addons_class($name, $type = 'hook', $class = null)` | 获取插件类名 |
| `addon_url($url, $params = [], $domain = false, $suffix = false)` | 生成插件地址 |
| `get_addons_menu($name)` | 读取插件 `menu.php` |
| `get_addons_list()` | 读取插件列表，带缓存 |

`addon_rewrite_usable()`、`addon_rewrite_fill()`、`addon_rewrite_registered()`、`addon_rewrite_url()` 为伪静态内部辅助函数，业务代码通常只需使用 `addon_url()`。

## 目录结构

项目目录：

```text
www/
├─ addons/                 插件目录
├─ app/                    应用目录
├─ config/                 配置目录
│  └─ addons.php           插件配置（可选，覆盖包内默认值）
├─ public/                 Web 入口目录
├─ route/                  路由定义目录
├─ runtime/                运行时目录
├─ vendor/                 Composer 依赖目录
├─ composer.json
└─ think                   命令行入口
```

插件目录：

```text
addons/test/
├─ Plugin.php              插件入口类
├─ info.json               插件信息
├─ config.php              插件配置
├─ menu.php                插件菜单（可选）
├─ route.php               插件路由（可选）
├─ middleware.php          插件中间件（可选）
├─ service.json            容器绑定（可选）
├─ fun.php                 函数文件（可选）
├─ command.php             命令列表（可选）
├─ console.php             命令配置（可选）
├─ controller/             控制器
├─ view/                   模板
└─ ...
```

## 常见问题

### 访问插件返回 404

- 确认当前应用为 `index`，插件路由只在该应用中注册和执行
- 确认插件 `info.json` 的 `status` 为 `1`
- 确认控制器命名空间为 `addons\{插件名}\controller`，文件名与类名大小写一致
- 确认插件目录名、`info.json` 的 `name` 与命名空间一致
- 修改路由或插件信息后，清除相关缓存再试

### 钩子没有执行

- 确认 `addons.autoload` 为 `true`，或已在 `hooks` 中手动配置
- 确认钩子方法是 `public`，且方法名与调用名一致
- 确认插件 `Plugin.php` 存在并能正常实例化
- 非调试模式下钩子映射会写入 `hooks` 缓存，修改后需清除缓存

### 伪静态不生效

- 确认 `rewrite` 结构正确，值的方向为「规则模板 => 插件/控制器/操作」
- 确认规则已同步到 `config/addons.php` 的 `route`
- 确认目标路径没有被其他插件占用
- 未同步或规则不可用时，`addon_url()` 会回退原生路由，这是预期行为

### 修改配置后没有生效

- 确认保存时调用了 `set_addons_config()` 或 `set_addons_config_value()`
- 确认 `addon_config` 表已创建，且表前缀与项目数据库配置一致
- 清除 `addon_config_{插件名}` 缓存后重试

### 插件列表没有更新

`get_addons_list()` 使用 `addons_list` 缓存。新增、删除插件或修改 `info.json` 后，应清除该缓存。

### 手工部署到 vendor 后路径异常

语言包与默认配置按 `vendor/youvision/think-addons` 读取，手工同步时请保持该目录结构。

## License

Apache-2.0