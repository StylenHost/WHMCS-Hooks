<?php


add_hook('CancellationRequest', 1, function ($vars) {

    $adminUsername = false; // The ticket will be opened by this Admin user. Set false to open the ticket using your own customer. Set to admin username string if using Admin User
    $clientID = $vars['userid'];
    $clientName = getClientName($clientID, $adminUsername);
    $relID = $vars['relid'];
    /*** Unused Variables ***/
    //$reason = $vars['reason'];
    //$type = $vars['type'];
    $productDetails = getProductDetails($clientID, $relID, $adminUsername);
    $productName = $productDetails[0];
    $productSub = $productDetails[1];

    //ticket message
    $message = "Hello {$clientName},\nWe have received your request to cancel the following service:\n\n{$productName}\n\nWe value your business and would love the opportunity to keep your service active. If there is anything we can do such as change the specs or adjust the price to better meet your needs, please let us know.\nThank you!\n";

    $command = 'OpenTicket';
    $postData = array(
        'deptid' => '1',
        'subject' => 'Cancellation Request - '. $productSub,
        'message' => $message,
        'clientid' => $clientID,
        'priority' => 'Low',
        'markdown' => true,
        'serviceid' => $relID,
    );

    $results = localAPI($command, $postData, $adminUsername);
});


function getClientName($id, $adminUsername = false)
{
    $command = 'GetClientsDetails';
    $postData = array(
        'clientid' => $id,
        'stats' => false,
    );

    $results = localAPI($command, $postData, $adminUsername);
    $name = $results['client']['firstname'];
    return $name;
}
function getProductDetails($cid, $pid, $adminUsername = false)
{
    $command = 'GetClientsProducts';
    $postData = array(
        'clientid' => $cid,
        'serviceid' => $pid,
        'stats' => false,
    );

    $results = localAPI($command, $postData, $adminUsername);
    $data = array($results['products']['product'][0]['groupname'] . " - " . $results['products']['product'][0]['domain'], $results['products']['product'][0]['domain']);
    return $data;
}
