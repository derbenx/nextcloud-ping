<?php
// v10
// udp scan?
$nxc=1; //1=nextcloud, 0=bare
$out='fart';

$ip=$_REQUEST['ip']; // #.#.#.#
$pt=$_REQUEST['pt'] ? $_REQUEST['pt'] : '80'; //1-65535 or p for ping
$to=$_REQUEST['to'] ? $_REQUEST['to'] : '.2'; //timeout
if ($ip) {
 if ($pt=='p'){
  //exec("ping -c 1 $ip", $ot, $stat);
  $top=$to*1000;
  if (PHP_OS!='WINNT'){
   exec("fping -c1 -t$top  $ip", $ot, $stat);
   if (count($ot) !== 0) { $tmp='up'; }
   if (count($ot) == 0) { $tmp='dn'; }
  } else {
   exec("ping -n 1 -w $top  $ip", $ot, $stat);
   $tmp= strpos($ot[2],"timed") !== false ? 'dn' :'up';
  }
  //print_r($ot);
  //print_r($stat);
  //$tmp= strpos($ot[4]," 0%") !== false ? 'up' :'dn';
  $out="$ip ping $tmp";
 } else {
  $st = microtime(true);
   //$fs = fsockopen("udp://127.0.0.1", 13, $errno, $errstr);
  $fs = fsockopen($ip, intval($pt), $errno, $errstr, $to+.1);
   //echo $errno." ".$errstr;
   $fn = microtime(true);
   $tm = $fn-$st;
  if (!$errno) {
   fclose($fs);
   //!$fs ? " dn" : " up"
  
   if ($fs) { $tmp='op'; }
   if (!$fs) {
    if ($tm<$to) { $tmp='cl'; } //closed, host up
    else { $tmp='dn'; } //down
   }
  $out="$ip $pt $tmp >> $tm $to";
  } else {
   $tmp= $errno==110 ? 'dn':'cl';
   $out="$ip $pt $tmp >> $tm $errno $errstr";
  }
 }
} else {
 $out='no data';
}
if (!$out) { $out='pfft'; }
//if regular server
if ($nxc==0){ echo $out; }
?>