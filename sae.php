<?php
/**
 * Created by PhpStorm.
 * User: Leon
 * Date: 15/12/16
 * Time: 上午12:30
 */
/* 在SAE的Storage中新建的Domain名，比如“wordpress”*/
define('SAE_STORAGE',wordpress);
/* 设置文件上传的路径和文件路径的URL，不要更改 */
define('SAE_DIR', 'saestor://'.SAE_STORAGE.'/uploads');
define('SAE_URL', 'http://'.$_SERVER['HTTP_APPNAME'].'-'.SAE_STORAGE.'.stor.sinaapp.com/uploads');
?>
