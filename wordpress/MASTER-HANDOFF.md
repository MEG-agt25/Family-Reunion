# HARRIS-NELSON FAMILY REUNION — COMPLETE WEBSITE HANDOFF
**For Claude in Chrome. This is the full project brief + build instructions.
It is the ONLY source you need — never fetch URLs for content; when this
file travels as an attachment, the five page fragments are appended at the
bottom. Version: July 14, 2026.**

---

# PART 1 — THE PROJECT (everything decided this session)

## Who this is for
The **Harris-Nelson Family Reunion Organization** — a family founded by
John Harris & Judy Bender (late 1800s, Mississippi; he a farmer, she a
homemaker). Through Emma (Bently) Moncrief & Ed Moncrief came Mandy
Moncrief, then **Lela Mae Price (July 16, 1922 – Feb 15, 1996)**, whose
nine children became the family's nine branches: Mary Nelson, Mildred
Ellis, Jessie Moore, James Edward, James Earl, Curtis, Mary Jane Gray,
Mandy Ellis, Shirley Harris (plus the Nelson line of Joe & Oreatha). The
family migrated from Mississippi to Cleveland, Ohio. The 2026 reunion
("Together Again!" / "Here we go Again… Kick'n it with the Kins!") was
held July 17–19, 2026 in Cleveland with ~115 attending.

**Website:** hnfamilyreunion.com (WordPress, Twenty Twenty-Five).
**Family email:** harrisnelsonfamilyreunion@gmail.com.
**Site identity:** Title `Harris-Nelson Family Reunion` · Tagline
`Rooted in love. Growing together.` This is NOT a blog.

## How the family governs (context for the pages)
- **12 officers**: President (Miesha) · Vice President (Aunt Shirley) ·
  Secretary (Marcelette — the site owner) · Treasurer (Jasmine) ·
  Financial Secretary (Aunt Vanessa) · Historian (Shone) · Hospitality
  Chair (Loretta, leads the Courtesy Committee) · Sergeant-at-Arms
  (Joseph) · plus four open seats: Membership Chair, Parliamentarian
  (chairs the Protocol Committee), Doorkeeper, Meditation Chair.
- **11 standing committees**: Sports/Games, Music, Food, Safety, Services,
  Photography, Philanthropy & Fundraising, Planning, Technology, Courtesy,
  Protocol. Nine need chairs; all need 2–5 members.
- **Robert's Rules of Order** is the parliamentary authority; a written
  **Constitution & Bylaws** exists (it's one of the site pages).
- A family **501(c)(3) foundation** is being formed.

## The money model (context for shop/give pages)
- **Dues:** $125 single (or w/ 1 minor) · $175 single + guest or college
  student · $225 family of 3+. **Shirts:** $10, 2XL–4XL $15 (adult SM–4XL,
  youth SM–XL, toddler 6M–24M).
- **Three funds**, split of all fundraising set by family vote:
  1. **Land-Back Fund** — Where Is My Land retainer, research/legal costs,
     plus renovation & revitalization of family land
  2. **Scholarship Fund** — for descendants of the ancestors
  3. **Operating Fund** — strictly operating costs: this website's hosting,
     printing, permits/deposits, and seed money for future reunions
- **Helping Hands Hardship Fund** — confidential aid each reunion cycle:
  applications open Jan 1, close 30 days before the reunion; scored by the
  Philanthropy & Fundraising Committee (severity 40% / impact 30% /
  clarity 20% / discretion 10%); officers ratify; recipients may stay
  anonymous.
- **Payments today:** Zelle harrisnelsonfamilyreunion@gmail.com · Cash App
  $mieshanulife06 (payer writes name + purpose). Card payments come later
  when the owner connects Stripe/PayPal herself. Payment plans = a $25
  repeatable "Dues Installment" product. Donation pages get **goal
  progress meters** (GiveWP) so paid-vs-goal is visible.

## The membership vision (the heart of the site)
Family members **register** to create a profile. The registration form IS
the family questionnaire, generalized for all future reunions (no
2026-specific questions). **Each registration automatically becomes a
family-tree entry** — the notification email (to the family inbox) carries
the genealogy fields to the Historian, who maintains the master Ancestry
tree (3,465+ people) and the printed family-tree poster. Members get:
the interactive family tree, the member directory, committee sign-up,
superlatives voting, hardship applications, and orders — the "Membership
Benefits."

## HARD PRIVACY RULES (owner directive — non-negotiable)
**No personally identifiable information anywhere on the public site.**
The questionnaire shapes the registration form's FIELDS only — no
member's answers, names, dates, genealogy, accomplishments, or contact
details ever become public content. Profiles, member directory, and the
interactive tree live behind login. DOB visible to member + admins only;
board-suggestions field admin-only; only account basics required. Never
publish dues status, receipts, or payment confirmations. Public pages
carry organizational info and deceased ancestors' history only. If unsure
whether something identifies a living person: restrict it and ask.

---

# PART 2 — BUILD STATE (verify, don't redo)

**DONE:**
- Plugins active: Ultimate Member, WooCommerce, Fluent Forms, Elementor
- Additional CSS published (6,831 chars, `.hnwp`-scoped)
- 9 pages created WITH content: Home, Our History, Family Tree Form,
  Committees, Superlatives, Photos, Dues & T-Shirts, Give, Family Business
- 3 pages created EMPTY: Constitution & Bylaws (ID 36), Hardship Fund
  (ID 37), Features (ID 38)
- Photos uploaded to the Media Library (136 images)

**HUMAN-ONLY (ask the owner, never do):** Stripe/PayPal connection, any
passwords/logins, file selection from her computer, GitHub settings.

---

# PART 3 — OPEN TASKS, IN ORDER

**Task A — Fill the 3 empty pages.** ID 36 ← FRAGMENT 1 · ID 37 ←
FRAGMENT 2 · ID 38 ← FRAGMENT 3. Wrap in a Custom HTML block
(`<!-- wp:html --> … <!-- /wp:html -->`); the REST API method already
proven on the 9 successful pages works well.

**Task B — Members-only Interactive Family Tree.** New page, slug
`members/interactive-tree`, content = FRAGMENT 4. Apply Ultimate Member's
logged-in-only restriction BEFORE publishing. Verify logged-out visitors
get a login prompt.

**Task C — "The Family Journey" interactive history map.** New PUBLIC
page, slug `family-journey`, content = FRAGMENT 5 — an SVG road map with
8 clickable stops (Mississippi 1800s → Lela Mae → the nine branches → the
Migration → Cleveland 2024 → 2026 → the road ahead), each with a story
card and a reserved slot for **elder-interview videos** the owner will
record (her questions are printed in each slot). This page is the
**History menu tab's landing page**.

**Task D — Site identity & de-blog.** Settings → General: title + tagline
above (kills "Wanderlust"). Settings → Reading: static front page = Home,
no posts page. Delete "Hello world!" + Sample Page; remove blog blocks
from theme templates.

**Task E — THE MENU (final; supersedes everything earlier).**
1. **Home** — its own tab, links to front page, NO dropdown
2. **History** — links to /family-journey/ → dropdown: Our History ·
   Photos & Slideshow · Family Tree Form · Features
3. **Family Members** (renamed from "Account") — links to UM Account →
   dropdown in two groups:
   - Account: Sign Up/Register · Login/Logout · My Account ·
     Orders/Checkout · Password Reset
   - Membership Benefits (login-restricted pages): Committees ·
     Constitution & Bylaws · Family Business · Superlatives · Hardship
     Fund · Interactive Family Tree · Members Directory
   Deliberately PUBLIC: Home, Family Journey, Our History, Photos, Family
   Tree Form, Features, Give, Dues & T-Shirts (so relatives can pay/donate
   without an account). Keep Cart/Checkout out of the top menu.

**Task F — Apply the membership restrictions** to every Membership
Benefits page (UM logged-in-only, redirect to login). Verify logged out.

**Task G — Ultimate Member registration form** (basics required, rest
optional): Account: First/Last Name, Email, Username, Password. About:
DOB *(member/admin-only)*, City & State, Phone, **Family Branch** dropdown
with EXACT options: Mary Nelson / Mildred Ellis / Jessie Moore / James
Edward / James Earl / Curtis / Mary Jane Gray / Mandy Ellis / Shirley
Harris / Nelson line (Joe & Oreatha) / Not sure — help me find my branch!
Your line (feeds the tree): Parents · Grandparents · Great-Grandparents ·
Great-Great-Grandparents & other greats · Spouse/Partner & Anniversary ·
Children (& birth years). Your story: Schools & Graduation Dates ·
Fraternity/Sorority · Military · Accomplishments · One Interesting Fact ·
Known Family History Facts. Planning: Future-reunion wishes · Foundation
(501c3) interest Yes/Maybe/No · Board suggestions *(admin-only)* · Serve
on a committee? Chair/Member/Not now. Permissions checkboxes: Ancestry
tree (required) · poster · contact. Settings: profiles + directory
logged-in-only; new-registration notification →
harrisnelsonfamilyreunion@gmail.com (this IS the automatic tree entry).

**Task H — Four Fluent Forms** (replace each page's dashed placeholder
with the form shortcode; notifications → family email):
1. *Family Tree Update* (family-tree-form): Full name*, Maiden/other
   names, DOB, City & state, Branch dropdown, Parents*, Grandparents,
   Spouse, Children, Email, Phone, consent checkboxes (Ancestry*, poster,
   contact), story for the Historian. Subject "TREE ENTRY".
2. *Committee Sign-Up* (committees): Full name*, Branch, Email*, Phone,
   committee checkboxes (the 11; Courtesy/Protocol member-only), Role*
   radio Chair/Member/Either, Chair-first-choice dropdown (9), and the 7
   volunteer questions (why volunteer, hopes, hours/month, best
   contribution, prior volunteering, passions, success).
3. *Superlatives Ballot* (superlatives): Voter name*, Branch, one text
   input per award (Best Dressed, Life of the Party, Best Dancer, Loudest
   Laugh, Most Likely to Be Late, Best Cook/Dish, Grill Master, Best
   Card/Domino Player, Games MVP, Family Glue, Traveled the Farthest,
   Legacy Keeper, Write-in).
4. *CONFIDENTIAL Hardship Application* (hardship-fund): Applicant name*,
   Branch*, Email/phone*, Household size, For whom (self/on behalf),
   hardship-type checkboxes (Medical, Job loss, Housing/disaster, Funeral,
   Caregiving, Education emergency, Other), what happened & when*, how the
   award helps*, amount (opt), documentation (opt), confidential-review
   consent*, prefer-anonymity checkbox. Subject "CONFIDENTIAL — Hardship
   Application"; notification ONLY to the family email.

**Task I — WooCommerce products:** Dues $125 / $175 / $225 (virtual) ·
variable T-Shirt (sizes & prices above) · $25 repeatable Dues Installment
(the free payment-plan mechanism). Link buttons on Dues & T-Shirts.

**Task J — GiveWP** (install pre-approved): three donation forms with goal
progress meters on the Give page — Land-Back · Scholarship · Operating.
Goal amounts: ask the owner; $5,000 placeholders if she says proceed.

**Task K — Verify:** menu matches Task E · registration works and the
notification email arrives · every Membership Benefits page blocks
logged-out visitors · the tree renders for members · /family-journey/
stops click through · Photos slideshow plays (fix `/wp-content/uploads/`
month path if images 404) · all four forms submit · products exist ·
three goal meters show · nothing public shows any individual's info.

---

# PART 4 — DECISION LOG (settled; do not re-ask)
WordPress over static · domain hnfamilyreunion.com · profiles behind
login, registration = generalized questionnaire, registration doubles as
tree entry · DOB member/admin-only, basics-only required,
board-suggestions admin-only · "Account" renamed **Family Members** ·
Home standalone; History lands on the Family Journey map · Membership
Benefits (Committees, Constitution & Bylaws, Family Business,
Superlatives, Hardship, Interactive Tree, Directory) behind login ·
Give + Dues & T-Shirts stay public · GiveWP approved · owner handles
Stripe/PayPal + all credentials · repo visibility is the owner's business,
not yours.

# PART 5 — IF THIS CONVERSATION BREAKS
The owner starts a fresh conversation, re-attaches THIS file, and says
"continue from the first unfinished task." Verify the DONE list against
the live site — never redo, never re-ask, never fetch URLs for content.
If WordPress logs out, stop and ask the owner to log back in.

*(When sent as an attachment, FRAGMENTS 1–5 follow below this line.)*
