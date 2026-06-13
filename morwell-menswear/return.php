<?php
/* The page ToyyibPay returns the customer to after payment.
   status_id: 1 = success, 2 = pending, 3 = failed. */

$status  = $_GET['status_id'] ?? '';
$orderId = preg_replace('/[^A-Za-z0-9\-]/', '', $_GET['order_id'] ?? '');
$billCode = preg_replace('/[^A-Za-z0-9]/', '', $_GET['billcode'] ?? '');

if ($status === '1') {
  $title = 'Thank you for your order';
  $tone  = '#3F7A5E';
  $msg   = 'Your payment was successful. We will confirm your order and send tracking details to your WhatsApp once it has been posted.';
} elseif ($status === '2') {
  $title = 'Your payment is pending';
  $tone  = '#B07A2E';
  $msg   = 'Your payment is still being processed. If you have been charged, we will confirm your order shortly. You do not need to pay again.';
} else {
  $title = 'Your payment was not completed';
  $tone  = '#9B4A4A';
  $msg   = 'Your order was not paid for. No charge has been made. You are welcome to return to the store and try again.';
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title><?php echo htmlspecialchars($title); ?> — MORWELL</title>
<style>
  body{font-family:system-ui,-apple-system,'Segoe UI',sans-serif;background:#ECEAE5;color:#16171A;margin:0;
       display:flex;min-height:100vh;align-items:center;justify-content:center;padding:24px}
  .box{max-width:480px;background:#F6F4F0;border:1px solid #D7D3CA;border-radius:14px;padding:38px 34px;text-align:center;
       box-shadow:0 18px 50px -24px rgba(22,23,26,.5)}
  .dot{width:54px;height:54px;border-radius:50%;margin:0 auto 18px;display:flex;align-items:center;justify-content:center;
       color:#fff;font-size:1.6rem;background:<?php echo $tone; ?>}
  h1{font-size:1.4rem;margin:0 0 10px}
  p{color:#6C6A64;line-height:1.6;font-size:.95rem}
  .ref{margin-top:14px;font-size:.8rem;color:#6C6A64}
  .ref b{color:#16171A}
  a{display:inline-block;margin-top:22px;background:#16171A;color:#ECEAE5;padding:12px 24px;border-radius:999px;
    text-decoration:none;font-weight:600;font-size:.9rem}
</style>
</head>
<body>
  <div class="box">
    <div class="dot"><?php echo $status === '1' ? '&#10003;' : ($status === '2' ? '&#8230;' : '&#10005;'); ?></div>
    <h1><?php echo htmlspecialchars($title); ?></h1>
    <p><?php echo htmlspecialchars($msg); ?></p>
    <?php if ($orderId !== '') : ?>
      <p class="ref">Order reference: <b><?php echo htmlspecialchars($orderId); ?></b></p>
    <?php endif; ?>
    <a href="index.html">Back to the store</a>
  </div>
</body>
</html>
