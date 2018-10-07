<?php
//$session_life_time = 3610; 
//session_set_cookie_params($session_life_time); 
//session_id($session id);
session_start();
	
//不同环境下获取真实的IP
/*static $realip = NULL;
if($realip !== NULL){
	return $realip;
}*/
function get_ip(){
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
function create_token() {
	$time=date("Y-m-d_h:i");
	$token_raw=md5($time);
	$token_raw="pinnpinn".base64_encode($token_raw);
	$token_raw_arr=str_split($token_raw);
	$token_raw_lenth=count($token_raw_arr);
	$token="";
	for($num=0;$num<$token_raw_lenth;$num++) {
		$token.=ord($token_raw_arr[$num]);
	}
	return $token;
}

function create_token_final(){
	$t=time();
	$t_token=intval($_SESSION['token_time']);
	if(($t-$t_token)>600 && ($t-$t_token)<3600) {//十分钟之外，一小时之内
		if((date("i")%10)==0 || $_SESSION['token']=="") {
			$token=create_token();
			$_SESSION['token_time']=$t;
			$_SESSION['token']=$token;
		} else {
			$token=$_SESSION['token'];
		}
	} elseif(($t-$t_token)>3600){//超过一小时，重新生成token
		$token=create_token();
		$_SESSION['token_time']=$t;
		$_SESSION['token']=$token;
	} else {//十分钟之内
		$token=$_SESSION['token'];
	}
	return $token;
}

function show_bangumi($video_url,$video_danma){
	echo <<<EOF
	<!DOCTYPE html>
	<html lang="en">
	<head>
		<meta http-equiv="Access-Control-Allow-Origin" content="*" />
		<meta charset="UTF-8">
		<style type="text/css">
			html,body{padding: 0;  
			margin: 0;  
			height: 100%;  
			overflow:hidden;  }
			#dplayer  
			{  
				height: 100%;  
				z-index: 0;  
			} 
		</style>
		<title>Dplayer</title>
	</head>
	<body>
		<script src="./Dplayer/DPlayer.min.js"></script>
		<link rel="stylesheet" href="./Dplayer/DPlayer.min.css" type="text/css" media="all">
		<div id="dplayer"></div>
		<script type="text/javascript">
			const dp = new DPlayer({
				container: document.getElementById('dplayer'),
				screenshot: false,
				video: {
					url: '$video_url',
					pic: '',
					thumbnails: ''
				},
					danmaku: {
					id: '$video_danma',
					api: 'https://api.prprpr.me/dplayer/'
				}
			});
		</script>
	</body>
	</html>
EOF;
}

function show_error_information(){
	echo <<<EOF
	<!DOCTYPE html>
	<html lang="en">
	<head>
		<meta http-equiv="Access-Control-Allow-Origin" content="*" />
		<meta charset="UTF-8">
	</head>
	<body>
		<h1>本視頻鏈接屬於<a style='color:#FF99CC' href='http://www.pinnpinn.com/'>PinnPinn-ACG資源分享站點</a>所有。</h1>
		<h2>若於PinnPinn站內看到此信息可登陸後返回主頁再進入視頻頁面。</h2>
	</body>
	</html>
EOF;
}
	
$sora_bangumi_user_nickname_cookie=$_COOKIE['sora_bangumi_user_nickname'];
//unset($_SESSION['sora_bangumi_request_time']);
//unset($_SESSION['sora_bangumi_user_nickname']);
if(isset($_SESSION['sora_bangumi_user_nickname']) || $_SESSION['sora_bangumi_user_nickname']!=''){//判断用户登录
	if($_SESSION['sora_bangumi_user_nickname']==$sora_bangumi_user_nickname_cookie) {//本地cookies未被修改
		//$token=create_token_final();
		$video_url_base=substr($_SERVER["QUERY_STRING"],4);
		$video_url=$video_url_base."&token=".$token;
		$video_danma_base=md5($video_url_base);
		$video_danma="pinnpinn".$video_danma_base;
		//show_bangumi($video_url,$video_danma);//显示视频
		show_bangumi($video_url_base,$video_danma);//显示视频
	} else {
		show_error_information();//用户已登录，但cookies被修改，显示禁止盗链
	}
} else {
	show_error_information();//用户未登录，显示禁止盗链
}
?>