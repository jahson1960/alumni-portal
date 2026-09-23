/** @type {import('tailwindcss').Config} */
module.exports = {
  content: [
    "./app/Views/**/*.php",
    "./app/Core/helpers.php",
  ],
  theme: {
    extend: {
      colors: {
        "primary-navy": "#091a2e",
        // Driven by --btn-600-rgb / --btn-700-rgb, injected per-request from the admin
        // theme settings (see theme_style() in app/Core/helpers.php). The literal fallback
        // keeps every bg-gold/text-gold/etc. usage looking right even before that <style>
        // block loads, and <alpha-value> keeps slash-opacity modifiers (bg-gold/10) working.
        gold: "rgb(var(--btn-600-rgb, 212 147 38) / <alpha-value>)",
        "gold-hover": "rgb(var(--btn-700-rgb, 184 126 29) / <alpha-value>)",
      },
      fontFamily: {
        sans: ["Inter", "sans-serif"],
      },
    },
  },
  plugins: [
    require("@tailwindcss/forms"),
    require("@tailwindcss/typography"),
  ],
};
