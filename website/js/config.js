/* ============================================================
   Harris-Nelson Family Reunion — site configuration
   Fill these in once and every page/form picks them up.
   ============================================================ */
window.HN_CONFIG = {
  // Where form submissions go. Easiest no-server option: create a free
  // form at https://formspree.io (or Google Apps Script / Netlify Forms)
  // and paste the endpoint URL here. Leave "" to fall back to email.
  FORM_ENDPOINT: "",

  // Fallback + contact address (already set up)
  FAMILY_EMAIL: "harrisnelsonfamilyreunion@gmail.com",

  // Payment links — create these in your processor dashboards and paste
  // the URLs. Buttons stay hidden until a link is filled in.
  PAYMENTS: {
    REGISTRATION: "",   // e.g. Stripe Payment Link for reunion registration/dues
    TSHIRT: "",         // e.g. Stripe/PayPal link for t-shirt orders
    DONATE_LAND: "",    // Where Is My Land retainer fund
    DONATE_SCHOLARSHIP: "",
    DONATE_SEED: "",    // operating fund: website hosting + seed money for future reunions
    CASHAPP: "$mieshanulife06", // Miesha Wilson
    ZELLE: "harrisnelsonfamilyreunion@gmail.com" // Joseph Nelson — family reunion bank account
  },

  // External family links
  LINKS: {
    TREE_GOOGLE_FORM: "https://docs.google.com/forms/d/1Qiij5SyUJgV2qcYrMzBqY6h9ABc_ihg0OyhuHQWB5MY/viewform",
    HISTORY_DOC: "https://docs.google.com/document/d/1-XkKF4JBdL16uDzvXoB_foBZHAK3n_-f1kWKDvcqh7U/edit?tab=t.0",
    CANVA_ALBUM: "https://canva.link/fqxj2trjluu9fl5",
    YEARBOOK_POST: "https://x.com/tendin2/status/17914652",
    WHERE_IS_MY_LAND: "https://whereismyland.com"
  }
};
