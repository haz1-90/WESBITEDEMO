<?php
/* Server-to-server callback from ToyyibPay. This is called directly by
   ToyyibPay (not the customer's browser) once a payment is settled, so it
   is the reliable place to confirm that an order has been paid.
   status: 1 = success, 2 = pending, 3 = failed. */

require __DIR__ . '/config.php';

$status   = $_POST['status']   ?? '';
$ref      = $_POST['order_id'] ?? ($_POST['refno'] ?? '');
$billCode = $_POST['billcode'] ?? '';
$amount   = $_POST['amount']   ?? '';   // amount in cents

/* Keep a simple log on the server for your records. */
$logLine = date('c') . " status=$status ref=$ref billcode=$billCode amount=$amount" . PHP_EOL;
@file_put_contents(__DIR__ . '/toyyibpay-callback.log', $logLine, FILE_APPEND | LOCK_EX);

/* Email the store when a payment is confirmed. */
if ($status === '1') {
  $ringgit = number_format(((float) $amount) / 100, 2);
  $body  = "A payment has been confirmed.\n\n";
  $body .= "Order: $ref\nBill: $billCode\nAmount: RM$ringgit\n";
  @mail($STORE_EMAIL, "MORWELL payment received: $ref", $body, 'From: ' . $STORE_FROM);
}

http_response_code(200);
echo 'OK';
