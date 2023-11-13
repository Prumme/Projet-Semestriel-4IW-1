/** @type {import('tailwindcss').Config} */
module.exports = {
  content: [
    "./assets/**/*.js",
    "./assets/**/*.scss",
    "./templates/**/*.html.twig",
  ],
  theme: {
    extend: {
      colors: {
        primary: "#49AB9A",
        secondary: "#1E143B",
        white: "#F2F4F8",
        black: "#1E1E1E",
        secondary_light: "#21295C",
        primary_darken: "#337D70",
        danger: "#F34E29",
        warning: "#FFD339",
        disabled: "#BEDFD9",
      },
    },
  },
  plugins: [require("@tailwindcss/forms")],
};
