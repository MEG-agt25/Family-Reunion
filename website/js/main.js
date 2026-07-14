/* Harris-Nelson Family Reunion — shared form + payment-link logic */
(function () {
  var cfg = window.HN_CONFIG || {};

  // Highlight current nav item
  var here = location.pathname.split("/").pop() || "index.html";
  document.querySelectorAll("nav a").forEach(function (a) {
    if (a.getAttribute("href") === here) a.classList.add("here");
  });

  // Wire payment buttons: any element with data-pay="KEY" gets the URL
  // from HN_CONFIG.PAYMENTS[KEY]; hidden if not configured yet.
  document.querySelectorAll("[data-pay]").forEach(function (el) {
    var url = (cfg.PAYMENTS || {})[el.getAttribute("data-pay")] || "";
    if (url) { el.href = url; } else { el.style.display = "none"; }
  });
  document.querySelectorAll("[data-pay-fallback]").forEach(function (el) {
    var key = el.getAttribute("data-pay-fallback");
    var url = (cfg.PAYMENTS || {})[key] || "";
    if (url) el.style.display = "none"; // hide "coming soon" note once live
  });

  // Generic form handling: POST to FORM_ENDPOINT if configured,
  // otherwise open a pre-filled email to the family inbox.
  document.querySelectorAll("form[data-hn-form]").forEach(function (form) {
    form.addEventListener("submit", function (e) {
      e.preventDefault();
      var status = form.querySelector(".form-status");
      var data = new FormData(form);
      data.append("_form", form.getAttribute("data-hn-form"));
      data.append("_submitted", new Date().toISOString());

      function say(msg, ok) {
        if (!status) return;
        status.textContent = msg;
        status.className = "form-status " + (ok ? "ok" : "err");
      }

      if (cfg.FORM_ENDPOINT) {
        fetch(cfg.FORM_ENDPOINT, {
          method: "POST",
          body: data,
          headers: { Accept: "application/json" }
        })
          .then(function (r) {
            if (!r.ok) throw new Error("HTTP " + r.status);
            form.reset();
            say("Thank you! Your submission was received. 💛", true);
          })
          .catch(function () {
            say("Something went wrong — please try again, or email us at " + cfg.FAMILY_EMAIL, false);
          });
      } else {
        // Email fallback: build a readable message body
        var lines = ["[" + form.getAttribute("data-hn-form") + " submission]", ""];
        data.forEach(function (v, k) {
          if (k.charAt(0) !== "_" && String(v).trim() !== "") lines.push(k + ": " + v);
        });
        var subject = "HN Reunion — " + form.getAttribute("data-hn-form");
        location.href = "mailto:" + cfg.FAMILY_EMAIL +
          "?subject=" + encodeURIComponent(subject) +
          "&body=" + encodeURIComponent(lines.join("\n"));
        say("Opening your email app to send this to " + cfg.FAMILY_EMAIL + " …", true);
      }
    });
  });
})();
