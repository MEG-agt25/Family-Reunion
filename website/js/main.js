/* Harris-Nelson Family Reunion — shared nav, profile, and form logic */
(function () {
  var cfg = window.HN_CONFIG || {};

  /* ================= profile store ================= */
  function getProfile() {
    try { return JSON.parse(localStorage.getItem("hn-profile")) || null; } catch (e) { return null; }
  }
  window.HN_getProfile = getProfile;

  /* ================= site nav (injected on every page) ================= */
  var NAV_HTML =
    '<a href="index.html">Home</a>' +
    '<a href="tree.html">Family Tree</a>' +
    '<a href="shop.html">Shop</a>' +
    '<a href="give.html">Give</a>' +
    '<span class="nav-more"><button type="button" aria-haspopup="true" aria-expanded="false">⋯ More</button>' +
    '<span class="dropdown" role="menu">' +
      '<span><h4>Our Story</h4>' +
        '<a href="history.html">Our History<small>From Mississippi to Cleveland &amp; beyond</small></a>' +
        '<a href="mildred-tree.html">Interactive Family Tree<small>Mildred’s branch — tap + add</small></a>' +
        '<a href="gallery.html">Photos &amp; Slideshow<small>Reunion 2024 + the family album</small></a>' +
        '<a href="features.html">Everything This Site Does<small>The feature tour</small></a>' +
      '</span>' +
      '<span><h4>Get Involved</h4>' +
        '<a href="committees.html">Committees &amp; Chairs<small>11 committees — sign up to serve</small></a>' +
        '<a href="superlatives.html">Superlatives Voting<small>Give your people their flowers</small></a>' +
        '<a href="hardship.html">Helping Hands Fund<small>Confidential hardship aid</small></a>' +
        '<a href="tree.html">Family Tree Form<small>Add yourself &amp; your children</small></a>' +
      '</span>' +
      '<span><h4>Family Business</h4>' +
        '<a href="business.html">Officers &amp; Governance<small>The board, finances, the foundation</small></a>' +
        '<a href="constitution.html">Constitution &amp; Bylaws<small>How we govern — printable</small></a>' +
        '<a href="give.html">The Three Funds<small>Land · scholarships · operating</small></a>' +
        '<a href="shop.html">Dues &amp; T-Shirts<small>Pay &amp; order online</small></a>' +
      '</span>' +
    '</span></span>';

  var nav = document.querySelector("header nav");
  if (nav) {
    var p = getProfile();
    var first = p && p["Full name"] ? p["Full name"].split(" ")[0] : null;
    var chip = first
      ? '<a class="profile-chip" href="account.html">👤 ' + first + "</a>"
      : '<a class="profile-chip" href="account.html">👤 Create My Profile</a>';
    nav.innerHTML = NAV_HTML + chip;

    var more = nav.querySelector(".nav-more");
    var btn = more.querySelector("button");
    btn.addEventListener("click", function (e) {
      e.stopPropagation();
      more.classList.toggle("open");
      btn.setAttribute("aria-expanded", more.classList.contains("open"));
    });
    document.addEventListener("click", function () { more.classList.remove("open"); });
    document.addEventListener("keydown", function (e) { if (e.key === "Escape") more.classList.remove("open"); });

    var here = location.pathname.split("/").pop() || "index.html";
    nav.querySelectorAll("a").forEach(function (a) {
      if (a.getAttribute("href") === here) a.classList.add("here");
    });
  }

  /* ================= payment buttons ================= */
  document.querySelectorAll("[data-pay]").forEach(function (el) {
    var url = (cfg.PAYMENTS || {})[el.getAttribute("data-pay")] || "";
    if (url) { el.href = url; } else { el.style.display = "none"; }
  });
  document.querySelectorAll("[data-pay-fallback]").forEach(function (el) {
    var key = el.getAttribute("data-pay-fallback");
    if ((cfg.PAYMENTS || {})[key]) el.style.display = "none";
  });

  /* ================= profile pre-fill on every form ================= */
  var prof = getProfile();
  if (prof) {
    var MAP = {
      "Full name": prof["Full name"], "Applicant name": prof["Full name"],
      "Student name": prof["Full name"], "Voter name": prof["Full name"],
      "Email": prof["Email"], "Phone": prof["Phone"],
      "Family branch": prof["Family branch"], "Voter branch": prof["Family branch"],
      "Parents": prof["Parents"], "Grandparents": prof["Grandparents"],
      "Spouse": prof["Spouse"], "Children": prof["Children"],
      "City and state": prof["City and state"]
    };
    document.querySelectorAll("form[data-hn-form] input, form[data-hn-form] select, form[data-hn-form] textarea").forEach(function (el) {
      var v = MAP[el.name];
      if (v && !el.value) el.value = v;
    });
  }

  /* ================= activity log ================= */
  function logActivity(what) {
    try {
      var a = JSON.parse(localStorage.getItem("hn-activity")) || [];
      a.unshift({ what: what, when: new Date().toLocaleString() });
      localStorage.setItem("hn-activity", JSON.stringify(a.slice(0, 30)));
    } catch (e) {}
  }
  window.HN_logActivity = logActivity;

  /* ================= form submission ================= */
  document.querySelectorAll("form[data-hn-form]").forEach(function (form) {
    form.addEventListener("submit", function (e) {
      e.preventDefault();
      var status = form.querySelector(".form-status");
      var label = form.getAttribute("data-hn-form");
      var data = new FormData(form);
      data.append("_form", label);
      data.append("_submitted", new Date().toISOString());

      function say(msg, ok) {
        if (!status) return;
        status.textContent = msg;
        status.className = "form-status " + (ok ? "ok" : "err");
      }

      if (cfg.FORM_ENDPOINT) {
        fetch(cfg.FORM_ENDPOINT, { method: "POST", body: data, headers: { Accept: "application/json" } })
          .then(function (r) {
            if (!r.ok) throw new Error("HTTP " + r.status);
            logActivity(label);
            if (!form.hasAttribute("data-hn-keep")) form.reset();
            say("Thank you! Your submission was received. 💛", true);
          })
          .catch(function () {
            say("Something went wrong — please try again, or email " + cfg.FAMILY_EMAIL, false);
          });
      } else {
        var lines = ["[" + label + " submission]", ""];
        data.forEach(function (v, k) {
          if (k.charAt(0) !== "_" && String(v).trim() !== "") lines.push(k + ": " + v);
        });
        location.href = "mailto:" + cfg.FAMILY_EMAIL +
          "?subject=" + encodeURIComponent("HN Reunion — " + label) +
          "&body=" + encodeURIComponent(lines.join("\n"));
        logActivity(label);
        say("Opening your email app to send this to " + cfg.FAMILY_EMAIL + " …", true);
      }
    });
  });
})();
