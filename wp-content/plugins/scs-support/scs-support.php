<?php
/**
 * @package SCS Support
 * @version 1.0.0
 */
/*
Plugin Name: 新浪云存储SCS Support
Plugin URI: http://blog.gimhoy.com/archives/scs-support.html
Description: 上传附件至新浪云存储(Sina Cloud Storage)。This is a plugin for SCS.
Author: Gimhoy
Version: 1.0.0
Author URI: http://blog.gimhoy.com
*/

if ( !defined('WP_PLUGIN_URL') )
	define( 'WP_PLUGIN_URL', WP_CONTENT_URL . '/plugins' ); //  plugin url

define('SCS_BASENAME', plugin_basename(__FILE__));
define('SCS_BASEFOLDER', plugin_basename(dirname(__FILE__)));
define('SCS_FILENAME', str_replace(SCS_BASEFOLDER.'/', '', plugin_basename(__FILE__)));

// 初始化选项
register_activation_hook(__FILE__, 'scs_set_options');

/**
 * 初始化选项
 */
function scs_set_options() {
    $options = array(
        'bucket' => "",
        'ak' => "",
    	'sk' => "",
		'hiPath'  => "",
    );
    add_option('scs_options', $options, '', 'yes');
}

function scs_admin_warnings() {
    $scs_options = get_option('scs_options', TRUE);
    $scs_bucket = attribute_escape($scs_options['bucket']);
	if ( !$scs_options['bucket'] && !isset($_POST['submit']) ) {
		function scs_warning() {
			echo "
			<div id='scs-warning' class='updated fade'><p><strong>".__('SCS-Support插件已启用.')."</strong> ".sprintf(__('但你还必须 <a href="%1$s">输入你的SCS Bucket及AK/SK</a> 它才能正常工作.'), "options-general.php?page=" . SCS_BASEFOLDER . "/scs-support.php")."</p></div>
			";
		}
		add_action('admin_notices', 'scs_warning');
		return;
	} 
}
scs_admin_warnings();

function scs_upload($data,$useFileUpload=''){
	require_once('SCS.php');
	$scs_options = get_option('scs_options', TRUE);
    $scs_bucket = attribute_escape($scs_options['bucket']);
    $scs_ak = attribute_escape($scs_options['ak']);
    $scs_sk = attribute_escape($scs_options['sk']);
    $bucket = $scs_bucket;
	if(!is_object($scs)) $scs = new SCS($scs_ak, $scs_sk);
	
	$url = "http://sinastorage.com/{$bucket}/";
	$wp_upload_dir = wp_upload_dir();
	$object =  $wp_upload_dir['url'].'/'.basename($data['file']);
	$object = str_replace($url, '', $object);

	if(!$useFileUpload){
		$res = $scs->putObject($data, $bucket, $object, SCS::ACL_PUBLIC_READ);
	}else{
		$file = $wp_upload_dir['path'].'/'.$data['file'];
		$scs->putObjectFile($file, $bucket, $object, SCS::ACL_PUBLIC_READ);
	}
	return $res;
}


/*
 附件上传函数
 Hook 所有上传操作，上传完成后再存到云存储。
 默认设置为 public
*/
function mv_attachments_to_scs($data) {

	if(stripos( $data['type'], 'image/') !== false ){
		//上传缩略图
		add_filter('wp_generate_attachment_metadata', 'mv_thumbnails_to_scs');
	}
	
	scs_upload($data);

	return $data;
}
add_filter('wp_handle_upload', 'mv_attachments_to_scs');
 
/*
 缩略图上传函数
*/
function mv_thumbnails_to_scs($metadata){	
	if (isset($metadata['sizes']) && count($metadata['sizes']) > 0){
		foreach ($metadata['sizes'] as $data)
		{	
			scs_upload($data,true);
		}
	}
	return $data;
}

//hook所有xmlrpc的上传
add_filter( 'xmlrpc_methods', 'xml_to_scs' );
function xml_to_scs($methods) {
    $methods['wp.uploadFile'] = 'xmlrpc_upload';
    $methods['metaWeblog.newMediaObject'] = 'xmlrpc_upload';
    return $methods;
}
function xmlrpc_upload($args){
	$data  = $args[3];
	$object = sanitize_file_name( $data['name'] );
	$type = $data['type'];
	$bits = $data['bits'];
   
	require_once('SCS.php');
    $scs_options = get_option('scs_options', TRUE);
    $scs_bucket = attribute_escape($scs_options['bucket']);
    $scs_ak = attribute_escape($scs_options['ak']);
    $scs_sk = attribute_escape($scs_options['sk']);
    $bucket = $scs_bucket;
	if(!is_object($scs)) $scs = new SCS($scs_ak, $scs_sk);
	
	$url = "http://sinastorage.com/{$bucket}/";
	$wp_upload_dir = wp_upload_dir();
	$object =  $wp_upload_dir['url'].'/'.$object;
	$object = str_replace($url, '', $object);
	$requestHeaders = array('Content-Type' => $type);
	$res = $scs->putObject( $bits, $bucket, $object, SCS::ACL_PUBLIC_READ, array(), $requestHeaders );
	$url = $url.$object; 

	return array( 'file' => $url, 'url' => $url, 'type' => $data['type'] );
}

/*
 删除SCS上的附件
*/
function del_attachments_from_scs($file) {
	require_once('SCS.php');
	$scs_options = get_option('scs_options', TRUE);
	$scs_bucket = attribute_escape($scs_options['bucket']);
	$scs_ak = attribute_escape($scs_options['ak']);
	$scs_sk = attribute_escape($scs_options['sk']);
	$bucket = $scs_bucket;
	if(!is_object($scs)) $scs = new SCS($scs_ak, $scs_sk);
	
	$upload_dir = wp_upload_dir();
	$object = str_replace($upload_dir['basedir'],'',$file);
	$object = ltrim( $object , '/' );
	$object = str_replace('http://sinastorage.com/'.$bucket,'',$object);
	$hiPath = trim(attribute_escape($scs_options['hiPath']),'/');
	if($hiPath) $object = $hiPath.'/'.$object;

	$res = $scs->deleteObject($scs_bucket,$object);
	return $file;
}
add_action('wp_delete_file', 'del_attachments_from_scs');

function format_scs_url($url) {
	$wp_upload_dir = wp_upload_dir();
	$hiPath = attribute_escape($scs_options['hiPath']);
	if($hiPath) $hiPath = '/' . trim($hiPath,'/');

	$scs_url = "http://sinastorage.com/{$bucket}".$hiPath;
	$url = str_replace($wp_upload_dir['path'], $scs_url, $url);
	return $url;
}
add_filter('wp_get_attachment_url', 'format_scs_url');

function scs_plugin_action_links( $links, $file ) {
	if ( $file == plugin_basename( dirname(__FILE__).'/scs-support.php' ) ) {
		$links[] = '<a href="options-general.php?page=' . SCS_BASEFOLDER . '/scs-support.php">'.__('Settings').'</a>';
	}

	return $links;
}
add_filter( 'plugin_action_links', 'scs_plugin_action_links', 10, 2 );

function scs_add_setting_page() {
    add_options_page('SCS Setting', 'SCS Setting', 8, __FILE__, 'scs_setting_page');
}

add_action('admin_menu', 'scs_add_setting_page');

function scs_setting_page() {

	$options = array();
	if($_POST['bucket']) {
		$options['bucket'] = trim(stripslashes($_POST['bucket']));
	}
	if($_POST['ak']) {
		$options['ak'] = trim(stripslashes($_POST['ak']));
	}
	if($_POST['sk']) {
		$options['sk'] = trim(stripslashes($_POST['sk']));
	}
	if($_POST['hiPath']) {
		$hiPath = $_POST['hiPath'];
		$hiPath = trim($hiPath);
		$hiPath = trim(stripslashes($hiPath), "/");
		$options['hiPath'] = $hiPath;
		$scs_url = "http://sinastorage.com/{$_POST['bucket']}/{$hiPath}";
		//update_option('upload_path', $scs_url);
		update_option('upload_url_path', $scs_url);
	}	
	if($options !== array() ){
		update_option('scs_options', $options);
        
?>
<div class="updated"><p><strong>设置已保存！</strong></p></div>
<?php
    }

    $scs_options = get_option('scs_options', TRUE);
    $scs_bucket = attribute_escape($scs_options['bucket']);
    $scs_ak = attribute_escape($scs_options['ak']);
    $scs_sk = attribute_escape($scs_options['sk']);
	$hiPath = attribute_escape($scs_options['hiPath']); 
	$wp_upload_dir = wp_upload_dir();
	$subdir = $wp_upload_dir['subdir'];
?>
<div class="wrap" style="margin: 10px;">
    <h2>新浪云存储 设置</h2> 
	<a href="http://blog.gimhoy.com/archives/scs-support.html" target="_blank">帮助</a>
	<a href="http://blog.gimhoy.com/archives/scs-support.html" target="_blank">反馈建议</a>
	<a href="http://blog.gimhoy.com/archives/scs-support.html" target="_blank">下载最新版本</a>
	<a href="https://me.alipay.com/gimhoy" target="_blank"><span style='color:red'>捐赠</span></a>
    <form name="form1" method="post" action="<?php echo wp_nonce_url('./options-general.php?page=' . SCS_BASEFOLDER . '/scs-support.php'); ?>">
	  	<h3>基本设置</h3>	
        <fieldset>
            <legend>Bucket 设置</legend>
            <input type="text" id="bucket" name="bucket" value="<?php echo $scs_bucket;?>" placeholder="请输入云存储使用的 bucket"/>
            请先访问 <a href="http://open.sinastorage.com/">新浪云存储</a> 创建 bucket 后，填写以上内容
			<p></p>
        </fieldset>
        <fieldset>
            <legend>Access Key / API key</legend>
            <input type="text" name="ak" value="<?php echo $scs_ak;?>" placeholder=""/>
            <p></p>
        </fieldset>
        <fieldset>
            <legend>Secret Key</legend>
            <input type="text" name="sk" value="<?php echo $scs_sk;?>" placeholder=""/>
			访问 <a href="http://open.sinastorage.com/">新浪云存储</a>，获取 AK/SK
        </fieldset>

	   	<h3>上传路径设置</h3>	
		<fieldset>
            <legend>可自定义上传路径</legend>
            <input type="text" id="hiPath" name="hiPath" value="<?php echo $hiPath;?>" placeholder="" onkeyup="pathPreview();"/>	
			<p id= "path_preview"></p>
			<p></p>
			<script type="text/javascript">
				function pathPreview (){
					var path = document.getElementById("hiPath").value;
					var bucket = document.getElementById("bucket").value;
					var subdir = "<?php echo $subdir; ?>"
					document.getElementById("path_preview").innerHTML = "示例：http://sinastorage.com/"+bucket+"/"+path+subdir+"/example.jpg";
				}
			</script>
		</fieldset>	
		
	
        <fieldset class="submit">
            <input type="submit" name="submit" value="保存更新" />
        </fieldset>
    </form>
	<h2>赞助</h2>
		<p>如果你发现这个插件对你有帮助，欢迎<a href="https://me.alipay.com/gimhoy" target="_blank">赞助</a>!</p>
		<p><a href="https://me.alipay.com/gimhoy" target="_blank"><img src="http://archives.gimhoy.cn/archives/alipay_donate.png" alt="支付宝捐赠" title="支付宝" /></a></p>
	<br />
</div>
<?php
}