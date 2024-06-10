<?
$account = $mv -> accounts -> checkAuthorization();
$pages_login = $mv -> pages -> findRecordById(10);
$pages_lk = $mv -> pages -> findRecordById(15);
$pages_req = $mv -> pages -> findRecordById(16);
if($account != null){
    $pages_login -> active = 0;
    $pages_login -> update();
    $pages_lk -> active = 1;
    $pages_lk -> update();
    $pages_req -> active = 1;
    $pages_req -> update();

}
else{   
    $pages_login -> active = 1;
    $pages_login -> update();
    $pages_lk -> active = 0;
    $pages_lk -> update();
    $pages_req -> active = 0;
    $pages_req -> update();
}
?>