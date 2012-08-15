<?php

require_once('core.php');
require_once('config.php');

$ts = date('Y-m-d H:i:s', strtotime('-1 week'));
$te = date('Y-m-d H:i:s', mktime(date('H'), date('i'), date('s'), date('m'), date('d'), date('Y')));

$ys = date('Y-m-d H:i:s', strtotime('-1 week',strtotime('-1 week')));
$ye = date('Y-m-d H:i:s', mktime(date('H'), date('i'), date('s'), date('m'), date('d')-7, date('Y')));


// if (isset($_POST) && isset($_SERVER['PHP_AUTH_USER'])) {


// 	/* Check API key */
//     if ($apiKey === $_SERVER['PHP_AUTH_USER']) {
		$sales1 = getOrders($ts,$te);
		$count1 = getSoldCount($sales1);
		$total1 = getSoldValue($sales1);

		$sales2 = getOrders($ys,$ye);
		$count2 = getSoldCount($sales2);
		$total2 = getSoldValue($sales2);

		$prefix = '£';

		$currentSales = array("text"=>"Sales last 7 days", "value"=>$total1, "prefix"=>$prefix);
		$previousSales = array("text"=>"on previous", "value"=>$total2);

		$response = array("item"=>array($currentSales,$previousSales));

		$json = json_encode($response);

		//echo $ts . " to " . $te . "\n" . $ys . " to " . $ye . "\n";
		echo $json;        
    
//     } else {
//         Header("HTTP/1.1 403 Access denied");
//         $data = array('error' => 'Nice try, asshole.');
//         echo $data;
//     }

// } else {
// 	Header("HTTP/1.1 404 Page not found");
// }



?>