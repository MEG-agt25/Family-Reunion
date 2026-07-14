# Family Reunion — Harris-Nelson Family Reunion Project

Everything for the Harris-Nelson Family Reunion: the business-meeting
presentation, the code for **hnfamilyreunion.com**, and the governance /
501(c)(3) playbooks.

## What's here

| Path | What it is |
|---|---|
| `presentation/index.html` | **The reunion presentation** — a self-contained slide deck (open in any browser; ← → keys to navigate, `P` to print). 28 slides: welcome, weekend schedule, family history, trivia, yearbook, Reunion 2024 memories, acknowledgments, superlatives voting, tribute, financial report, officers & Robert's Rules, board & committee recognition (incl. the new Membership Chair seat), committee chair sign-ups, next-reunion location options, website plans, Where Is My Land fundraising, scholarships, the Helping Hands hardship fund, 501(c)(3) foundation, and the family tree project. |
| `website/` | **Starter code for hnfamilyreunion.com** — a complete static site, no build step needed. |
| `presentation/Harris-Nelson-Reunion-2026.pptx` | **Editable PowerPoint version** of the deck (26 slides) — open in PowerPoint or import into Canva to edit names, captions, and numbers. Regenerate anytime with `python3 presentation/build_pptx.py`. |
| `docs/constitution-and-bylaws.md` | Draft Constitution & Bylaws (Robert's Rules of Order as parliamentary authority) ready for adoption — bracketed items are the family's choices to vote on. |
| `docs/Hardship-Fund-Application.docx` | Printable Helping Hands hardship application (paper version of hardship.html). |
| `docs/roberts-rules-quick-guide.md` | One-page Robert's Rules handout for business meetings. |
| `docs/501c3-roadmap.md` | Step-by-step checklist to form the family foundation in the next few months. |
| `data/Latest-Family-Tree.ged` | The Ancestry GEDCOM export (July 2026, 3,465 people) — source of truth for tree dates; keep this updated after each Ancestry sync. |

## The website (`website/`)

| Page | Purpose |
|---|---|
| `index.html` | Home — next reunion, registration button, missions |
| `history.html` | The known family history (from "That Aunt") + the nine branches |
| `tree.html` | Family Tree Project form (mirrors the Google Form) — feeds the Ancestry tree & printed poster |
| `committees.html` | All eleven committees (incl. Courtesy, led by the Hospitality Chair, and Protocol, chaired by the Parliamentarian) with **chair/member sign-up** + volunteer questionnaire (adapted from the WIML questionnaire) |
| `superlatives.html` | Family superlatives **voting ballot** (awarded at each reunion) |
| `gallery.html` | Photo gallery + fullscreen auto-playing slideshow of all 123 slides from Reunion 2024 (Cleveland) — "We All We Got… We All We Need!" (photos in `photos/2024/`) |
| `mildred-tree.html` | **Interactive Mildred Harris branch tree** (1942–2012) built from the Ancestry GEDCOM + written history — "+ add" buttons on every person, weekend additions saved locally and exportable as CSV for the Historian |
| `constitution.html` | The Constitution & Bylaws as a readable, printable web page |
| `hardship.html` | Helping Hands hardship fund — program rules, deadlines, rubric, and the confidential application form |
| `shop.html` | T-shirt orders + registration/dues payment buttons |
| `give.html` | The three funds: land-back (Where Is My Land + land renovation), scholarships, and the operating fund (website hosting + seed money) + scholarship interest form |
| `business.html` | Officers, Robert's Rules, financial reports, 501(c)(3) roadmap |

### Launching the site (Technology Committee checklist)
1. **Preview locally:** open `website/index.html` in a browser — it just works.
2. **Configure:** edit `website/js/config.js`:
   - `FORM_ENDPOINT` — create a free form endpoint (Formspree, Netlify Forms, or a
     Google Apps Script web app) and paste the URL. Until then, every form falls
     back to opening an email to harrisnelsonfamilyreunion@gmail.com.
   - `PAYMENTS.*` — paste Stripe Payment Links / PayPal links for registration,
     t-shirts, and the three donation funds. Buttons stay hidden until filled in.
     Add the Cash App cashtag and Zelle info too.
3. **Deploy:** upload the `website/` folder to any static host (Netlify, Vercel,
   GitHub Pages, or the hosting that comes with the hnfamilyreunion.com domain).
4. **Point the domain** hnfamilyreunion.com at the host.

No card numbers are ever handled by the site itself — payments go through the
processor's hosted links.

## Reunion 2026 facts baked in
Cleveland, OH · July 17–19, 2026 · "Together Again!" / "Here we go Again…
Kick'n it with the Kins!" — full weekend schedule (Andrews Osborne Park →
Clay's Park → SiteCenters Beachwood), dues ($125/$175/$225), shirt prices
($10, 2XL–4XL $15), payment info (Zelle: harrisnelsonfamilyreunion@gmail.com ·
Cash App: $mieshanulife06), and the July 13, 2026 financial report
($5,540 collected · $1,675 outstanding · $7,625 expenses · $410 short).
Individual household balances are deliberately NOT published — those stay
with the Treasurer's dues sheet.

## Fill-in-later placeholders
Anything in `[square brackets]` (photo names/captions, memorial names,
accomplishment lists, fundraising split percentages) is waiting on real
information. Search the files for `[` to find them all. Photo captions are
easiest to edit in the `.pptx` (PowerPoint/Canva).

## Family links referenced throughout
- Email: harrisnelsonfamilyreunion@gmail.com
- History & yearbook doc (Google Docs), Canva photo album, yearbook post (X),
  Family Tree Google Form, family history trivia (email) — see the "All the
  Links" slide in the presentation
- Land-back partner: https://whereismyland.com
