<?php
/* Creates a ToyyibPay bill for the submitted order and redirects the
   customer to the secure payment page. The order total is recalculated
   here from the trusted price table in config.php. */

require __DIR__ . '/config.php';

if (($_SERVER['REQUEST_METHOD'] ?? 'GET') !== 'POST') {
  fail_page('This page only accepts orders submitted from the checkout.');
}

if ($TOYYIBPAY_SECRET_KEY === 'YOUR_SECRET_KEY_HERE' || $TOYYIBPAY_CATEGORY_CODE === 'YOUR_CATEGORY_CODE_HERE') {
  fail_page('Payment is not configured yet. Please add your ToyyibPay keys in config.php.');
}

/* --- Read and validate the order --- */
$items = json_decode($_POST['order'] ?? '', true);
if (!is_array($items) || count($items) === 0) {
  fail_page('Your bag appears to be empty. Please go back and add an item.');
}

$subtotal = 0;
$lines = [];
foreach ($items as $it) {
  $id   = isset($it['id'])   ? (string) $it['id']   : '';
  $size = isset($it['size']) ? (string) $it['size'] : '';
  $qty  = isset($it['qty'])  ? (int) $it['qty']     : 0;
  if (!isset($PRODUCTS[$id]) || $qty < 1) {
    continue;
  }
  $qty = min($qty, 20);
  $subtotal += $PRODUCTS[$id]['price'] * $qty;
  $lines[] = $PRODUCTS[$id]['name'] . ' (' . preg_replace('/[^A-Za-z0-9]/', '', $size) . ') x' . $qty;
}
if ($subtotal <= 0) {
  fail_page('We could not read your order. Please go back and try again.');
}

/* --- Customer details --- */
$name    = trim($_POST['name']     ?? '');
$phone   = trim($_POST['phone']    ?? '');
$email   = trim($_POST['email']    ?? '');
$address = trim($_POST['address']  ?? '');
$city    = trim($_POST['city']     ?? '');
$post    = trim($_POST['postcode'] ?? '');
$state   = trim($_POST['state']    ?? '');

if ($name === '' || $phone === '' || $email === '' || $address === '') {
  fail_page('Please complete your delivery details and try again.');
}
if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
  fail_page('Please enter a valid email address and try again.');
}

/* --- Shipping and total --- */
$shipping = ($subtotal >= $FREE_SHIP) ? 0 : (in_array($state, $EAST, true) ? $SHIP_EAST : $SHIP_WEST);
$total    = $subtotal + $shipping;
$amountCents = $total * 100;

/* --- Order reference --- */
$orderRef = 'MW' . date('ymd') . '-' . substr(strtoupper(bin2hex(random_bytes(3))), 0, 6);

/* --- Build the ToyyibPay request --- */
$billName = clean_text('MORWELL Order', 30);
$billDesc = clean_text('Order ' . $orderRef . ' ' . implode(' ', $lines), 100);
$base     = $TOYYIBPAY_SANDBOX ? 'https://dev.toyyibpay.com' : 'https://toyyibpay.com';

$fields = [
  'userSecretKey'           => $TOYYIBPAY_SECRET_KEY,
  'categoryCode'            => $TOYYIBPAY_CATEGORY_CODE,
  'billName'                => $billName,
  'billDescription'         => $billDesc,
  'billPriceSetting'        => 1,
  'billPayorInfo'           => 1,
  'billAmount'              => $amountCents,
  'billReturnUrl'           => $RETURN_URL,
  'billCallbackUrl'         => $CALLBACK_URL,
  'billExternalReferenceNo' => $orderRef,
  'billTo'                  => $name,
  'billEmail'               => $email,
  'billPhone'               => $phone,
  'billPaymentChannel'      => '2',   // 0 = FPX, 1 = card, 2 = both
];

$ch = curl_init($base . '/index.php/api/createBill');
curl_setopt_array($ch, [
  CURLOPT_RETURNTRANSFER => true,
  CURLOPT_POST           => true,
  CURLOPT_POSTFIELDS     => http_build_query($fields),
  CURLOPT_TIMEOUT        => 30,
]);
$response = curl_exec($ch);
$curlErr  = curl_error($ch);
curl_close($ch);

if ($response === false) {
  fail_page('We could not reach the payment gateway. Please try again in a moment.');
}

$data = json_decode($response, true);
$billCode = (is_array($data) && isset($data[0]['BillCode'])) ? $data[0]['BillCode'] : '';

if ($billCode === '') {
  fail_page('The payment gateway did not return a bill. Please confirm your ToyyibPay keys and category code are correct.');
}

/* Let the store know an order is on its way (does not block payment). */
notify_store($orderRef, $lines, $subtotal, $shipping, $total, $name, $phone, $email, $address, $city, $post, $state);

/* Send the customer to the secure payment page. */
header('Location: ' . $base . '/' . $billCode);
exit;
