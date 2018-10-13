<?php
/**
 * Created by PhpStorm.
 * User: pc
 * Date: 2018/10/12
 * Time: 12:32
 */

$client = new SoapClient('https://www.bellecat.com/api/soap/?wsdl');

// If somestuff requires API authentication,
// then get a session token
$session = $client->login('analytics', 'ssI3wz%CZb5ZHfJ7kk*h3anp7Luu1UCz');

$result = $client->call($session, 'sales_order.info', 'test');
var_dump($result);

// If you don't need the session anymore
$client->endSession($session);