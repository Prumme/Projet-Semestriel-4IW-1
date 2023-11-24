/** @type {import('tailwindcss').Config} */
module.exports = {
  content: [
    "./assets/**/*.js",
    "./assets/**/*.scss",
    "./templates/**/*.html.twig",
  ],
  safelist: [
    {
      pattern: /(bg|text|border|fill)-.*/,
      variants: ["hover"],
    },
  ],
  theme: {
    extend: {
      colors: {
        primary: "#49AB9A",
        secondary: "#1E143B",
        white: "#F2F4F8",
        primary: {
          "50": "#f8fcfb",
          "100": "#edf7f6",
          "200": "#dbf0ec",
          "300": "#bfe3dd",
          "400": "#7ec8bc",
          "500": "#49AB9A",
          "600": "#32766b",
          "700": "#25564e",
          "800": "#15322d",
          "900": "#0b1917",
          "950": "#060e0d"
        },
        secondary: "#1E143B",
        white: "#F2F4F8",
        black: "#1E1E1E",
        secondary_light: "#21295C",
        primary_darken: "#337D70",
        danger: "#F34E29",
        warning: "#FFD339",
        disabled: "#BEDFD9",
      },
      // fontFamily: {
      //   sans: ["Lexend", "sans-serif"],
      // },
    },
  },
  plugins: [require("@tailwindcss/forms")],
};
