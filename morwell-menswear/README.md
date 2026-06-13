# MORWELL — Menswear Store

A self-contained storefront with a product grid, size and live-stock selection,
a bag drawer, and a checkout that takes payment through **ToyyibPay (FPX + card)**.

## Files

| File | Purpose |
|------|---------|
| `index.html` | The full storefront (front end). |
| `config.php` | Your ToyyibPay keys, prices, shipping rules, and email settings. |
| `create-bill.php` | Receives the order, recalculates the total, and creates the ToyyibPay bill. |
| `return.php` | The page the customer sees after paying (success / pending / failed). |
| `callback.php` | The server-to-server confirmation ToyyibPay sends when a payment settles. |

## How payment works

1. The customer fills in the checkout form and presses **Pay with ToyyibPay**.
2. The browser posts the order to `create-bill.php`.
3. `create-bill.php` recalculates the price and shipping on the server (so the
   amount cannot be changed from the browser), creates a ToyyibPay bill, and
   redirects the customer to the secure payment page.
4. After payment the customer is returned to `return.php`, and ToyyibPay also
   notifies `callback.php` directly to confirm the payment.
5. The store receives an email when the order is placed and again when it is paid.

## Going live — step by step

1. **Register with ToyyibPay**
   - Test first at <https://dev.toyyibpay.com> (sandbox).
   - For real payments, register and verify your business at <https://toyyibpay.com>.
2. **Get your two values**
   - **User Secret Key** — in your ToyyibPay profile.
   - **Category Code** — create a Category, then copy its code.
3. **Edit `config.php`**
   - Paste in `TOYYIBPAY_SECRET_KEY` and `TOYYIBPAY_CATEGORY_CODE`.
   - Keep `TOYYIBPAY_SANDBOX = true` while testing; set it to `false` to go live.
   - Set `STORE_EMAIL` to the address where you want order notifications, and
     `STORE_FROM` to an address on your own domain for reliable delivery.
4. **Turn the backend on in `index.html`**
   - Find `const PAY_ENDPOINT='';` near the top of the script and change it to
     `const PAY_ENDPOINT='create-bill.php';`.
5. **Upload the whole folder** to PHP shared hosting (PHP 7.4 or newer with cURL,
   which almost every Malaysian host provides). Keep all files in one folder so
   the site and the payment script share the same address.
6. **Test a full order** in sandbox mode, then switch to live.

## Notes

- If you change a price, update it in **both** `config.php` and `index.html`.
- In demo mode (`PAY_ENDPOINT` empty), checkout opens a pre-filled order summary
  in WhatsApp instead of taking payment — useful before your hosting is ready.
- Stock counts in `index.html` are sample data. To manage stock from one place,
  it can later be connected to a backend such as a Google Sheet.
