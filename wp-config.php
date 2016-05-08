<?php
/**
 * WordPress基础配置文件。
 *
 * 这个文件被安装程序用于自动生成wp-config.php配置文件，
 * 您可以不使用网站，您需要手动复制这个文件，
 * 并重命名为“wp-config.php”，然后填入相关信息。
 *
 * 本文件包含以下配置选项：
 *
 * * MySQL设置
 * * 密钥
 * * 数据库表名前缀
 * * ABSPATH
 *
 * @link https://codex.wordpress.org/zh-cn:%E7%BC%96%E8%BE%91_wp-config.php
 *
 * @package WordPress
 */

// ** MySQL 设置 - 具体信息来自您正在使用的主机 ** //
/** WordPress数据库的名称 */
define('DB_NAME', 'leon_blog');

/** MySQL数据库用户名 */
define('DB_USER', 'root');

/** MySQL数据库密码 */
define('DB_PASSWORD', 'leonme');

/** MySQL主机 */
define('DB_HOST', 'localhost');

/** 创建数据表时默认的文字编码 */
define('DB_CHARSET', 'utf8mb4');

/** 数据库整理类型。如不确定请勿更改 */
define('DB_COLLATE', '');

define('WP_USE_MULTIPLE_DB', true);

/**#@+
 * 身份认证密钥与盐。
 *
 * 修改为任意独一无二的字串！
 * 或者直接访问{@link https://api.wordpress.org/secret-key/1.1/salt/
 * WordPress.org密钥生成服务}
 * 任何修改都会导致所有cookies失效，所有用户将必须重新登录。
 *
 * @since 2.6.0
 */
define('AUTH_KEY',         '^g)3G0jJnb,>l_J#hT=2&sVKYfIpd6~=#7Wm,5IT7q`A<aE8pT^LRAQ8RQ8K>N_1');
define('SECURE_AUTH_KEY',  'jr0px|;2d^lFF[DVQO-$LN2Eqn1&(H`6AGF@z`JM5Hu6V*F8V%:B+ubFfk8?/hOX');
define('LOGGED_IN_KEY',    '.)c~|Bl2K9Fhf`v*_nt>@6iVy4nvawNizOkO03]A6j57iKx4y_:)mg]9.]54LCF=');
define('NONCE_KEY',        'Test5Zi!qx;_DY4%JWtQs^@?|;<i PI&? tXIyk_Dt]2vRO}Oov6w&/Lk&+>9IH%');
define('AUTH_SALT',        '(O1Q+YudO}]0~>zPRlO6GIVsD2v;~H)W0q~|<p-s5}!6+_<=EDkL[^5?Bn4%^5!x');
define('SECURE_AUTH_SALT', 'Sfe/5*b()QX6XJRD!k_jFGq?`|W aSp89V@LX`-=7r3JoT~;sR|?Q{[y+eG:B e=');
define('LOGGED_IN_SALT',   '`Jd@ViunB!f]aNIv5TaA;|$/ek9Tc0e!cys)31Y>x+;XpXM&eA}]1NG9*9~?bHa?');
define('NONCE_SALT',       ':q0AWcDlg-BN4KY;C@M)jZbH4D5q(B$-}jW-3wH5}n9@DP+7)Yg%oObz}TIHTb+N');

/**#@-*/

/**
 * WordPress数据表前缀。
 *
 * 如果您有在同一数据库内安装多个WordPress的需求，请为每个WordPress设置
 * 不同的数据表前缀。前缀名只能为数字、字母加下划线。
 */
$table_prefix  = 'wp_';

/**
 * 开发者专用：WordPress调试模式。
 *
 * 将这个值改为true，WordPress将显示所有用于开发的提示。
 * 强烈建议插件开发者在开发环境中启用WP_DEBUG。
 *
 * 要获取其他能用于调试的信息，请访问Codex。
 *
 * @link https://codex.wordpress.org/Debugging_in_WordPress
 */
define('WP_DEBUG', false);

/**
 * zh_CN本地化设置：启用ICP备案号显示
 *
 * 可在设置→常规中修改。
 * 如需禁用，请移除或注释掉本行。
 */
define('WP_ZH_CN_ICP_NUM', true);

/* 好了！请不要再继续编辑。请保存本文件。使用愉快！ */

/** WordPress目录的绝对路径。 */
if ( !defined('ABSPATH') )
	define('ABSPATH', dirname(__FILE__) . '/');

/** 设置WordPress变量和包含文件。 */
require_once(ABSPATH . 'wp-settings.php');

define("FS_METHOD", "direct");
define("FS_CHMOD_DIR", 0777);
define("FS_CHMOD_FILE", 0777);
?>
