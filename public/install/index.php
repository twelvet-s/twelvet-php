<?php

/**
 * TwelveT安装程序
 *
 * 安装完成后建议删除此文件
 * @author L
 * @website https://www.twelvet.cn
 */

// 响应POST请求
if (isset($_SERVER['REQUEST_METHOD']) && $_SERVER['REQUEST_METHOD'] == 'POST') {
    // 应用名称
    define('WEBSITE', "twelvet");

    // 目录分隔符
    define('DS', DIRECTORY_SEPARATOR);

    // 根目录
    define('ROOT_PATH', __DIR__ . DS . '..' . DS . '..' . DS);

    // 应用目录
    define('APP_PATH', ROOT_PATH . 'application' . DS);

    // 配置目录
    define('CONFIG', ROOT_PATH . 'config' . DS);

    // 安装包目录
    define('INSTALL_PATH', './');

    // 数据库配置文件
    define('DB', CONFIG . 'database.php');

    // 后台入口文件
    define('ADMIN', ROOT_PATH . 'public' . DS . 'admin.php');

    // 缓存目录
    define('RUNTIME', APP_PATH . 'runtime');

    // 锁定升级文件
    define('LOCK', INSTALL_PATH . 'install.lock');

    // 检测的目录是否存在(需要检测)
    $checkDirs = [
        'thinkphp',
        'vendor',
        'public' . DS . 'static'
    ];

    // 错误信息
    $errInfo = '';

    // 官网快捷连接
    $link = [
        'qqun'  => "https://jq.qq.com/?_wv=1027&amp;k=487PNBb",
        'gitee' => 'https://gitee.com/L/twelvet/attach_files',
        'home'  => 'https://www.twelvet.net?ref=install',
        'forum' => 'https://forum.twelvet.net?ref=install',
        'doc'   => 'https://doc.twelvet.net?ref=install',
    ];

    // 返回json
    function json(int $code, String $msg)
    {
        header('Content-Type:application/json; charset=utf-8');
        $temp = ['code' => $code, 'msg' => $msg];
        exit(json_encode($temp));
    }

    // 判断是否存在安装锁定文件
    if (is_file(LOCK)) {
        $errInfo = "当前已安装" . WEBSITE . "，如需重新安装，请手动移除public/install/install.lock文件";
    } else {
        if (version_compare(PHP_VERSION, '7.3.0', '<')) {
            $errInfo = "当前版本(" . PHP_VERSION . ")过低，请使用PHP7.3以上版本";
        } else {
            if (!extension_loaded("PDO")) {
                $errInfo = "当前未开启PDO，无法进行安装";
            } else {
                if (!is_writable(DB)) {
                    echo DB;
                    // 判断是否被限制区域
                    $open_basedir = ini_get('open_basedir');
                    if ($open_basedir) {
                        $dirArr = explode(PATH_SEPARATOR, $open_basedir);
                        if ($dirArr && in_array(__DIR__, $dirArr)) {
                            $errInfo = '请将有关本项目的open_basedir关闭后再尝试';
                        }
                    }
                    if (!$errInfo) {
                        $errInfo = '当前config/database.php读写权限不足，无法写入配置信息';
                    }
                } else {
                    $dirArr = [];
                    // 遍历数组中的目录检查是否存在
                    foreach ($checkDirs as $k => $v) {
                        if (!is_dir(ROOT_PATH . $v)) {
                            $errInfo = '当前代码仅包含核心代码，可处于命令行模式执行composer istall自动获取，或前往官网下载完整包或资源包覆盖后再尝试安装，<a href="https://www.twelvet.net/download.html?ref=install" target="_blank">立即前往下载</a>';
                            break;
                        }
                    }
                }
            }
        }
    }

    // 存在错误信息立即阻断执行
    if ($errInfo) {
        json(0, $errInfo);
    } else if (isset($_POST['method']) && !$errInfo) {
        json(1, '允许安装');
    }

    // Host
    $mysqlHostname = isset($_POST['mysqlHost']) ? $_POST['mysqlHost'] : '127.0.0.1';
    // Port
    $mysqlHostport = isset($_POST['mysqlHostport']) ? $_POST['mysqlHostport'] : 3306;
    // 分割端口
    $hostArr = explode(':', $mysqlHostname);
    // 判断是否存在段
    if (count($hostArr) > 1) {
        // 重新赋值地址
        $mysqlHostname = $hostArr[0];
        // 重新赋值端口
        $mysqlHostport = $hostArr[1];
    }
    // 数据库账号密码
    $mysqlUsername = isset($_POST['mysqlUsername']) ? $_POST['mysqlUsername'] : 'root';
    $mysqlPassword = isset($_POST['mysqlPassword']) ? $_POST['mysqlPassword'] : '';
    $mysqlDatabase = isset($_POST['mysqlDatabase']) ? $_POST['mysqlDatabase'] : 'twelvet';
    // 数据表前缀
    $mysqlPrefix = isset($_POST['mysqlPrefix']) ? $_POST['mysqlPrefix'] : 'tl_';

    // 管理员账号密码
    $adminUsername = isset($_POST['adminUsername']) ? $_POST['adminUsername'] : 'admin';
    $adminPassword = isset($_POST['adminPassword']) ? $_POST['adminPassword'] : '123456';
    $adminPasswordConfirmation = isset($_POST['adminPasswordConfirmation']) ? $_POST['adminPasswordConfirmation'] : '123456';
    $adminEmail = isset($_POST['adminEmail']) ? $_POST['adminEmail'] : 'admin@admin.com';

    // 限制账号输入
    if (!preg_match("/^\w{3,12}$/", $adminUsername)) json(0, "用户名只能由3-12位数字、字母、下划线组合");
    if (!preg_match("/^[\S]{3,16}$/", $adminPassword)) json(0, "密码长度必须在6-16位之间，不能包含空格");
    if ($adminPassword !== $adminPasswordConfirmation) json(0, "两次输入的密码不一致");

    try {
        // 获取并检查SQL
        $sql = @file_get_contents(INSTALL_PATH . 'twelvet.sql');
        if (!$sql) throw new Exception("无法读取public/install/twelvet.sql文件，请检查是否有读权限");

        // 替换表前缀
        if ($mysqlPrefix != 'tl_') $sql = str_replace("`tl_", "`{$mysqlPrefix}", $sql);

        // 连接数据库
        $pdo = new PDO("mysql:host={$mysqlHostname};port={$mysqlHostport}", $mysqlUsername, $mysqlPassword, [
            PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
            PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8"
        ]);

        // 检测是否支持innodb存储引擎
        $pdoStatement = $pdo->query("SHOW VARIABLES LIKE 'innodb_version'");
        $result = $pdoStatement->fetch();
        if (!$result) throw new Exception("当前数据库不支持innodb存储引擎，请开启后再重新尝试安装");

        // 传世创建数据库
        $pdo->query("CREATE DATABASE IF NOT EXISTS `{$mysqlDatabase}` CHARACTER SET utf8 COLLATE utf8_general_ci;");
        $pdo->query("USE `{$mysqlDatabase}`");

        // 批量导入SQL
        $pdo->exec($sql);

        // 获取数据库信息
        $config = @file_get_contents(DB);
        // 定义闭包提供使用
        $callback = function ($matches) use ($mysqlHostname, $mysqlHostport, $mysqlUsername, $mysqlPassword, $mysqlDatabase, $mysqlPrefix) {
            $field = ucfirst($matches[1]);
            $replace = ${"mysql{$field}"};
            // 是否采用默认端口
            if ($matches[1] == 'hostport' && $mysqlHostport == 3306) $replace = '';

            return "'{$matches[1]}'{$matches[2]}=>{$matches[3]}Env::get('database.{$matches[1]}', '{$replace}'),";
        };

        // 调用闭包批量替换信息
        $config = preg_replace_callback("/'(hostname|database|username|password|hostport|prefix)'(\s+)=>(\s+)Env::get\((.*)\)\,/", $callback, $config);

        // 写入数据并检测是否写入成功
        $result = @file_put_contents(DB, $config);
        if (!$result) throw new Exception("无法写入数据库信息到config/database.php文件，请检查是否有写权限");

        // 生成随机密码密匙
        $newKey = substr(md5(uniqid(true)), 0, 6);
        // 重新生成密码
        $newPassword = md5(md5($adminPassword) . $newKey);
        $pdo->query("UPDATE {$mysqlPrefix}admin SET username = '{$adminUsername}', email = '{$adminEmail}',password = '{$newPassword}', password_key = '{$newKey}' WHERE username = 'admin'");

        $adminName = '';
        // 存在admin文件将其随机改名
        if (is_file(ADMIN)) {
            $x = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
            $adminName = substr(str_shuffle(str_repeat($x, ceil(10 / strlen($x)))), 1, 10) . '.php';
            rename(ADMIN, ROOT_PATH . 'public' . DS . $adminName);
        }

        // 写入并检查lock文件
        $result = @file_put_contents(LOCK, '此文件存在意义：防止恶意重复安装');
        if (!$result) throw new Exception("无法写入安装锁定到publi/install/install.lock文件，请检查是否有写权限");

        json(1, $adminName);
    } catch (PDOException $e) {
        json(0, $e->getMessage());
    } catch (Exception $e) {
        json(0, $e->getMessage());
    }
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <link rel="shortcut icon" href="/favicon.ico" type="image/x-icon" />
    <title>安装TwelveT</title>
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1, minimum-scale=1">
    <meta name="renderer" content="webkit">

    <style>
        body {
            background: #fff;
            margin: 0;
            padding: 0;
            line-height: 1.5;
            -webkit-font-smoothing: antialiased;
            -moz-osx-font-smoothing: grayscale;
        }

        body,
        input,
        button {
            font-family: 'Source Sans Pro', 'Helvetica Neue', Helvetica, 'Microsoft Yahei', Arial, sans-serif;
            font-size: 14px;
            color: #7E96B3;
        }

        .container {
            max-width: 480px;
            margin: 0 auto;
            padding: 20px;
            text-align: center;
        }

        a {
            color: #18bc9c;
            text-decoration: none;
        }

        a:hover {
            text-decoration: underline;
        }

        h1 {
            margin-top: 0;
            margin-bottom: 10px;
        }

        h2 {
            font-size: 28px;
            font-weight: normal;
            color: #3C5675;
            margin-bottom: 0;
            margin-top: 0;
        }

        form {
            margin-top: 40px;
        }

        .form-group {
            margin-bottom: 20px;
        }

        .form-group .form-field:first-child input {
            border-top-left-radius: 4px;
            border-top-right-radius: 4px;
        }

        .form-group .form-field:last-child input {
            border-bottom-left-radius: 4px;
            border-bottom-right-radius: 4px;
        }

        .form-field input {
            background: #EDF2F7;
            margin: 0 0 1px;
            border: 2px solid transparent;
            transition: background 0.2s, border-color 0.2s, color 0.2s;
            width: 100%;
            padding: 15px 15px 15px 180px;
            box-sizing: border-box;
        }

        .form-field input:focus {
            border-color: #18bc9c;
            background: #fff;
            color: #444;
            outline: none;
        }

        .form-field label {
            float: left;
            width: 160px;
            text-align: right;
            margin-right: -160px;
            position: relative;
            margin-top: 18px;
            font-size: 14px;
            pointer-events: none;
            opacity: 0.7;
        }

        button,
        .btn {
            background: #3C5675;
            color: #fff;
            border: 0;
            font-weight: bold;
            border-radius: 4px;
            cursor: pointer;
            padding: 15px 30px;
            -webkit-appearance: none;
        }

        button[disabled] {
            opacity: 0.5;
        }

        .form-buttons {
            height: 52px;
            line-height: 52px;
        }

        .form-buttons .btn {
            margin-right: 5px;
        }

        #error,
        .error,
        #success,
        .success,
        #warmtips,
        .warmtips {
            background: #D83E3E;
            color: #fff;
            padding: 15px 20px;
            border-radius: 4px;
            margin-bottom: 20px;
        }

        #success {
            background: #3C5675;
        }

        #error a,
        .error a {
            color: white;
            text-decoration: underline;
        }

        #warmtips {
            background: #ffcdcd;
            font-size: 14px;
            color: #e74c3c;
        }

        #warmtips a {
            background: #ffffff7a;
            display: block;
            height: 30px;
            line-height: 30px;
            margin-top: 10px;
            color: #e21a1a;
            border-radius: 3px;
        }
    </style>
</head>

<body>

    <div class="container">
        <h2>安装 TwelveT</h2>
        <div>

            <form method="post">
                <div id="error" style="display:none"></div>
                <div id="success" style="display:none"></div>
                <div id="warmtips" style="display:none"></div>

                <div class="form-group">
                    <div class="form-field">
                        <label>MySQL 数据库地址</label>
                        <input type="text" name="mysqlHost" value="127.0.0.1" required="">
                    </div>

                    <div class="form-field">
                        <label>MySQL 数据库名</label>
                        <input type="text" name="mysqlDatabase" value="twelvet" required="">
                    </div>

                    <div class="form-field">
                        <label>MySQL 用户名</label>
                        <input type="text" name="mysqlUsername" value="root" required="">
                    </div>

                    <div class="form-field">
                        <label>MySQL 密码</label>
                        <input type="password" name="mysqlPassword">
                    </div>

                    <div class="form-field">
                        <label>MySQL 数据表前缀</label>
                        <input type="text" name="mysqlPrefix" value="tl_">
                    </div>

                    <div class="form-field">
                        <label>MySQL 端口号</label>
                        <input type="number" name="mysqlHostport" value="3306">
                    </div>
                </div>

                <div class="form-group">
                    <div class="form-field">
                        <label>管理者用户名</label>
                        <input name="adminUsername" value="admin" required="" />
                    </div>

                    <div class="form-field">
                        <label>管理者Email</label>
                        <input name="adminEmail" value="admin@admin.com" required="">
                    </div>

                    <div class="form-field">
                        <label>管理者密码</label>
                        <input type="password" name="adminPassword" required="">
                    </div>

                    <div class="form-field">
                        <label>重复密码</label>
                        <input type="password" name="adminPasswordConfirmation" required="">
                    </div>
                </div>

                <div class="form-buttons">
                    <button type="submit" disabled>正在检测。。。</button>
                </div>
            </form>

            <!-- jQuery -->
            <script src='https://cdn.staticfile.org/jquery/3.4.1/jquery.js'></script>
            <!-- layer -->
            <script src='https://cdn.staticfile.org/layer/2.3/layer.js'></script>

            <script>
                $(function() {
                    // 定义加载层
                    var loading;
                    // 提交按钮
                    var submit = $(this).find('button');
                    // 判断是否允许安装
                    $.ajax({
                        url: './',
                        type: 'POST',
                        data: {
                            'method': 'auth'
                        },
                        beforeSend: function() {
                            loading = layer.load(1);
                        },
                        success: function(res) {
                            if (res['code'] == 1) {
                                submit.prop('disabled', false).text('立即安装');
                                // 高亮第一个框
                                $('form :input:first').select();
                                // 为按钮绑定事件
                                $('form').on('submit', function(e) {
                                    // 禁止浏览器默认操作
                                    e.preventDefault();
                                    var form = this;
                                    // 发送安装请求
                                    $.ajax({
                                        url: './',
                                        type: 'POST',
                                        data: $(this).serialize(),
                                        beforeSend: function() {
                                            loading = layer.load(1);
                                            submit.text('安装中...').prop('disabled', true);
                                        },
                                        success: function(res) {
                                            if (res['code'] == 1) {
                                                var adminUrl = '';
                                                if(res['msg'] != ''){
                                                    adminUrl = res['msg'];
                                                }
                                                $('#error').hide();
                                                $(".form-group", form).remove();
                                                submit.remove();
                                                $("#success").text("安装成功！开始你的TwelveT之旅吧！").show();

                                                var buttons = $(".form-buttons", form);
                                                $('<a class="btn" href="../">访问首页</a>').appendTo(buttons);

                                                if (adminUrl !== '') {
                                                    $("#warmtips").html('温馨提示：请将以下后台登录入口牢记或手动更改后台地址，为了你的安全，不要泄漏或发送给他人！如有泄漏请及时修改！<br>以及尽快删除public/install目录,日后需重装可前往官网获取安装包<a href="../' + adminUrl + '">' + adminUrl + '</a>').show();
                                                    $('<a class="btn" href="../' + adminUrl + '" id="btn-admin" style="background:#18bc9c">访问后台</a>').appendTo(buttons);
                                                }
                                                localStorage.setItem("twelvetep", "installed");
                                            } else {
                                                layer.alert(res['msg'], {
                                                    skin: 'layui-layer-molv',
                                                    success: function() {
                                                        submit.text('重新安装').prop('disabled', false);
                                                    }
                                                });
                                            }
                                        }
                                    }).then(() => {
                                        layer.close(loading);
                                    })
                                });
                            } else {
                                layer.alert(res['msg'], {
                                    skin: 'layui-layer-molv',
                                    success: function() {
                                        submit.text('禁止安装').prop('disabled', true);
                                    }
                                });
                            }
                        }
                    }).then(() => {
                        layer.close(loading);
                    })
                });
            </script>
        </div>
    </div>

</body>

</html>