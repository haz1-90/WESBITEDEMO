# Razak Homestay KL - Booking backend (Phase 2, Option A)

This folder holds the Google Apps Script that powers live availability and the
booking log for the website. It follows Option A from the project notes: the
website shows availability in real time, and the host confirms each booking by
hand after the deposit is received.

There are two moving parts:

1. A Google Sheet that stores every booking and acts as the availability record.
2. An Apps Script web app (`Code.gs`) that the website reads from and writes to.

Money is never handled here. Guests still pay through the ToyyibPay hosted link,
and the funds go straight to the host's bank account.

---

## What you will end up with

- A web app URL that ends in `/exec`.
- That URL pasted into `razak-homestay-kl.html` (the `BOOKING_API` setting).
- A `Bookings` sheet that fills in automatically as guests check dates.

---

## Step 1 - Create the Google Sheet

1. Sign in to the host's Google account.
2. Go to <https://sheets.google.com> and create a new blank spreadsheet.
3. Name it, for example, **Razak Homestay - Bookings**.

You do not need to add any column headers by hand. The script creates the
`Bookings` sheet and its headers the first time it runs.

The columns are:

| Timestamp | Unit | CheckIn | CheckOut | Nights | GuestName | WhatsApp | Status | Ref |
|-----------|------|---------|----------|--------|-----------|----------|--------|-----|

`Status` is one of `Pending`, `Paid`, or `Cancelled`.

---

## Step 2 - Add the script

1. In the spreadsheet, open **Extensions -> Apps Script**.
2. Delete any sample code in the editor.
3. Open `Code.gs` from this folder, copy everything, and paste it in.
4. Click the save icon.

---

## Step 3 - Deploy the web app

1. In the Apps Script editor, click **Deploy -> New deployment**.
2. For the type, choose **Web app**.
3. Set the options:
   - **Description:** Razak Homestay booking API
   - **Execute as:** Me (the host's account)
   - **Who has access:** Anyone
4. Click **Deploy** and grant the permissions when asked.
5. Copy the **Web app URL**. It ends with `/exec`.

The URL is public on purpose, because the website calls it from the guest's
browser. It only returns booked date ranges and accepts a pending booking. No
secret key is involved, so it is safe to use this way.

---

## Step 4 - Connect the website

1. Open `razak-homestay-kl.html`.
2. Near the top of the `<script>` block, find this line:

   ```js
   const BOOKING_API = "";
   ```

3. Paste your web app URL between the quotes:

   ```js
   const BOOKING_API = "https://script.google.com/macros/s/XXXXX/exec";
   ```

4. Save and publish the site.

If you leave `BOOKING_API` blank, the website still works using the manual
`AVAILABILITY` list in the HTML. The backend simply adds live updates on top.

---

## How a booking flows

1. The guest picks a home and dates, then clicks **Check availability**.
2. The website asks the web app for the latest booked ranges and checks for a
   clash.
3. If the dates are open, the website records a `Pending` row and shows the
   ToyyibPay and WhatsApp buttons.
4. The guest pays the RM100 deposit and sends the prefilled WhatsApp message,
   which carries the same booking reference shown on screen.
5. The host checks the payment, finds the matching `Pending` row by its
   reference, and changes `Status` to `Paid`.
6. From that point, those dates show as booked to the next visitor.

A `Pending` row only holds the dates for 48 hours. If the guest does not pay,
the dates open again on their own. You can change this window with the
`PENDING_HOLD_HOURS` value at the top of `Code.gs`.

To block dates by hand (for the host's own use or maintenance), add a row with
`Status` set to `Paid`. To free dates, change the status to `Cancelled` or
delete the row.

---

## Updating the script later

If you edit `Code.gs`, you must redeploy for the change to take effect:

1. **Deploy -> Manage deployments**.
2. Open the existing deployment, click the edit (pencil) icon.
3. Set **Version** to **New version** and click **Deploy**.

Keeping the same deployment means the web app URL does not change, so you do not
need to touch the HTML again.

---

## A quick test

- Open the web app URL in a browser. You should see a short JSON response such as
  `{"ok":true,"availability":{"maxim":[],"klcc":[]}}`.
- On the live site, check a set of dates. A new `Pending` row should appear in
  the sheet within a few seconds.
- Change that row's `Status` to `Paid`, reload the site, and confirm the same
  dates now show as booked.
