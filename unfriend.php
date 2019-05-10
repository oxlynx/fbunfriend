<?php
set_time_limit(0);
require_once('color.php');

function friendlist($token){
	$a = json_decode(file_get_contents('https://graph.facebook.com/me/friends?access_token='.$token), true);
	return $a['data'];
}
function last_active($id, $tok){
	$a = json_decode(file_get_contents('https://graph.facebook.com/'.$id.'/feed?access_token='.$tok.'&limit=1'), true);
	$date = $a['data'][0]['created_time'];
	$aa = strtotime($date);
	return date('Y', $aa);
}

function unfriend($id, $token){
	$url = 'https://graph.facebook.com/me/friends?uid='.$id.'&access_token='.$token;
	$ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "DELETE");
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    $result = curl_exec($ch);
    curl_close($ch);
	if($result == true){
		$unf = Console::green('[HAPUS SU]');
	} else {
		$unf = Console::red('[KOK GAGAL SU]');
	}
	return $unf;
}

echo Console::cyan("     Facebook Auto Unfriend\n");
echo Console::purple("        Author OxLynx\n\n");

//INPUT
echo "Facebook token : ";
$fbtoken = trim(fgets(STDIN));
echo "Tahun : ";
$year = trim(fgets(STDIN));
echo "\n";

$count = 0;
$FL = friendlist($fbtoken);
$totalFL = count($FL);
foreach($FL as $list){
	$count++;
	$name = $list['name'];
	$id = $list['id'];
	$date = last_active($id, $fbtoken);
	echo Console::cyan("(" .$count. "/" .$totalFL. ")");
	if($date < $year){
		echo Console::red('[OFF CUK]').' '.$name.' ~ '.$date.' '.unfriend($id, $fbtoken);
		echo "\r\n";
	}else{
		echo Console::green('[AMAN BRO]').' '.$name.' ~ '.$date;
		echo "\r\n";
	}
}
