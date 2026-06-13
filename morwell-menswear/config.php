<?php
/* ============================================================
   MORWELL — ToyyibPay configuration
   ------------------------------------------------------------
   Fill in the two values below after you register at
   https://toyyibpay.com (or https://dev.toyyibpay.com for testing).

   1. TOYYIBPAY_SECRET_KEY  — found under your ToyyibPay profile.
   2. TOYYIBPAY_CATEGORY_CODE — create a Category, then copy its code.

   While testing, keep SANDBOX set to true and register a separate
   account at https://dev.toyyibpay.com. When you are ready to take
   real payments, set SANDBOX to false and use your live account keys.
   ============================================================ */

$TOYYIBPAY_SECRET_KEY   = 'YOUR_SECRET_KEY_HERE';
$TOYYIBPAY_CATEGORY_CODE = 'YOUR_CATEGORY_CODE_HERE';
$TOYYIBPAY_SANDBOX      = true;          // true = test mode, false = live payments

/* Where order notifications are emailed. Use an address you check. */
$STORE_EMAIL = 'muhdhazwan90@gmail.com';
/* The "from" address for notification emails. For best delivery, use an
   address on your own hosting domain (for example orders@yourdomain.com). */
$STORE_FROM  = 'MORWELL Store <orders@example.com>';

/* ------------------------------------------------------------
   Product prices (in Ringgit). These must match index.html so the
   total is recalculated on the server and cannot be tampered with
   from the browser. Update both files if you change a price.
   ------------------------------------------------------------ */
$PRODUCTS = [
  'oxford' => ['name' => 'Heritage Oxford Shirt',  'price' => 149],
  'tee'    => ['name' => 'Everyday Heavy T-Shirt', 'price' => 69],
  'stripe' => ['name' => 'Striped Lambswool Knit', 'price' => 179],
  'crew'   => ['name' => 'Cotton Crew Sweater',    'price' => 159],
  'puffer' => ['name' => 'Featherlight Puffer',    'price' => 289],
  'coat'   => ['name' => 'Tailored Wool Coat',     'price' => 359],
];

/* Shipping rules — must match index.html. */
$EAST      = ['Sabah', 'Sarawak', 'Labuan'];
$FREE_SHIP = 250;   // free shipping at or above this subtotal
$SHIP_WEST = 10;
$SHIP_EAST = 15;

/* ------------------------------------------------------------
   The return and callback URLs are worked out automatically from
   the address this folder is served at, so there is nothing to edit.
   ------------------------------------------------------------ */
$scheme  = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') ? 'https' : 'http';
$host    = $_SERVER['HTTP_HOST'] ?? 'localhost';
$dir     = rtrim(str_replace('\\', '/', dirname($_SERVER['SCRIPT_NAME'] ?? '/')), '/');
$baseUrl = $scheme . '://' . $host . $dir;

$RETURN_URL   = $baseUrl . '/return.php';
$CALLBACK_URL = $baseUrl . '/callback.php';

/* ------------------------------------------------------------
   Helper functions
   ------------------------------------------------------------ */

/* ToyyibPay allows only letters, numbers, spaces and underscores in the
   bill name and description. This strips anything else and trims length. */
function clean_text($text, $max) {
  $text = preg_replace('/[^A-Za-z0-9 _]/', ' ', $text);
  $text = preg_replace('/\s+/', ' ', trim($text));
  return substr($text, 0, $max);
}

/* Show a simple, on-brand error page and stop. */
function fail_page($message) {
  http_response_code(400);
  header('Content-Type: text/html; charset=utf-8');
  echo '<!DOCTYPE html><html lang="en"><head><meta charset="UTF-8">'
     . '<meta name="viewport" content="width=device-width, initial-scale=1.0">'
     . '<title>Order could not be processed</title>'
     . '<style>body{font-family:system-ui,sans-serif;background:#ECEAE5;color:#16171A;'
     . 'display:flex;min-height:100vh;align-items:center;justify-content:center;margin:0;padding:24px}'
     . '.box{max-width:460px;background:#F6F4F0;border:1px solid #D7D3CA;border-radius:12px;padding:32px;text-align:center}'
     . 'h1{font-size:1.3rem;margin:0 0 12px}p{color:#6C6A64;line-height:1.55}'
     . 'a{display:inline-block;margin-top:18px;background:#16171A;color:#ECEAE5;padding:12px 22px;'
     . 'border-radius:999px;text-decoration:none;font-weight:600}</style></head><body><div class="box">'
     . '<h1>We could not process your order</h1><p>' . htmlspecialchars($message) . '</p>'
     . '<a href="index.html">Back to the store</a></div></body></html>';
  exit;
}

/* Email the store the full order details (best effort — never blocks payment). */
function notify_store($ref, $lines, $subtotal, $shipping, $total, $name, $phone, $email, $address, $city, $post, $state) {
  global $STORE_EMAIL, $STORE_FROM;
  $body  = "New order (awaiting payment)\n\n";
  $body .= "Reference: $ref\n\n";
  $body .= "Items:\n - " . implode("\n - ", $lines) . "\n\n";
  $body .= "Subtotal: RM$subtotal\nShipping: RM$shipping\nTotal: RM$total\n\n";
  $body .= "Customer:\n$name\n$phone\n$email\n$address, $post $city, $state\n";
  $headers = 'From: ' . $STORE_FROM . "\r\nReply-To: " . $email;
  @mail($STORE_EMAIL, "MORWELL new order $ref (RM$total)", $body, $headers);
}
