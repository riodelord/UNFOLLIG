<?php
/**
 * Instagram Tools
 *
 */
class Instagram
{
    function __construct() {
        date_default_timezone_set("Asia/Jakarta");
        $this->nc = "\e[0m";
        $this->white = "\e[37m";
        $this->black = "\e[0;30m";
        $this->blue = "\e[34m";
        $this->lightblue = "\e[1;34m";
        $this->green = "\e[0;32m";
        $this->lighgreen = "\e[1;32m";
        $this->cyan = "\e[0;36m";
        $this->lightcyan = "\e[1;36m";
        $this->red = "\e[0;31m";
        $this->lightred = "\e[1;31m";
        $this->purple = "\e[0;35m";
        $this->lightpurple = "\e[1;35m";
        $this->brown = "\e[0;33m";
        $this->yellow = "\e[33m";
        $this->gray = "\e[0;30m";
        $this->lightgray = "\e[92m";
        $this->orange = "\e[33m";
    }
    public function instagram($ighost = 0, $useragent = 0, $url = 0, $cookie = 0, $data = 0, $httpheader = array(), $proxy = 0, $userpwd = 0, $is_socks5 = 0) {
        $url = $ighost ? 'https://i.instagram.com/api/v1/' . $url : $url;
        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_USERAGENT, $useragent);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
        curl_setopt($ch, CURLOPT_TIMEOUT, 20);
        if($proxy) curl_setopt($ch, CURLOPT_PROXY, $proxy);
        if($userpwd) curl_setopt($ch, CURLOPT_PROXYUSERPWD, $userpwd);
        if($is_socks5) curl_setopt($ch, CURLOPT_PROXYTYPE, CURLPROXY_SOCKS5);
        if($httpheader) curl_setopt($ch, CURLOPT_HTTPHEADER, $httpheader);
        curl_setopt($ch, CURLOPT_HEADER, 1);
        if($cookie) curl_setopt($ch, CURLOPT_COOKIE, $cookie);
        if ($data):
            curl_setopt($ch, CURLOPT_POST, 1);
            curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
        endif;
        $response = curl_exec($ch);
        $httpcode = curl_getinfo($ch);
        if(!$httpcode) return false; else{
            $header = substr($response, 0, curl_getinfo($ch, CURLINFO_HEADER_SIZE));
            $body = substr($response, curl_getinfo($ch, CURLINFO_HEADER_SIZE));
            curl_close($ch);
            return array($header, $body);
        }
    }
    public function generateDeviceId($seed = 0) {
        $volatile_seed = filemtime(__DIR__);
        return 'android-'.substr(md5($seed.$volatile_seed), 16);
    }
    public function generateSignature($data = 0) {
        $hash = hash_hmac('sha256', $data, 'b4946d296abf005163e72346a6d33dd083cadde638e6ad9c5eb92e381b35784a');
        return 'ig_sig_key_version=4&signed_body='.$hash.'.'.urlencode($data);
    }
    public function generate_useragent($sign_version = '12.0.0.7.91') {
        $resolusi = array('1080x1776','1080x1920','720x1280', '320x480', '480x800', '1024x768', '1280x720', '768x1024', '480x320');
        $versi = array('HM NOTE 1LTE', 'HM 1SW', 'MI 4W', 'Redmi 4','Redmi 4x','Redmi Note 5','Redmi Note 5A','MI MAX 2','MI 6','Redmi 3','Redmi Note 3');
        $dpi = array('120', '160', '320', '240');
        $ver = $versi[array_rand($versi)];
        return 'Instagram '.$sign_version.' Android ('.mt_rand(10,11).'/'.mt_rand(1,3).'.'.mt_rand(3,5).'.'.mt_rand(0,5).'; '.$dpi[array_rand($dpi)].'dpi; '.$resolusi[array_rand($resolusi)].'; Xiaomi; '.$ver.'; armani; qcom; en_US)';
    }
    public function get_csrftoken() {
        $fetch = $this->instagram('si/fetch_headers/', null, null);
        $header = $fetch[0];
        if (!preg_match('#Set-Cookie: csrftoken=([^;]+)#', $fetch[0], $token)) {
            return json_encode(array('result' => false, 'content' => 'Missing csrftoken'));
        } else {
            return substr($token[0], 22);
        }
    }
    public function generateUUID($type = 0) {
        $uuid = sprintf(
            '%04x%04x-%04x-%04x-%04x-%04x%04x%04x',
            mt_rand(0, 0xffff),
            mt_rand(0, 0xffff),
            mt_rand(0, 0xffff),
            mt_rand(0, 0x0fff) | 0x4000,
            mt_rand(0, 0x3fff) | 0x8000,
            mt_rand(0, 0xffff),
            mt_rand(0, 0xffff),
            mt_rand(0, 0xffff)
        );
        return $type ? $uuid : str_replace('-', '', $uuid);
    }
    public function curl($url, $data = 0, $header = 0, $useragent = 0, $cookie = 0){
        $c = curl_init();
        curl_setopt($c, CURLOPT_URL, $url);
        curl_setopt($c, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($c, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($c, CURLOPT_FOLLOWLOCATION, 1);
        if(!$header == 0){
            curl_setopt($c, CURLOPT_HTTPHEADER, $header);
        }
        if(!$data == 0){
            curl_setopt($c, CURLOPT_POSTFIELDS, $data);
        }
        curl_setopt($c, CURLOPT_HEADER, 1);
        if(!$cookie == 0){
            curl_setopt($c, CURLOPT_COOKIE, $cookie);
        }
        if(!$useragent == 0){
            curl_setopt($c, CURLOPT_USERAGENT, $useragent);
        }
        $response = curl_exec($c);
        $header = substr($response, 0, curl_getinfo($c, CURLINFO_HEADER_SIZE));
        $body = substr($response, curl_getinfo($c, CURLINFO_HEADER_SIZE));
        curl_close($c);
        return array($header, $body);
    }
    public function getCookie($username, $password) {
        $useragent = $this->generate_useragent();
        $devid = $this->generateDeviceId();
        $guid = $this->generateUUID();
        $a = $this->instagram(1, $useragent, 'accounts/login/', 0, $this->generateSignature('{"device_id":"'.$devid.'","guid":"'.$guid.'","username":"'.$username.'","password":"'.$password.'","Content-Type":"application/x-www-form-urlencoded; charset=UTF-8"}'));
        $header = $a[0];
        $data = $a[1];
        preg_match_all('%Set-Cookie: (.*?);%',$header,$d);$cookie = '';
        for($o=0;$o<count($d[0]);$o++)$cookie.=$d[1][$o].";";
        $match = preg_match_all('/urlgen="(.*?)";/', $cookie, $match) ? $match[0][0] : null;
        $cookie = str_replace($match, '', $cookie);
        $data = str_replace('"ok"}', '"ok","useragent":"'.$useragent.'","cookie":"'.$cookie.'"}', $data);
        return $data;
    }
    public function postLike($media_id, $useragent, $cookie) {
        $like = $this->instagram(1, $useragent, 'media/'.$media_id.'/like/', $cookie, $this->generateSignature('{"media_id":"'.$media_id.'"}'));
        return $like[1];
    }
    public function postComment($media_id, $text, $useragent, $cookie) {
        $comment = $this->instagram(1, $useragent, 'media/'.$media_id.'/comment/', $cookie, $this->generateSignature('{"comment_text":"'.$text.'"}'));
        return $comment[1];
    }
    public function postFollow($user_id, $useragent, $cookie) {
        $follow = $this->instagram(1, $useragent, 'friendships/create/'.$user_id.'/', $cookie, $this->generateSignature('{"user_id":"'.$user_id.'"}'));
        return $follow[1];
    }
    public function postUnFollow($user_id, $useragent, $cookie) {
        $unfollow = $this->instagram(1, $useragent, 'friendships/destroy/'.$user_id.'/', $cookie, $this->generateSignature('{"user_id":"'.$user_id.'"}'));
        return $unfollow[1];
    }
    public function Dashboard() {
        echo "---------------------------------------------\n";
        echo "".$this->yellow."Instagram".$this->white." Tools\n";
        echo "Copyright © 2018 ".$this->Red."Riodelord".$this->white."\n";
        echo "---------------------------------------------\n";
        echo " -> 1. ".$this->lighgreen."Unfollow Not Follback".$this->white."\n";
        echo " -> 2. ".$this->lighgreen."Unfollow All".$this->white."\n";
        echo "\nSelect option : ".$this->lighgreen."";
        $option = trim(fgets(STDIN));
        echo "".$this->white."\n";
        if($option == '1'){
            $this->ViewUnfollowNotFollback();
        }else if($option == '2'){
            $this->ViewUnfollowAll();
        }else{
            $this->Dashboard();
        }
    }
    public function ViewUnfollowNotFollback() {
        echo "---------------------------------------------\n";
        echo "Unfollow Not Follback Login\n";
        echo "---------------------------------------------\n";
        echo "".$this->lighgreen."Username : ".$this->white;
        $username = trim(fgets(STDIN));
        echo "".$this->lighgreen."Password : ".$this->black;
        $password = trim(fgets(STDIN));
        echo "\n";
        echo "".$this->orange."Please wait checking username/password ...".$this->white;
        $status = json_decode($this->getCookie($username,$password));
        if($status->status == 'ok'){
            echo "\n".$this->orange."Getting cookies...".$this->white;
            $userid = @$status->logged_in_user->pk;
            $username = @$status->logged_in_user->username;
            echo"\n";
            echo"\n";
            echo "".$this->white."---Information----";
            echo"\n";
            $ip = $this->curl('https://www.instabotlike.net/lib/ip.php');
            echo "\nIP : ".$this->orange."".$ip[1]."".$this->white;
            echo "\nStatus : ".$this->lighgreen."True".$this->white;
            echo "\nUserID : ".$userid;
            echo "\nUsername : ".$username;
            echo"\n";
            echo"\n";
            echo "".$this->lighgreen."Delay(in seconds) : ".$this->white;
            $delay = trim(fgets(STDIN));
            echo "\n";
            echo "".$this->white."---Proccess running----";
            echo "\n";
            echo $this->UnfollowNotFollback($userid, $delay, $status->useragent, $status->cookie);
            echo $this->orange."\nProccess complete. Run Again? ".$this->white."y/n";
            echo "\nSelect option : ".$this->lighgreen."";
            $option = trim(fgets(STDIN));
            if($option == 'y'){
                echo $this->white;
                $this->ViewUnfollowNotFollback();
            }else{
                echo $this->white;
                $this->Dashboard();
            }
        }else{
            echo"\n";
            echo "\nError : ".$this->red."Username/password incorret.".$this->white;
            echo"\n";
            echo $this->orange."\nRelogin Unfollow Not Follback? ".$this->white."y/n";
            echo "\nSelect option : ".$this->lighgreen."";
            $option = trim(fgets(STDIN));
            if($option == 'y'){
                echo $this->white;
                $this->ViewUnfollowNotFollback();
            }else{
                echo $this->white;
                $this->Dashboard();
            }
        }
    }
    public function UnfollowNotFollback($user_id, $delay, $useragent, $cookie, $next_max_id = null, $i = 1) {
        $following = $this->instagram(1, $useragent, 'friendships/'.$user_id.'/following?max_id='.$next_max_id, $cookie);
        $obj = json_decode($following[1]);
        @$max_id = $obj->next_max_id;
        $ij = 0;
        foreach ($obj->users as $users){
            $user_id = $users->pk;
            $username = $users->username;
            $check = $this->instagram(1, $useragent, 'friendships/show/'.$user_id.'/', $cookie);
            $cek = json_decode($check[1]);
            if($cek->followed_by == false){
                $ij = $ij + 1;
                echo $this->white."[".date("h:i:s")."]".$this->yellow."[".$ij."]".$this->white." @".$username.$this->red." Gamau Follback Brantas abis".$this->white."\n";
                $unfollow = $this->instagram(1, $useragent, 'friendships/destroy/'.$user_id.'/', $cookie, $this->generateSignature('{"user_id":"'.$user_id.'"}'));
                $obj2 = json_decode($unfollow[1]);
                if($obj2->status == "ok"){
                    echo $this->white."[".date("h:i:s")."] ".$this->gold."Mengunfollow ".$this->white."@".$username.$this->green." success".$this->white."\n";
                }else{
                    echo $this->white."[".date("h:i:s")."] ".$this->gold."Mengunfollow ".$this->white."@".$username.$this->red." failed".$this->white."\n";
                }
                if($ij >= 10){
                    echo "\n".$this->white."[".date("h:i:s")."]".$this->yellow." Delay ".$delay." seconds".$this->white."\n\n";
                    sleep($delay);
                    $ij = 0;
                }
                $i++;
            }else{
                echo $this->white."[".date("h:i:s")."] @".$username.$this->green." Sudah Ngefollback cok!".$this->white."\n";
            }
        }
        if($max_id) {
            $this->UnfollowNotFollback($user_id, $delay, $useragent, $cookie, $max_id, $i);
            exit();
        }
        $i--;
        echo $this->white."[".date("h:i:s")."][".$i."]".$this->yellow." Orang belum follow dan sudah diunfollow!".$this->white."\n";
    }
    public function ViewUnfollowAll() {
        echo "---------------------------------------------\n";
        echo "Unfollow All Login\n";
        echo "---------------------------------------------\n";
        echo "".$this->lighgreen."Username : ".$this->white;
        $username = trim(fgets(STDIN));
        echo "".$this->lighgreen."Password : ".$this->black;
        $password = trim(fgets(STDIN));
        echo "\n";
        echo "".$this->orange."Please wait checking username/password ...".$this->white;
        $status = json_decode($this->getCookie($username,$password));
        if($status->status == 'ok'){
            echo "\n".$this->orange."Getting cookies...".$this->white;
            $userid = @$status->logged_in_user->pk;
            $username = @$status->logged_in_user->username;
            echo"\n";
            echo"\n";
            echo "".$this->white."---Information----";
            echo"\n";
            $ip = $this->curl('https://www.instabotlike.net/lib/ip.php');
            echo "\nIP : ".$this->white."".$ip[1]."".$this->white;
            echo "\nStatus : ".$this->lighgreen."True".$this->white;
            echo "\nUserID : ".$userid;
            echo "\nUsername : ".$username;
            echo"\n";
            echo"\n";
            echo "".$this->lighgreen."Delay(in seconds) : ".$this->white;
            $delay = trim(fgets(STDIN));
            echo "\n";
            echo "".$this->white."---Proccess running----";
            echo "\n";
            echo $this->UnfollowAll($userid, $delay, $status->useragent, $status->cookie);
            echo $this->orange."\nProccess complete. Run Again? ".$this->white."y/n";
            echo "\nSelect option : ".$this->lighgreen."";
            $option = trim(fgets(STDIN));
            if($option == 'y'){
                echo $this->white;
                $this->ViewUnfollowAll();
            }else{
                echo $this->white;
                $this->Dashboard();
            }
        }else{
            echo"\n";
            echo "\nError : ".$this->red."Username/password incorret.".$this->white;
            echo"\n";
            echo $this->orange."\nRelogin Unfollow All? ".$this->white."y/n";
            echo "\nSelect option : ".$this->lighgreen."";
            $option = trim(fgets(STDIN));
            if($option == 'y'){
                echo $this->white;
                $this->ViewUnfollowAll();
            }else{
                echo $this->white;
                $this->Dashboard();
            }
        }
    }
    public function UnfollowAll($user_id, $delay, $useragent, $cookie, $next_max_id = null, $i = 1) {
        $following = $this->instagram(1, $useragent, 'friendships/'.$user_id.'/following?max_id='.$next_max_id, $cookie);
        $obj = json_decode($following[1]);
        @$max_id = $obj->next_max_id;
        $ij = 0;
        foreach ($obj->users as $users){
            $user_id = $users->pk;
            $username = $users->username;
            $ij = $ij + 1;
            $unfollow = $this->instagram(1, $useragent, 'friendships/destroy/'.$user_id.'/', $cookie, $this->generateSignature('{"user_id":"'.$user_id.'"}'));
            $obj2 = json_decode($unfollow[1]);
            if($obj2->status == "ok"){
                echo $this->white."[".date("h:i:s")."] ".$this->gold."Mengunfollow ".$this->white."@".$username.$this->green." unfollow success".$this->white."\n";
            }else{
                echo $this->white."[".date("h:i:s")."] ".$this->gold."Mengunfollow ".$this->white."@".$username.$this->red." unfollow failed".$this->white."\n";
            }
            if($ij >= 10){
                echo "\n".$this->white."[".date("h:i:s")."]".$this->yellow." Delay ".$delay." seconds".$this->white."\n\n";
                sleep($delay);
                $ij = 0;
            }
            $i++;

        }
        if($max_id) {
            $this->UnfollowAll($user_id, $delay, $useragent, $cookie, $max_id, $i);
            exit();
        }
        $i--;
    }
}
$sys = new Instagram();
echo $sys->Dashboard();
