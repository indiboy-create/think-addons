# 示例插件

`examples/` 下为可直接复制到项目根 `addons/` 目录的示例插件。

## 使用

```bash
cp -r examples/test addons/test
cp -r examples/demo addons/demo
```

复制后请确认：

- 插件目录名、命名空间、`info.json` 的 `name` 三者一致
- 插件 `info.json` 的 `status` 为 `1`
- 多应用模式下，插件路由通过 `index` 应用访问

## test

最小可运行示例：

- `Plugin.php` 实现 `install` / `uninstall`，并提供 `testhook`
- `controller/Index.php` 提供 `index` / `link`
- `config.php` 定义 `welcome` 配置项
- `view/info.html` 使用 `addon_url()` 生成链接

访问方式：

- `/addons/test/index/index`
- `/addons/test/index/link`
- `/addons/test/index/link/id/1`
- 模板中调用 `{:hook('testhook')}`

## demo

进阶示例，展示更多扩展能力：

- `config.php` 定义 `welcome` 与 `rewrite`
- `service.json` 将 `GreeterContract` 绑定到 `Greeter`
- `command.php` 注册 `demo:hello` 命令
- `menu.php` 提供菜单数据
- `fun.php` 提供 `demo_hello()` 函数
- `route.php` 注册额外路由 `demo/hello`

访问方式：

- `/addons/demo/index/index`
- `/addons/demo/index/link`
- 伪静态 `/demo`、`/demo/link`、`/demo/link/<id>`，需先将 `rewrite` 同步到 `config/addons.php` 的 `route`
- 命令：`php think demo:hello`
- 额外路由：`/demo/hello`

## 项目配置示例

`examples/config/addons.php` 是项目级配置示例，复制到项目 `config` 目录：

```bash
cp examples/config/addons.php config/addons.php
```

该文件用于配置插件系统的全局选项（包括 `jump` 跳转提示模板），不要放进 `addons/` 插件目录。`jump` 模板路径留空时使用包内 `src/addons/tpl/dispatch_jump.tpl`。

## 注意事项

- `rewrite` 需要宿主在保存插件配置或启用插件时同步到 `config/addons.php` 的 `route`；未同步时 `addon_url()` 会回退原生路由
- 全局插件中间件放在项目根 `addons/middleware.php`，不是单个插件目录内
- 示例中的菜单结构与图标仅作演示，实际字段以宿主菜单组件为准