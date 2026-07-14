# MASTER HANDOFF — hnfamilyreunion.com build
**Read this FIRST in every new Claude Chrome session. It supersedes all
earlier instructions, summaries, and the runbook where they conflict.
Last updated: July 14, 2026.**

## How to use this document
You are Claude in Chrome, working in the owner's logged-in browser. The
website code was pre-built by Claude Code — your job is deployment and
configuration, not redesign. Work the OPEN TASKS below in order, checking
each against DONE first so you never redo or re-ask. The owner has already
made every decision recorded here — do not re-litigate them.

---

## 1. VERIFIED STATUS (as of last session)

**DONE — do not redo:**
- ✅ Plugins installed & active: Ultimate Member, WooCommerce, Fluent Forms, Elementor
- ✅ Additional CSS published to the theme (6,831 chars, `.hnwp`-scoped)
- ✅ 9 pages created WITH content: Home, Our History, Family Tree Form,
  Committees, Superlatives, Photos, Dues & T-Shirts, Give, Family Business
- ✅ 3 pages created EMPTY: Constitution & Bylaws (ID 36), Hardship Fund
  (ID 37), Features (ID 38)
- ✅ Photos uploaded to the Media Library by the owner (136 images)
- ✅ Repo history scrubbed of all genealogy data (safe at either visibility)

**NOT done:** everything in section 4.

## 2. WHERE EVERY ASSET LIVES
| Asset | Location |
|---|---|
| Page fragments (public pages) | repo `wordpress/pages/*.wp.html` — read via raw URLs if repo public, or the owner's logged-in GitHub tab if private |
| Interactive Family Tree fragment (PII — never in repo) | owner's private bundle: https://claude.ai/code/artifact/218c7ccb-7581-4ccb-aeca-3e78f3d74246 (textareas f1–f4: f1 Constitution, f2 Hardship, f3 Features, f4 Interactive Tree) |
| NEW: Family Journey interactive map fragment | repo `wordpress/pages/history-map.wp.html` |
| Field lists for registration/forms/products | repo `wordpress/RUNBOOK-WORDPRESS.md` §4–§7 |
| Photos | already in the WP Media Library — verify one URL (Media → any image → copy URL) and fix the base path in the Photos page + tree page if it isn't `/wp-content/uploads/2026/07/` |

## 3. HARD PRIVACY RULES (unchanged, non-negotiable)
No PII on the public website. Profiles/directory behind login. The
Interactive Family Tree page is members-only — apply Ultimate Member's
logged-in-only restriction BEFORE publishing it. DOB visible to member +
admins only; board-suggestions field admin-only; only account basics
required. Never publish dues status, receipts, or payment confirmations.
The questionnaire shaped the registration FIELDS only — no one's actual
answers become page content.

## 4. OPEN TASKS — in this order

### Task A — Fill the 3 empty pages
Page ID 36 ← bundle textarea f1 (Constitution & Bylaws) · ID 37 ← f2
(Hardship Fund) · ID 38 ← f3 (Features). Wrap each in a Custom HTML block
(`<!-- wp:html --> … <!-- /wp:html -->`) via the REST API, same method as
the 9 successful pages.

### Task B — Members-only Interactive Family Tree
New page, slug `members/interactive-tree`, content = bundle textarea f4.
Apply UM logged-in-only restriction FIRST, then publish. Verify logged-out
visitors get a login prompt, not the tree.

### Task C — NEW: "Family Journey" interactive map page
Create page, slug `family-journey`, title "The Family Journey", content =
repo fragment `wordpress/pages/history-map.wp.html` (self-contained SVG
road map with 8 clickable stops + story cards + reserved video slots).
This page is PUBLIC and is the **landing page for the History menu tab**.
The dashed boxes are placeholders for elder-interview videos the owner
will provide later (she wrote interview questions; videos get embedded
into the matching slot as they're ready — recommend unlisted YouTube/Vimeo
embeds, owner's call on hosting).

### Task D — Site identity & de-blog
Settings → General: Site Title `Harris-Nelson Family Reunion`, Tagline
`Rooted in love. Growing together.` (kills "Wanderlust"). Settings →
Reading: static front page = Home, no posts page. Delete "Hello world!"
post + Sample Page. Remove blog/latest-posts blocks from theme templates.

### Task E — THE MENU (owner's final structure — supersedes ALL earlier menus)
Three top-level tabs:
1. **Home** — its own tab, links to the front page, NO dropdown.
2. **History** — links to **/family-journey/ (the interactive map)** →
   dropdown: Our History · Photos & Slideshow · Family Tree Form · Features
3. **Family Members** (renamed from "Account") — links to UM Account →
   dropdown, two groups:
   - Account tabs: Sign Up/Register · Login/Logout · My Account ·
     Orders/Checkout (WooCommerce) · Password Reset
   - **Membership Benefits** (each page gets UM logged-in-only
     restriction): Committees · Constitution & Bylaws · Family Business ·
     Superlatives · Hardship Fund · Interactive Family Tree ·
     Members Directory
   Deliberately PUBLIC (do not restrict): Home, Family Journey, Our
   History, Photos, Family Tree Form, Features, **Give and Dues &
   T-Shirts** — so relatives can donate/pay without creating an account.
   (If the owner wants Give/Dues restricted too, she'll say so.)

### Task F — Apply the membership restrictions
For every Membership Benefits page above: UM restriction = logged-in users
only, with "redirect to login" behavior. Verify each as a logged-out
visitor.

### Task G — Ultimate Member registration form
Exact field list in RUNBOOK-WORDPRESS.md §4 (the generalized
questionnaire). Registration notification email →
harrisnelsonfamilyreunion@gmail.com (each sign-up doubles as the family
tree entry for the Historian).

### Task H — Four Fluent Forms
Field lists in RUNBOOK §5: #1 Family Tree Update (family-tree-form page) ·
#2 Committee Sign-Up (committees) · #3 Superlatives Ballot (superlatives) ·
#4 CONFIDENTIAL Hardship Application (hardship-fund). Replace each page's
dashed placeholder with the form shortcode; notifications → family email.

### Task I — WooCommerce products
RUNBOOK §6: three dues tiers ($125/$175/$225, virtual) · variable T-shirt
(sizes; $10, 2XL–4XL $15) · $25 repeatable Dues Installment (the free
payment-plan mechanism). Link the buttons on Dues & T-Shirts. Payments
stay Zelle/Cash App until the owner connects Stripe/PayPal herself.

### Task J — GiveWP
Install + activate GiveWP (owner pre-approved). Three donation forms with
goal progress meters, placed on the Give page: Land-Back Fund ·
Scholarship Fund · Operating Fund. Goal amounts: ask the owner (Treasurer
sets them); use $5,000 placeholders if she says proceed.

### Task K — Verification checklist
- [ ] www.hnfamilyreunion.com shows the reunion homepage, no Wanderlust, no blog
- [ ] Menu = Home / History / Family Members exactly as Task E
- [ ] /family-journey/ map: stops click, story cards change, video slots show
- [ ] Register a test account → profile behind login → notification email arrives
- [ ] Each Membership Benefits page blocks logged-out visitors
- [ ] Interactive tree renders for a logged-in member; "+ add" works
- [ ] Photos page plays the 2024 slideshow (fix upload-path URLs if broken)
- [ ] All four Fluent Forms submit + notify the family inbox
- [ ] Products exist; Give page shows three goal meters
- [ ] Nothing public shows any individual's personal information

## 5. HUMAN-ONLY TASKS (never do these; ask the owner)
Connecting Stripe/PayPal credentials · any logins/passwords · GitHub repo
visibility toggles · selecting files from her computer · Google One
storage billing (her Drive banner — unrelated to this build).

## 6. DECISION LOG (already settled — don't re-ask)
- Stack: WordPress (not the static site). Domain: hnfamilyreunion.com.
- Repo visibility: owner's call; history was scrubbed so either is safe
  for the genealogy data. She may flip it private when the build is done.
- Privacy: the three UM defaults confirmed (DOB member/admin-only, basics
  required only, board-suggestions admin-only).
- Menu: Task E above is FINAL (replaces "Home/History/Account" and the
  older 6-heading structure).
- "Account" is renamed "Family Members".
- Committees, Constitution & Bylaws, Family Business (+ Superlatives,
  Hardship, Interactive Tree, Directory) are Membership Benefits — behind
  login.
- GiveWP install pre-approved.

## 7. TROUBLESHOOTING
- **WordPress session expires:** stop, ask the owner to log back in.
- **Claude Chrome conversation errors out** (e.g. "unexpected tool_use_id"
  API errors) **or compacts and loses the thread:** the owner starts a
  FRESH conversation and says: "Open
  github.com/MEG-agt25/Family-Reunion/blob/main/wordpress/MASTER-HANDOFF.md
  (or read it via my logged-in GitHub tab), verify the DONE list, and
  continue from the first unfinished OPEN TASK."
- **Repo fetch fails** (private repo / rate limits): read files through the
  owner's logged-in GitHub tab, or the claude.ai bundle for the four
  fragments it contains.
- Prefer the WP REST API for bulk content (proven method); narrate
  progress; update nothing outside WordPress admin + reading the repo.
