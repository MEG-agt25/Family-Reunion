=== Harris-Nelson Voting Portal ===
Contributors: harrisnelsonfamily
Tags: voting, ballots, family, members
Requires at least: 6.0
Tested up to: 6.8
Requires PHP: 7.4
Stable tag: 1.0.0
License: GPLv2 or later

Family ballots for the Harris-Nelson Family Reunion — build a ballot in
wp-admin, members vote on the site, the Secretary exports the results.

== Description ==

* **Ballots are posts.** Create a Ballot (wp-admin -> Ballots) with a
  title, description, open/close window, and any mix of single-choice and
  write-in questions. One-click **Duplicate ballot** for the next vote.
* **Eligibility per ballot:** Dues-current members (via the Member
  Benefits Bridge plugin's hn_is_current_member(), soft dependency),
  anyone logged in, or open (collects name + family branch instead).
  If the Bridge is inactive, dues ballots fall back to logged-in-only
  and an admin notice tells you.
* **One vote per member,** enforced server-side (unique key per
  user+ballot). Optional "voters may revise until close".
* **Front end:** [hn_ballot id="123"] — mobile-first, no external
  CSS/JS, warm family styling, friendly before-open/after-close
  messages with the family motto.
* **Results:** admin-only screen with live counts, tie flagging, and
  CSV export for the Secretary's minutes. Optional
  [hn_ballot_results id="123"] publishes AGGREGATE totals only, after
  close, only when you tick "publish totals". Individual votes are
  never rendered publicly; write-in text never appears publicly.
* **Data:** custom votes table (dbDelta), prepared statements, nonces,
  capability checks. Uninstall keeps all data unless you opt in to
  deletion under Ballots -> Settings.
* **Seed:** activation creates a draft "Constitution & Bylaws 2026"
  ballot pre-loaded with the family constitution's 14 bracketed
  decisions.

== Installation ==

1. Plugins -> Add New -> Upload Plugin -> hn-voting-portal.zip -> Activate.
2. Ballots -> edit the seeded draft (or add a new one), set the open/close
   window and eligibility, Publish.
3. Put [hn_ballot id="ID"] on any page (the ID shows in the Ballots list).
4. After close: Ballots -> Results -> Export CSV for the minutes.

== Changelog ==

= 1.0.0 =
* First release: ballots CPT, eligibility levels, one-vote enforcement,
  revise option, results + CSV, publish-totals shortcode, seeded 2026
  Constitution & Bylaws ballot.
