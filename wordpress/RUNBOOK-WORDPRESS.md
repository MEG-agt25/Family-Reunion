# WordPress Build Runbook — hnfamilyreunion.com

**For:** Claude in Chrome, working in the WordPress admin at
hnfamilyreunion.com/wp-admin (plugins already installed & active:
Ultimate Member, WooCommerce, Fluent Forms, Elementor).

**What this package contains** (in the GitHub repo under `wordpress/`):
- `pages/*.wp.html` — every site page converted to a paste-ready fragment
  for a **Custom HTML block** in the WordPress editor
- `additional.css` — the design system, scoped so it can't fight the theme;
  paste ONCE into **Appearance → Customize → Additional CSS**
- This runbook — page map, menus, registration fields, forms, and products

Photos inside the fragments load from the GitHub repo's raw URLs, so they
work immediately with no media uploads. (Optionally migrate them to the
WordPress Media Library later.)

---

## 0. HARD PRIVACY RULES (owner directive — non-negotiable)
**No personally identifiable information appears anywhere on the public
website. None.** The questionnaire exists ONLY to shape the registration
form's *fields* — no family member's answers, names, dates, genealogy,
accomplishments, or contact details ever become public page content.
Specifically:
- Profiles **require registration** and live **behind login** — the member
  directory and all profile pages are logged-in-only.
- The **interactive family tree** (fragment at
  `wordpress/members-only/interactive-tree.wp.html`) goes on a page
  restricted to logged-in members (Ultimate Member → content restriction on
  the page, or UM's Restrict Content box) at `/members/interactive-tree/`.
  It must never be published publicly — it contains real names and years.
- DOB: visible to the member + admins only. Board-suggestions field:
  admin-only. Only account basics required; all else optional.
- Never publish dues status, receipts, payment confirmations, or any
  individual's questionnaire answers.
- Public pages carry organizational info only (committees, funds, rules,
  history of deceased ancestors) — if unsure whether something identifies a
  living person, keep it behind login and ask the owner.

## 0b. Repo hygiene (do this early)
The GitHub repo is currently PUBLIC and page images hotlink from it.
1. Upload the photos (from the repo's `website/photos/` folders) to the
   WordPress **Media Library** and update the image URLs in the pasted
   pages to the Media Library URLs (search for `raw.githubusercontent`).
2. Then ask the owner to flip the repo to **Private** (GitHub repo →
   Settings → General → Danger Zone → Change visibility). The genealogy
   data files have been removed from the repo, but private is the right
   resting state for a family repo.

## 1. Paste the stylesheet (once)
Appearance → Customize → Additional CSS → paste the whole contents of
`wordpress/additional.css` → Publish.

## 2. Create the pages
For each row: Pages → Add New → set Title and slug → add ONE **Custom
HTML** block → paste the entire matching `wordpress/pages/<file>` →
Publish. (Get file contents from
github.com/MEG-agt25/Family-Reunion/tree/main/wordpress/pages — use the
"Raw" view and copy everything.)

| Page title | Slug | Fragment file | Notes |
|---|---|---|---|
| Home | (set as front page) | `index.wp.html` | Settings → Reading → static front page |
| Our History | `history` | `history.wp.html` | |
| Family Tree Form | `family-tree-form` | `tree.wp.html` | replace placeholder with Fluent Form #1 |
| Interactive Family Tree (MEMBERS-ONLY) | `members/interactive-tree` | `members-only/interactive-tree.wp.html` | restrict to logged-in members (see §0) |
| Committees | `committees` | `committees.wp.html` | placeholder → Fluent Form #2 |
| Superlatives | `superlatives` | `superlatives.wp.html` | placeholder → Fluent Form #3 |
| Photos | `photos` | `gallery.wp.html` | slideshow works as-is |
| Dues & T-Shirts | `dues-and-shirts` | `shop.wp.html` | add WooCommerce product links (step 6) |
| Give | `give` | `give.wp.html` | add GiveWP forms (step 7) |
| Family Business | `family-business` | `business.wp.html` | |
| Constitution & Bylaws | `constitution-and-bylaws` | `constitution.wp.html` | printable |
| Hardship Fund | `hardship-fund` | `hardship.wp.html` | placeholder → Fluent Form #4 |
| Features | `features` | `features.wp.html` | the feature tour |

Delete "Sample Page" and the "Hello world!" post.

## 3. Menus (the dropdown structure)
Appearance → Editor → Navigation (or Menus). Primary menu:

- **Home**
- **Family Tree** → children: *Family Tree Form*, *Interactive Family Tree (members-only — hidden or lock-marked for logged-out visitors)*
- **Get Involved** → children: *Committees*, *Superlatives*, *Hardship Fund*
- **Money** → children: *Dues & T-Shirts* , *Give*, *Shop* (WooCommerce)
- **Our Story** → children: *Our History*, *Photos*, *Features*
- **Family Business** → children: *Family Business*, *Constitution & Bylaws*
- **My Account** → Ultimate Member's Account page (shows Login/Register when logged out)

## 4. Ultimate Member — registration = the generalized questionnaire
UM → Forms → edit the Registration form. Fields (basics required, all
else optional):

**Account (required):** First & Last Name · Email · Username · Password

**About you:** Date of Birth *(privacy: visible to owner/admins only)* ·
City & State · **Family Branch** — dropdown, exact options:
`Mary Nelson / Mildred Ellis / Jessie Moore / James Edward / James Earl /
Curtis / Mary Jane Gray / Mandy Ellis / Shirley Harris / Nelson line (Joe &
Oreatha) / Not sure — help me find my branch!` · Phone

**Your line (feeds the family tree — Historian gets these):**
Parents' Names · Grandparents' Names · Great-Grandparents' Names ·
Great-Great-Grandparents & other greats · Spouse/Partner & Anniversary ·
Children's Names (& birth years)

**Your story (yearbook & acknowledgments):** School(s) & Graduation
Date(s) · Fraternity/Sorority · Military Affiliation · Personal & Family
Accomplishments · One Interesting Fact · Known Family History Facts

**Planning ahead:** What would you like to see at future reunions? ·
Foundation (501c3) interest — radio Yes/Maybe/No · Board suggestions
*(admin-only field)* · Would you serve on a committee? — radio
Chair/Member/Not now

**Permissions (checkboxes):** Add me to the family Ancestry tree
(required) · Names on the printed tree poster · Family may contact me

Settings: UM → Settings → Access → site content public EXCEPT member
directory & profiles (logged-in only). New-registration notification email
→ harrisnelsonfamilyreunion@gmail.com **(this is how each sign-up
automatically becomes a family-tree entry — the Historian receives every
registration with the genealogy fields).**

## 5. Fluent Forms — the four forms
Replace each page's dashed placeholder box with the matching form's
shortcode. Field lists:

**#1 Family Tree Update** (family-tree-form page): Full name* · Maiden/other
names · DOB · City & state · Family Branch dropdown (options above) ·
Parents* · Grandparents · Spouse/partner · Children (& birth years) ·
Email · Phone · Checkboxes: Ancestry consent* / poster / contact · Story
for the Historian. Notification → family email, subject "TREE ENTRY".

**#2 Committee Sign-Up** (committees page): Full name* · Branch · Email* ·
Phone · Checkboxes (choose many): Sports/Games, Music, Food, Safety,
Services, Photography, Philanthropy & Fundraising, Planning, Technology,
Courtesy (member), Protocol (member) · Role* radio: Chair ⭐ / Member /
Either · Chair first choice dropdown (9 chairable committees) · the 7
volunteer-questionnaire textareas (why volunteer, hopes, hours/month
select, best contribution, prior volunteering, passions, success).

**#3 Superlatives Ballot** (superlatives page): Voter name* · Branch ·
one text input per award: Best Dressed, Life of the Party, Best Dancer,
Loudest Laugh, Most Likely to Be Late, Best Cook/Dish, Grill Master, Best
Card/Domino Player, Games MVP, Family Glue, Traveled the Farthest, Legacy
Keeper, Write-in.

**#4 Helping Hands Application** (hardship-fund page — mark form
CONFIDENTIAL): Applicant name* · Branch* · Email/phone* · Household size ·
For whom (radio: myself / on behalf of a family member) · Hardship type
checkboxes (Medical, Job loss/income, Housing/disaster, Funeral,
Caregiving, Education emergency, Other) · What happened & when* · How the
award would help* · Amount (optional) · Documentation (optional) ·
Consent to confidential review* · Prefer anonymity checkbox. Notification
ONLY to the family email, subject "CONFIDENTIAL — Hardship Application".

## 6. WooCommerce — dues & shirts
Products → Add New:
1. **Reunion Dues — Single (or with 1 minor)** — $125, virtual
2. **Reunion Dues — Single + guest / college student** — $175, virtual
3. **Reunion Dues — Family (3+)** — $225, virtual
4. **Reunion T-Shirt** — variable product; attribute Size =
   `Youth S/M/L/XL · Adult S/M/L/XL ($10) · 2XL/3XL/4XL ($15) ·
   Toddler 6M/12M/18M/24M ($10)`
5. **Dues Installment — $25** — virtual, note "buy as many as you need,
   any time" → this is the free payment-plan mechanism (repeatable
   installments; true auto-recurring billing needs a paid extension —
   flag for later).
Link the "Pay Dues / Order Shirts" buttons on the Dues & T-Shirts page to
these products. **The owner must connect Stripe/PayPal herself** (WooCommerce
→ Settings → Payments); do not touch credentials. Keep the Zelle
($mieshanulife06 Cash App / harrisnelsonfamilyreunion@gmail.com Zelle)
instructions visible as the no-fee option.

## 7. GiveWP — donations & the tracking meter
Install + activate **GiveWP** (free). Create three donation forms, each
with a **goal progress bar** (the "tracking meter"), any-amount giving
enabled, and place them on the Give page under the matching fund cards:
1. **Land-Back Fund** — goal $[Treasurer sets]
2. **Scholarship Fund** — goal $[Treasurer sets]
3. **Operating Fund (website + seed money)** — goal $[Treasurer sets]
GiveWP free supports one-time gifts of any amount at any frequency the
giver chooses manually; automatic recurring donations are a paid add-on —
flag, don't buy.

## 8. Verification checklist
- [ ] Register a test member → profile requires login to view → the
      registration email (with genealogy fields) arrives at the family inbox
- [ ] Interactive Family Tree page renders Mildred's branch; "+ add" works
- [ ] Photos page plays the 2024 slideshow
- [ ] Each Fluent Form submits and notifies the family email
- [ ] A $1 test product checkout works after the owner connects payments
- [ ] Give page shows three goal meters
- [ ] Nothing anywhere shows dues balances, receipts, or payment confirmations

## Rules
- Profiles and member directory stay behind login. DOB and board-suggestion
  fields are never publicly visible.
- Do not enter, store, or screenshot any payment credentials.
- If a WordPress session expires, stop and ask the owner to log in.
