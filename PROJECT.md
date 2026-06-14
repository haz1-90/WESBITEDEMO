# WESBITEDEMO — Project Notes & Build Log

KNM Web Studio · Muhammad R. · WhatsApp `601161200054` · `muhdhazwan90@gmail.com`

This file documents every website in this repository: what it is, what was
fixed and **why**, the **final state**, the live links, and the points an owner
can edit. Hosted free on **GitHub Pages** from the `main` branch.

---

## 1. Live links

| # | Website | Type | Link |
|---|---------|------|------|
| 0 | Demo hub (menu of everything) | Landing menu | `https://haz1-90.github.io/WESBITEDEMO/` |
| 1 | KNM Web Studio | Agency / services | `https://haz1-90.github.io/WESBITEDEMO/knm-web-studio/` |
| 2 | MORWELL Menswear | Online store + payment | `https://haz1-90.github.io/WESBITEDEMO/morwell-menswear/` |
| 3 | Razak Homestay KL | Homestay booking | `https://haz1-90.github.io/WESBITEDEMO/razak-homestay-kl.html` |
| 4 | Adam & Aisyah — Premium | Wedding invitation | `https://haz1-90.github.io/WESBITEDEMO/Npremiumwedding/` |
| 5 | Adam & Aisyah — Classic | Wedding invitation | `https://haz1-90.github.io/WESBITEDEMO/wedding.html` |
| 6 | Liora Scarves | Boutique | `https://haz1-90.github.io/WESBITEDEMO/template-basic-2-boutique.html` |

All links, for quick copy:

```text
Hub               https://haz1-90.github.io/WESBITEDEMO/
KNM Web Studio    https://haz1-90.github.io/WESBITEDEMO/knm-web-studio/
MORWELL Menswear  https://haz1-90.github.io/WESBITEDEMO/morwell-menswear/
Razak Homestay    https://haz1-90.github.io/WESBITEDEMO/razak-homestay-kl.html
Wedding Premium   https://haz1-90.github.io/WESBITEDEMO/Npremiumwedding/
Wedding Classic   https://haz1-90.github.io/WESBITEDEMO/wedding.html
Liora Scarves     https://haz1-90.github.io/WESBITEDEMO/template-basic-2-boutique.html
```

> Tip: after a change, a shared link may show an old preview on WhatsApp.
> Force a refresh by adding `?v=2` (any number) to the end, or use the
> Facebook Sharing Debugger and press "Scrape Again".

---

## 2. KNM Web Studio (the agency site) — `knm-web-studio/`

The studio's own marketing site: explains the service, shows the work, and
invites an enquiry. Cinematic but professional. Formal, plain English (no jargon).

### What was done and why

| Change | Why |
|--------|-----|
| Built the full site: hero, problem statement, services, packages, portfolio, process, trust, contact | Needed an impressive "shopfront" that gives a wow factor and explains the service clearly |
| Services explained as **The Front** / **The Engine** / **Ease of Use (UI/UX)** | Front end, back end and design, in plain terms a non-technical owner understands |
| Three packages: **Basic / Intermediate / Premium**, **no prices** | Prices intentionally left out until a market survey is done; each opens a WhatsApp quote request |
| Trending effects: scroll-progress bar, gradient hero, scroll reveals, cursor glow, 3D card tilt, magnetic buttons, shine, pinned "showreel" phone | Wow factor, Samsung-like premium feel; all respect reduced-motion and only run on fine-pointer devices |
| **Hero video** (`hero.mp4`) made full-bleed, with the headline/CTAs at the bottom-left, a strong veil and text shadows | Cinematic; the owner uploaded a workspace clip. The veil keeps text clearly readable over the footage |
| Removed the floating preview cards from the hero | They clashed with the video; the showreel phone carries the work visual instead |
| **Mobile**: burger menu, back-to-top button, the showreel phone auto-plays, hero cards hidden | Easy navigation without long scrolling; no odd placeholders; a moving visual on phones |
| Removed the small stats strip and the scrolling keyword marquee | Felt small/unclear; cleaner without them |
| Replaced the rotating headline word with a static line | "while you run your ___" wrapped awkwardly |
| Balanced the "Why this matters" two columns | Right card looked taller/unbalanced |
| Removed the displayed phone number (kept WhatsApp + Email buttons) | Owner concern about number scraping by scammers |
| Branded **share image** (`og-cover.jpg`, 75 KB): studio photo + brand + tagline | A professional, trustworthy preview when the link is shared |
| Credit shortened to **"Muhammad R."** | Owner preference |
| Added a **"See our packages"** CTA in the hero | Quick jump to the packages section |

### Final state
- Video-forward hero (`hero.mp4`), clear text, three CTAs: Start on WhatsApp · See our work · See our packages.
- Sections: Showreel (work) → Why this matters → Services → Packages → Our Work → Process → Trust → Contact.
- Contact via WhatsApp button + Email button only (no number shown).
- Share preview = `og-cover.jpg`.
- **No prices yet.**

### Owner-editable
- `hero.mp4` — the hero background video. Replace to change the mood.
- `og-cover.jpg` — the share preview image.
- `studio.jpg` — an optimised copy of the workspace photo, spare for a future section.
- Package feature lists, headline, and copy — edit the text in `index.html`.
- WhatsApp `601161200054`, email `muhdhazwan90@gmail.com`.

---

## 3. MORWELL Menswear — `morwell-menswear/`

A demo online store: product grid, size + live stock, shopping bag, checkout.

### What was done and why

| Change | Why |
|--------|-----|
| Rewrote all copy into formal, natural English; removed marketing/AI-sounding phrases and stray em-dashes; "Tees" → "T-Shirts" | The brief asked for full, formal, human-sounding English |
| Built a **ToyyibPay payment backend** (PHP): `create-bill.php`, `return.php`, `callback.php`, `config.php` | A real checkout that takes FPX/card payment, not just a demo |
| Checkout recalculates price + shipping **on the server**; creates a ToyyibPay bill; callback confirms payment and emails the store | Prevents the amount being tampered with from the browser |
| `PAY_ENDPOINT` flag in `index.html` | Empty = WhatsApp demo mode; set to `create-bill.php` to go live |

### Final state
- Fully working in **WhatsApp demo mode** (checkout opens a pre-filled order).
- Ready for live payments once ToyyibPay keys are added — see `morwell-menswear/README.md`.

### To go live (later)
1. Register at ToyyibPay; get the **User Secret Key** and **Category Code**.
2. Paste them into `config.php` (keep `SANDBOX = true` while testing).
3. Set `PAY_ENDPOINT = 'create-bill.php'` in `index.html`.
4. Upload the folder to **PHP shared hosting** (GitHub Pages cannot run PHP).

---

## 4. Premium Wedding — `Npremiumwedding/`

Animated invitation: video hero, scroll story, RSVP, gallery, music.

### What was done and why

| Change | Why |
|--------|-----|
| Rebuilt the "Kisah" story as a **native CSS scroll-snap page-turn** (`scroll-snap-stop: always`, ~0.85s flip) | Earlier versions had a green flash on entry, a jerky/overshooting feel, and the last slide vanished too fast. Native snap gives **one scroll = one page**, smooth and readable, the same on desktop and mobile |
| Gallery "Mereka Berdua": added the couple / groom / bride photos | Owner's real photos in the gallery |
| Compressed those photos from ~7 MB PNG to ~150–250 KB JPG | Fast loading and a working WhatsApp link preview (large images get rejected by preview crawlers) |
| Simplified the attendance heading to **"RSVP"** | The previous wording read oddly |
| Music now **pauses when the tab is hidden/minimised/closed** and resumes only if it was playing | It used to keep playing in the background |
| `og:image` set to the couple photo | A warm, on-brand share preview |

### Final state
- Smooth one-page-at-a-time story; clear RSVP; gallery shows the three photos; music behaves correctly.

### Owner-editable
- Gallery images: `pengantin-berdua.jpg`, `pengantin-adam.jpg`, `pengantin-aisyah.jpg`.
- Story scenes: `scene-*.webp`. Hero video: `hero.mp4`. Music: `lagu2.mp3`.
- Text, dates, names: search the `CONFIG` block and the `EDIT:` comments in `index.html`.

---

## 5. Classic Wedding — `wedding.html`
A simpler single-page version of the same invitation. The same three gallery
photos are wired in, and `og:image` is set. (Originally the repo's root
`index.html`; renamed to `wedding.html` so the root could host the demo menu.)

## 6. Liora Scarves — `template-basic-2-boutique.html`
A boutique demo. The two human-model photos (hero + story) were replaced with
**modest scarf product images** for a syariah-compliant presentation. `og:image`
and Twitter card added.

## 7. Demo hub — root `index.html`
A landing menu that links to every site above, so each one has its own clean
entry point. Updated whenever a new site is added.

---

## 8. Pending / to-do

- [ ] **Set package prices** (after a market survey) and add them to KNM `index.html`.
- [ ] **ToyyibPay keys** for MORWELL (and any premium client) to take live payments.
- [ ] **Domain** (optional): e.g. `knmwebstudio.com` or `knm.studio`. Connect to
      GitHub Pages with a `CNAME` file, or move hosting to Netlify.
- [ ] **2FA + branch protection** on the GitHub account (see below).

## 9. Security & hosting notes

- This repo is **public**: anyone with the link can read and copy the files.
  This is normal for a website — the **front-end code of any live site is always
  viewable** by visitors (browser "View Source"), regardless of where it is hosted.
  Making the repo private only hides the GitHub repo, not the live page's code.
- **What actually protects you:** turn on **2FA**, use a strong unique password,
  add a **branch-protection rule** on `main`, and do not add untrusted
  collaborators. No one can change your repo without your login.
- **No secrets in the repo.** The ToyyibPay secret key lives only in the PHP
  `config.php` on the payment host, never in any front-end file.
- Want the source hidden? Use a **private repo + Netlify/Vercel** (free tier
  supports private repos), or GitHub Pro for Pages from a private repo.

---

## 10. How updates are made
Work happens on the `claude/zen-noether-4kzz5e` branch, then is merged to `main`.
GitHub Pages rebuilds `main` automatically (about 1–2 minutes), and the live
links update.
