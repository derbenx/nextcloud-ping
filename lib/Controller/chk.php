<?php
// v9
// udp scan?
$ip=$_REQUEST['ip']; // #.#.#.#
$pt=$_REQUEST['pt'] ? $_REQUEST['pt'] : '80'; //1-65535 or p for ping
$to=$_REQUEST['to'] ? $_REQUEST['to'] : '.2'; //timeout
if ($ip) {
 if ($pt=='p'){
  //exec("ping -c 1 $ip", $out, $stat);
  $top=$to*1000;
  if (PHP_OS!='WINNT'){
   exec("fping -c1 -t$top  $ip", $out, $stat);
   if (count($out) !== 0) { $tmp='up'; }
   if (count($out) == 0) { $tmp='dn'; }
  } else {
   exec("ping -n 1 -w $top  $ip", $out, $stat);
   $tmp= strpos($out[2],"timed") !== false ? 'dn' :'up';
  }
  //print_r($out);
  //print_r($stat);
  //$tmp= strpos($out[4]," 0%") !== false ? 'up' :'dn';
  echo "$ip ping $tmp";
 } else {
  $st = microtime(true);
  //$fs = fsockopen("udp://127.0.0.1", 13, $errno, $errstr);
  $fs = fsockopen($ip, $pt, $errno, $errstr, $to+.1);
  $fn = microtime(true);
  $tm = $fn-$st;
  //!$fs ? " dn" : " up"
  
  if ($fs) { $tmp='op'; }
  if (!$fs) {
   if ($tm<$to) { $tmp='cl'; } //closed, host up
   else { $tmp='dn'; } //down
  }
  echo "$ip $pt $tmp >> $tm $to";
  fclose($fs);
 }
} else {
 echo 'no data';
}
?>