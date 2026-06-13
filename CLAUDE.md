# CLAUDE.md — Razak Homestay KL (project memory for Claude Code)

> Read this first. It is the source of truth for this project. Do not re-ask
> what is already answered here. Keep it updated as work progresses.

## What this is
Direct-booking website for **Razak Homestay KL** — two 3-bedroom condominium
homes, booked directly with the host to avoid OTA (Airbnb/Agoda) commission.

- **Unit A:** The Maxim Residence — Maxim, Cheras (3 bedrooms, sleeps 6)
- **Unit B:** The Skyline Suite — KLCC, Kuala Lumpur (3 bedrooms, sleeps 6)
- **Owner/host:** Muhammad Razak · WhatsApp `601161200054` · muhdhazwan90@gmail.com
- **Deposit:** RM100
- **Business goal:** direct bookings; part of KNM's path to RM10k/month.

## Stack & deploy
- **Frontend:** single static file `razak-homestay-kl.html` (zero JS dependencies;
  only Google Fonts + Pexels images). Deploy to **Netlify** (drag-drop or Git) or
  **GitHub Pages** (repo: `haz1-90/WESBITEDEMO`).
- **Backend (Phase 2):** Google **Apps Script** web app + Google **Sheet**.
  Deployed from script.google.com in the OWNER's Google account — NOT GitHub.

## Status
- **FRONTEND = LOCKED.** ✅ Do not restructure layout/sections without the owner's
  explicit OK. Design direction "Twilight KL" (deep teal-ink + brass + warm paper).
- Sections built: nav, hero (load-reveal + Ken Burns), facts strip, 2 unit galleries
  with lightbox, availability badges, booking widget (date validation + clash check),
  amenities, house rules (original wording), location, FAQ, footer, WhatsApp float,
  LodgingBusiness schema. PDPA data-use notice added.

## Owner-editable points (in `razak-homestay-kl.html`, top of `<script>`)
- `TOYYIBPAY_LINK` — paste the real ToyyibPay hosted bill URL (currently placeholder).
- `WHATSAPP_NUMBER` — currently 601161200054.
- `BOOKING_API` — paste the Apps Script Web App URL (ends in `/exec`) to turn on
  live availability. Blank = manual mode using the `AVAILABILITY` list. See `backend/README.md`.
- `AVAILABILITY` — per-unit `bookedRanges`; add `["YYYY-MM-DD","YYYY-MM-DD"]` to block
  dates, delete a line to free them. Still works as a manual fallback alongside the backend.
- **Photos** are Pexels placeholders — replace with the owner's real unit photos.

## RIGID rules — never compromise (ship-safe)
1. **No secrets in frontend.** ToyyibPay SECRET KEY and Apps Script deployment URL
   live ONLY in Apps Script **Script Properties** — never in the HTML.
2. **PDPA.** Guest data is NOT stored on the website; it flows to WhatsApp + ToyyibPay
   (owner = data controller). Keep the data-use notice. No personal data on public pages.
3. **Honest capability.** Do not claim "instant auto-lock on payment" unless the
   Apps Script callback (Option B) is actually built and tested. Until then it is
   "live availability + owner-confirmed lock."
4. **Assets licensed.** Replace Pexels with real photos, or keep proof of license.
5. Money flows DIRECTLY to the owner's bank via ToyyibPay. KNM never holds funds.

## Phase 2 — booking system + payment (Option A BUILT)
Option A is built and lives in `backend/` (`Code.gs` + `README.md`). The frontend
reads live availability from the Apps Script web app and logs each held stay as a
`Pending` row. It stays fully working in manual mode until `BOOKING_API` is set.

**To go live:** follow `backend/README.md` (create the Sheet, paste `Code.gs`,
deploy as a web app for "Anyone", paste the `/exec` URL into `BOOKING_API`).

**Still to do for full hands-off (Option B):** ToyyibPay API bill creation +
payment callback so paid dates lock with no manual step.

**Option A (recommended for 2 units) — live availability + owner-confirm lock:**
- Google Sheet = availability DB + booking log (columns: timestamp, unit, check-in,
  check-out, nights, guest name, WhatsApp, status[Pending/Paid/Cancelled], ref).
- Apps Script web app: `GET` returns booked ranges per unit (frontend fetches live);
  `POST` writes a *Pending* booking row.
- Guest pays ToyyibPay **hosted link** → sends WhatsApp → owner verifies payment →
  flips row to **Paid** → that date auto-shows blocked to the next visitor.
- No ToyyibPay secret key needed (hosted link only). Simple, robust.

**Option B (upgrade at higher volume) — full auto via ToyyibPay API + callback:**
- Apps Script creates a bill per booking (API key in Script Properties), ToyyibPay
  callback → verify → auto-write Paid + block dates. Hands-off, more moving parts.

## Build discipline (follow these)
- Build in UNITS, not one giant dump. Analyze-before-fix on bugs.
- Sequence: frontend locked → backend now OK. Keep form/field names stable.
- Before go-live: run the 9 ship-safe checks + test on a REAL phone
  (ToyyibPay opens the real bill, WhatsApp prefills correctly, mobile 380px).
