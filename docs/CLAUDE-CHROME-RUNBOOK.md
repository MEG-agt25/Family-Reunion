# Claude Chrome Runbook — Launch hnfamilyreunion.com

**Who this is for:** Claude in Chrome, operating in Marcelette's browser where
she is logged into GitHub, the domain registrar, and Google Drive.

**The situation:** The complete website is already built and pushed to
`github.com/MEG-agt25/Family-Reunion` (the `website/` folder — 15 pages:
home, profile/account, features tour, history, interactive family tree,
tree form, committees, superlatives, gallery with the 2024 slideshow, shop,
give, hardship fund, family business, constitution). A GitHub Actions
workflow (`.github/workflows/deploy-pages.yml`) deploys `website/` to GitHub
Pages on every push — it is currently failing ONLY because Pages has not
been enabled on the repo. Do not rebuild or redesign anything; the code is
done. Your job is deployment plumbing, in this order:

---

## Task 1 — Enable GitHub Pages (2 minutes)
1. Go to https://github.com/MEG-agt25/Family-Reunion/settings/pages
2. Under **Build and deployment → Source**, select **GitHub Actions**.
3. Go to the **Actions** tab → "Deploy website to GitHub Pages" → open the
   most recent (failed) run → **Re-run all jobs**.
4. Wait for it to go green, then verify
   https://meg-agt25.github.io/Family-Reunion/ loads the reunion homepage
   (green header, "Rooted in Love. Growing Together.").

## Task 2 — Point www.hnfamilyreunion.com at the site (10 minutes + DNS wait)
1. Find where hnfamilyreunion.com is registered (GoDaddy, Namecheap,
   Squarespace Domains, Google Workspace, etc. — check the owner's email
   receipts or ask her if not obvious). Open that registrar's **DNS
   management** page for hnfamilyreunion.com.
2. Add these records (delete any conflicting parked A/CNAME records first):
   | Type  | Host/Name | Value                  |
   |-------|-----------|------------------------|
   | CNAME | `www`     | `meg-agt25.github.io`  |
   | A     | `@`       | `185.199.108.153`      |
   | A     | `@`       | `185.199.109.153`      |
   | A     | `@`       | `185.199.110.153`      |
   | A     | `@`       | `185.199.111.153`      |
3. Back at https://github.com/MEG-agt25/Family-Reunion/settings/pages,
   enter `www.hnfamilyreunion.com` in **Custom domain** and Save. If GitHub
   asks for domain verification, follow its TXT-record instructions at the
   registrar.
4. Once the DNS check passes (minutes to a few hours), tick
   **Enforce HTTPS**.
5. Verify https://www.hnfamilyreunion.com loads, and that the bare domain
   (hnfamilyreunion.com) redirects to it.

## Task 3 — Wire the forms to a real inbox (10 minutes)
Right now every form falls back to opening the visitor's email app. Fix:
1. Go to https://formspree.io → sign up free using
   **harrisnelsonfamilyreunion@gmail.com** → create a new form named
   "HN Family Site" → copy the endpoint URL (looks like
   `https://formspree.io/f/xxxxxxx`).
2. Edit the config file directly on GitHub:
   https://github.com/MEG-agt25/Family-Reunion/edit/main/website/js/config.js
   — set `FORM_ENDPOINT: ""` to `FORM_ENDPOINT: "https://formspree.io/f/xxxxxxx"`
   and commit to `main`. The site redeploys automatically.
3. Test: open the live site → My Profile → fill a test profile → submit →
   confirm the submission arrives in the Formspree dashboard/email.
4. In Formspree settings, add harrisnelsonfamilyreunion@gmail.com as the
   notification email so the Historian sees every tree entry.

## Task 4 — (Optional, when the Treasurer is ready) Card payments
1. Create Stripe **Payment Links** (or PayPal buttons) for: registration/dues,
   t-shirts, and the three donation funds.
2. Paste each URL into the matching `PAYMENTS` field in the same
   `website/js/config.js` file and commit. Buttons appear automatically;
   until then the site shows Zelle/Cash App instructions, which already work.

## Task 5 — Google Drive
1. Open the "Family Reunion" folder in Google Drive.
2. Upload the latest shared files (Marcelette has them from Claude Code):
   `Family-Reunion-Complete.zip`, `Harris-Nelson-Reunion-2026.pptx`,
   `Constitution-and-Bylaws.docx`, `Hardship-Fund-Application.docx`,
   `Roberts-Rules-Quick-Guide.docx`, `501c3-Roadmap.docx`.
   (Alternative: download the repo zip from
   https://github.com/MEG-agt25/Family-Reunion/archive/refs/heads/main.zip)
3. Set the folder's sharing to "Anyone with the link can view" if the family
   should be able to browse it.

## Verification checklist (do all of these on the live site)
- [ ] Homepage loads at www.hnfamilyreunion.com with HTTPS padlock
- [ ] "⋯ More" menu opens with the three dropdown groups
- [ ] My Profile: create a test profile → dashboard appears → open the
      committees page → name/branch/email are pre-filled
- [ ] Interactive tree (More → Interactive Family Tree) renders Mildred's
      branch and "+ add" works
- [ ] Photos page plays the 2024 slideshow fullscreen
- [ ] A form submission arrives at Formspree/the family email
- [ ] Constitution page prints cleanly (Ctrl/Cmd+P)

## Rules
- Never commit to any branch except `main` of MEG-agt25/Family-Reunion, and
  only touch `website/js/config.js` unless asked.
- Do not put the dues sheet, receipts, or anyone's payment confirmations on
  the website or in the repo.
- If a step fails, stop and report exactly what screen you're on and what
  the error says rather than improvising.
