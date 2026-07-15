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

Photos: the repo is now PRIVATE (done ✅), so the fragments reference the
WordPress Media Library instead. Upload the owner's
`Photos-for-WordPress-Media.zip` contents (136 images, unique filenames)
via **Media → Add New** in a single batch. The fragments assume the upload
month folder `/wp-content/uploads/2026/07/` — if WordPress puts them
elsewhere or renames a file, adjust those URLs in the pasted pages
(they appear only on the Photos page and members-only tree page).

---

## 0. HARD PRIVACY RULES (owner directive — non-negotiable)
**No personally identifiable information appears anywhere on the public
website. None.** The questionnaire exists ONLY to shape the registration
form's *fields* — no family member's answers, names, dates, genealogy,
accomplishments, or contact details ever become public page content.
Specifically:
- Profiles **require registration** and live **behind login** — the member
  directory and all profile pages are logged-in-only.
- The **interactive family tree** (fragment provided privately via the
  owner's claude.ai bundle — NOT in this repo, because it names living
  family members) goes on a page
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

## 0b. Repo hygiene — status
✅ The owner has made the GitHub repo private. Genealogy data files were
removed. One consequence: you (Claude Chrome) can still read the repo in
the owner's logged-in browser, but the public internet cannot — which is
why photos must be served from the WordPress Media Library (see the photo
note above).

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
| Interactive Family Tree (MEMBERS-ONLY) | `members/interactive-tree` | from the owner's private claude.ai bundle | restrict to logged-in members BEFORE publishing (see §0) |
| Committees | `committees` | `committees.wp.html` | placeholder → Fluent Form #2 |
| Superlatives | `superlatives` | `superlatives.wp.html` | placeholder → Fluent Form #3 |
| Photos | `photos` | `gallery.wp.html` | slideshow works as-is |
| Dues & T-Shirts | `dues-and-shirts` | `shop.wp.html` | add WooCommerce product links (step 6) |
| Give | `give` | `give.wp.html` | add GiveWP forms (step 7) |
| Family Business | `family-business` | `business.wp.html` | |
| Constitution & Bylaws | `constitution-and-bylaws` | `constitution.wp.html` | printable |
| Hardship Fund | `hardship-fund` | `hardship.wp.html` | placeholder → Fluent Form #4 |
| Features | `features` | `features.wp.html` | the feature tour |
| Financial Reports (BOARD-ONLY) | `board/financial-reports` | `financial-reports.wp.html` | draft → restrict to Administrator + Executive Board roles → publish (MASTER-HANDOFF Task O) |
| Board Sign-Up (unlisted) | `board-signup` | UM Board registration form shortcode | never in a menu; owner shares the link with officers |

Delete "Sample Page" and the "Hello world!" post.

## 3. Menus — THREE headings with dropdowns (owner's structure)
Appearance → Editor → Navigation (or Menus). Site identity first:
**Settings → General** → Site Title: `Harris-Nelson Family Reunion`,
Tagline: `Rooted in love. Growing together.` (removes the "Wanderlust"
placeholder branding). **Settings → Reading** → static front page = Home;
no posts page. Delete the "Hello world!" post and Sample Page — this is
not a blog; remove any blog/latest-posts blocks from the theme templates.

Primary menu — exactly FIVE top-level tabs (owner's final structure;
matches MASTER-HANDOFF Task E v2, which supersedes anything older):

- **Home** (links to the front page) — NO dropdown
- **History** (links to /family-journey/) → dropdown:
  *Our History* · *Photos & Slideshow* · *Family Tree Form*
- **What's Happening** (links to /features/) → dropdown:
  *Features* · *Give* · *Dues & T-Shirts*
- **My Account** (links to Ultimate Member's Account page) → dropdown,
  two groups — ALL committee- and membership-related items live here:
  Account: *Login/Logout* · *My Account* · *Orders/Checkout* ·
  *Password Reset* — then Membership Benefits (login-restricted):
  *Committees* · *Constitution & Bylaws* · *Family Business* ·
  *Superlatives* · *Hardship Fund* · *Interactive Family Tree* ·
  *Members Directory* · *Financial Reports (board)*
- **Sign Up/Register** (links to the UM Registration page) — NO dropdown

No other top-level tabs. Keep WooCommerce's Cart/Checkout out of the
menu (reachable from the shop flow); /board-signup/ stays unlisted.

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

## 4b. Executive Board role & board sign-up (access by duty)
1. UM → User Roles → Add New: `Executive Board`, based on Subscriber, no
   wp-admin capabilities, **Registration Status = "Require admin review"**
   (self-service activation must be impossible).
2. UM → Forms → duplicate Registration → `Board Member Sign-Up` → assigned
   role `Executive Board` → shortcode on unlisted page `/board-signup/`.
   The owner shares that single link privately with the 12 officers and
   approves each pending registration herself (Users → pending review).
3. Restrict `/board/financial-reports/` to roles Administrator +
   Executive Board only (draft first — see §2 table and Task O).
4. Duty-based extras (owner-only decisions): Treasurer + Financial
   Secretary may additionally get WooCommerce "Shop manager" to view
   orders/dues; nobody but the owner gets Administrator.

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

## 8. Member Benefits Bridge — installing `hn-member-benefits.php`
This code (in the repo at `wordpress/hn-member-benefits.php`) makes a
WooCommerce dues purchase — or $125 of $25 installments — automatically
unlock members-only sections wrapped in `[hn_members]...[/hn_members]`.
It also adds a "Dues" column on the admin Users list and a Treasurer
checkbox for members who pay by Zelle / Cash App / cash. A member can see
only their OWN status (via `[hn_dues_status]` on the account page) —
never anyone else's.

**Do this AFTER §6 creates the products** (the code needs their IDs).

Route A — Code Snippets plugin (preferred; no file upload involved):
1. Plugins → Add New → search **"Code Snippets"** (free) → Install →
   Activate.
2. Open the file in a browser tab:
   `github.com/MEG-agt25/Family-Reunion/blob/main/wordpress/hn-member-benefits.php`
   → click **Raw** → select all → copy.
3. Snippets → Add New → title `HN Member Benefits Bridge` → paste the code
   **minus the opening `<?php` line** (Code Snippets supplies its own; a
   duplicate `<?php` is a syntax error) → scope "Run snippet everywhere" →
   Save Changes and Activate.

Route B — plugin upload (needs the owner's `hn-member-benefits.zip`):
1. Plugins → Add New → **Upload Plugin**. WordPress only accepts a `.zip`
   here — a bare `.php` file will not be accepted by the uploader.
2. The OWNER clicks "Choose File" and picks `hn-member-benefits.zip` from
   her computer (only she can drive the browser's file picker) →
   Install Now → Activate.

Then, on either route:
4. Edit the code where `hn_mb_map()` lists four entries keyed
   `0 => ... // TODO real ID`. Replace each `0` with the matching product
   ID from §6 (Products list — the ID appears in the row/URL). The three
   dues products keep `'type' => 'dues'`; the $25 installment keeps
   `'type' => 'installment'`. Save.
5. Verify with a NON-admin test user: an `[hn_members]` section shows the
   "pay your dues" prompt → tick the Treasurer "Paid" checkbox on that
   user's profile → section unlocks → un-tick → locks again.
6. Privacy rule §0 covers dues data too: never screenshot or record real
   orders or member payment info while testing.

## 9. Verification checklist
- [ ] Register a test member → profile requires login to view → the
      registration email (with genealogy fields) arrives at the family inbox
- [ ] Interactive Family Tree page renders Mildred's branch; "+ add" works
- [ ] Photos page plays the 2024 slideshow
- [ ] Each Fluent Form submits and notifies the family email
- [ ] A $1 test product checkout works after the owner connects payments
- [ ] Give page shows three goal meters
- [ ] Nothing anywhere shows dues balances, receipts, or payment confirmations

## 10. Harris-Nelson Voting Portal — install & test
The family's ballot system (repo `wordpress/hn-voting-portal/`):
ballots are built in wp-admin, members vote via shortcode, the
Secretary exports CSV results for the minutes. Soft-integrates with the
Member Benefits Bridge (dues-current eligibility) — no hard dependency.

**Install**
1. Build the zip (repo, one command): from `wordpress/` run
   `sh hn-voting-portal/build-zip.sh` → `wordpress/hn-voting-portal.zip`
   (or the owner uses the zip Claude Code sent her).
2. The OWNER uploads: Plugins → Add New → Upload Plugin → the zip →
   Install Now → Activate. (Uploader accepts .zip only; the file picker
   is owner-only.)
3. Activation auto-creates the votes table and a DRAFT ballot
   "Constitution & Bylaws 2026 — Family Ballot" pre-loaded with the 14
   bracketed decisions from the constitution.

**Configure the first vote**
4. Ballots → edit the seeded draft → set Opens/Closes datetimes →
   eligibility "Dues-current members" → keep "voters may revise" ✓ →
   Publish.
5. Create a page `family-vote` (behind login like other member pages)
   containing `[hn_ballot id="<ID>"]` — the ID shows in the Ballots
   list's Shortcode column. Add it under My Account → Membership
   Benefits in the menu.

**Test checklist**
- [ ] Logged out: ballot page asks for login (page restriction) and the
      shortcode itself also refuses non-eligible visitors
- [ ] Non-dues member: sees the "voting members are current on dues"
      prompt with a Pay Dues button (needs Bridge active; if Bridge is
      off, an admin notice appears on the Ballots screens)
- [ ] Dues-current member (tick Treasurer Paid checkbox on a test user):
      sees questions, votes, gets the green thank-you
- [ ] Same member votes again: with revise ON the form prefills and
      updates; with revise OFF it politely refuses (server-enforced)
- [ ] Before open / after close: friendly motto messages, no form
- [ ] Ballots → Results: counts correct, ties flagged red, write-ins
      listed admin-only; CSV downloads with one row per ballot cast
- [ ] "Publish totals" unticked: [hn_ballot_results] says results come
      after close; ticked + closed: aggregate bars only, write-in text
      NEVER shown publicly
- [ ] Duplicate ballot: copies questions/settings, never votes/dates
- [ ] Delete the test votes/users when done (Results CSV first if the
      Secretary wants a record)

**Privacy rules (same as everything else):** individual votes are
admin-eyes only; nothing about who voted or how is ever public; the
public results shortcode is aggregate-only and opt-in.

## Rules
- Profiles and member directory stay behind login. DOB and board-suggestion
  fields are never publicly visible.
- Do not enter, store, or screenshot any payment credentials.
- If a WordPress session expires, stop and ask the owner to log in.
