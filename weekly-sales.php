<?php
/**
 * Geckoboard Magento Widgets - Weekly Widget (Sales)
 * Shows the total sales value excluding vat hardcoded so far this week, compared to the same time the previous week
 * 
 * Copyright (c) 2010, Simon Young & previous contributors
 * (Based on Geckoboard support docs here: http://geckoboard.zendesk.com/entries/296250-how-to-magento-ecommerce-widgets-v1-0)
 * 
 * Permission is hereby granted, free of charge, to any person obtaining a copy
 * of this software and associated documentation files (the "Software"), to deal
 * in the Software without restriction, including without limitation the rights
 * to use, copy, modify, merge, publish, distribute, sublicense, and/or sell
 * copies of the Software, and to permit persons to whom the Software is
 * furnished to do so, subject to the following conditions:
 * 
 * The above copyright notice and this permission notice shall be included in
 * all copies or substantial portions of the Software.
 * 
 * DISCLAIMER
 * 
 * THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
 * IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
 * FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
 * AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
 * LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
 * OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN
 * THE SOFTWARE.
 * 
 */

require_once('response.class.php');
require_once('config.php');

$responseObj = new Response();

global $apiKey;

/* Get the total sales so far this week compared to last week */

$ts = date('Y-m-d H:i:s', strtotime('last monday'));
$te = date('Y-m-d H:i:s', mktime(date('H'), date('i'), 0, date('m'), date('d'), date('Y')));

$ys = date('Y-m-d H:i:s', strtotime('-1 week',strtotime('last monday')));
$ye = date('Y-m-d H:i:s', mktime(date('H'), date('i'), 0, date('m'), date('d')-7, date('Y')));

function getSales($start,$finish) {
	
	global $endPoint,$mageUser,$magePass;
	
	$response=0;
	
	//create soap object & then create session id using api user & password from Magento
	$proxy = new SoapClient($endPoint);
	$sessionId = $proxy->login($mageUser,$magePass);
	
	//set filters for the api call
	$filters = array(
	    'created_at' => array("from"=>$start, "to"=>$finish),
	    'status' => array('nin'=>array('On Hold','Canceled','Suspected Fraud','Payment Review'))
	);
	
	//make the call
	$orders = $proxy->call($sessionId, 'sales_order.list', array($filters));
	
	foreach($orders as $order) {
	    $response=$response+$order['grand_total'];
	}
	
	return $response;
}

if (isset($_POST) && isset($_SERVER['PHP_AUTH_USER'])) {
    
    /* Set response format */
    $format = isset($_POST['format']) ? (int)$_POST['format'] : 1;
    $format = ($format == 1) ? 'xml' : 'json';
    $responseObj->setFormat($format);

    /* Check API key */
    if ($apiKey == $_SERVER['PHP_AUTH_USER']) {
        
        $data = array('item' => array(
            array('value' => round((getSales($ts,$te)/117.5)*100), 'text' => 'sales total this week'),
            array('value' => round((getSales($ys,$ye)/117.5)*100), 'text' => 'on last week'),
        ));
        
        $response = $responseObj->getResponse($data);
        echo $response;
    
    } else {
        Header("HTTP/1.1 403 Access denied");
        $data = array('error' => 'Access denied.');
        echo $responseObj->getResponse($data);
    }

} else {
    
    Header("HTTP/1.1 404 Page not found");

}

?>