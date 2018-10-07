<?php
/*
Plugin Name: sora-bangumi-player
Plugin URI: #
Description: 给网站添加弹幕播放器
Version: 1.5
Author: Sora
Author URI: http://www.pinnpinn.com/sora/index.html
*/
$session_life_time = 1800; 
session_set_cookie_params($session_life_time); 
session_start();

//不同环境下获取真实的IP
/*static $realip = NULL;
if($realip !== NULL){
	return $realip;
}*/
function sora_bangumi_player_get_ip(){
	//判断服务器是否允许$_SERVER
	if(isset($_SERVER)) {    
		if(isset($_SERVER[HTTP_X_FORWARDED_FOR])) {
			$realip = $_SERVER[HTTP_X_FORWARDED_FOR];
		}elseif(isset($_SERVER[HTTP_CLIENT_IP])) {
			$realip = $_SERVER[HTTP_CLIENT_IP];
		}else{
			$realip = $_SERVER[REMOTE_ADDR];
		}
	} else {
		//不允许就使用getenv获取  
		if(getenv("HTTP_X_FORWARDED_FOR")) {
			$realip = getenv( "HTTP_X_FORWARDED_FOR");
		} elseif(getenv("HTTP_CLIENT_IP")) {
			$realip = getenv("HTTP_CLIENT_IP");
		} else {
			$realip = getenv("REMOTE_ADDR");
		}
	}
	return $realip;
}

function sora_bangumi_player_write_nickname() {
	$sora_bangumi_user_nickname_cookie=$_COOKIE['sora_bangumi_user_nickname'];
		global $current_user;//获取用户名写入cookie
		$sora_bangumi_user_nickname='';
		$sora_bangumi_user_nickname=$current_user->nickname;//获取用户名
		if($sora_bangumi_user_nickname!=''){
			setcookie('sora_bangumi_user_nickname', $sora_bangumi_user_nickname, time()+3600);//获取用户名写入cookie
			$_SESSION['sora_bangumi_user_nickname']=$sora_bangumi_user_nickname;//获取用户名写入session
		}else{
			setcookie('sora_bangumi_user_nickname', 'null', time()-3600);//清除cookie
		}
}
add_action('wp_enqueue_scripts', 'sora_bangumi_player_write_nickname');

function sora_bangumi_player_clean_nickname() {
	setcookie('sora_bangumi_user_nickname', 'null', time()-3600);//清除cookie
	$_SESSION['sora_bangumi_user_nickname']='';//获取用户名写入session
}
add_action('wp_logout','sora_bangumi_player_clean_nickname');


function iframe_Sora($array_url_title) {//短代码要处理的函数
	$sora_url= explode(',',$array_url_title["url"]); //文章url短代码链接,以英文逗号分割
	$sora_name= explode(',',$array_url_title["name"]);//文章短代码名字,以英文逗号分割
	$sora_url_i=  count($sora_url);   //获取数量
	$sora_name_i= count($sora_name);  //获取数量
	$sora_i=($sora_url_i > $sora_name_i) ? $sora_url_i:$sora_name_i; //三元比较
	$sora_html = "";
	for($i=0;$i<$sora_i;$i++){
		global  $sora_html;
		$sora_html .=  '<option value="'.strval($i+1).'" data-video="'.$sora_url[$i].'">'.$sora_name[$i].'</option>'; 
	}
	//$sora_video="/wp-content/plugins/sora-bangumi-player/sora-bangumi-player.php?url=".$sora_url[0]."&token=".$token_final;
	$sora_video="/wp-content/plugins/sora-bangumi-player/sora-bangumi-player.php?url=".$sora_url[0];
	$sora_danmaku_base=md5($sora_url[0]);
	$sora_danmaku_id="pinnpinn".$sora_danmaku_base;
	

return <<<EOF
<script src="/wp-content/plugins/sora-bangumi-player/jquery-3.3.1.min.js"></script>
<script src="/wp-content/plugins/sora-bangumi-player/sora-bangumi-player.js"></script>
<link rel="stylesheet" href="/wp-content/plugins/sora-bangumi-player/sora-bangumi-player.css" type="text/css" media="all">
<div class="sora-bangumi-player">
	<div id="sora-bangumi-player-children">
		<div class="iframe-sora-bangumi-player">
			<iframe id="bangumi-player" class="bangumi-player" border="0" src="$sora_video" allowfullscreen="allowfullscreen" mozallowfullscreen="mozallowfullscreen" msallowfullscreen="msallowfullscreen" oallowfullscreen="oallowfullscreen" webkitallowfullscreen="webkitallowfullscreen" frameborder="no"></iframe>
			<div class="bangumi-danmu">
				<div class="bangumi-watching">
					<span class="bangumi-watching-number" title="游客">?</span>人正在观看,<span class="danmuku-num">0</span>条弹幕
				</div>
				<div class="bangumi-list">
					<div>时间</div>
					<div>弹幕</div>
					<div>用户</div>
				</div>
			<div class="list-overflow"></div>
			</div>
		</div>
		<div class="listSkip">
			<a href="javascript:void(0)" class="sora-bangumi-button-before">上一集</a>
			<a href="javascript:void(0)" class="sora-bangumi-button-under">下一集</a>
			<select id="select" class="sora-bangumi-select">
				$sora_html
			</select>
		</div>
		<script>
			//弹出框
			function alert_back(text) {
				$(".alert_back").html(text).show();
				setTimeout(function () {
					$(".alert_back").fadeOut();
				},1200)
			}
			//秒转分秒
			function formatTime(seconds) {
				return [
				//parseInt(seconds / 60 / 60),
				parseInt(seconds / 60 % 60),
				parseInt(seconds % 60)
				]
				.join(":")
				.replace(/\b(\d)\b/g, "0$1");
			}
			function create_danmuku_list(obj) {
				var size=Object.keys(obj.data).length;
				var danmuku_list_html='';
				for (var i=0;i<size;i++) {
					danmuku_list_html+="<div class=\"danmuku-list\" time=\""+obj.data[i][0]+"\"><li>"+formatTime(obj.data[i][0])+"</li><li title=\""+obj.data[i][4]+"\">"+obj.data[i][4]+"</li><li>"+obj.data[i][3]+"</li></div>";
				}
				$(".list-overflow").append(danmuku_list_html);
				$(".danmuku-num").text(size);
			}
			$.ajax({
				url:"https://api.prprpr.me/dplayer/v3/?id=$sora_danmaku_id",
				success:function (data) {
					create_danmuku_list(data);
					$(".danmuku-list").on("click",function () {
						var iframe_obj=$("iframe#bangumi-player").contentWindow.dp;
						alert(iframe_obj);
						//var iframe_player_dp=document.getElementById('bangumi-player').contentWindow.dp;
						iframe_obj.seek(0);
					})
				}
			})
		</script>
	</div>
</div>
EOF;
}
add_shortcode('sora', 'iframe_sora');
?>
