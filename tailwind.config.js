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
        secondary: "#1E143B",
        white: "#F2F4F8",
        primary: {
          50: "#f8fcfb",
          100: "#edf7f6",
          200: "#dbf0ec",
          300: "#bfe3dd",
          400: "#7ec8bc",
          450: "#54C7B7",
          500: "#49AB9A",
          600: "#32766b",
          700: "#25564e",
          750: "#0E4138",
          800: "#15322d",
          900: "#0b1917",
          950: "#060e0d",
        },
        secondary: {
          400: "#21295C",
          500: "#1E143B",
          600: "#1a1234",
        },
        danger: {
          500: "#F34E29",
        },
        black: "#1E1E1E",
        green_blue: "#2e7881",
        secondary_light: "#21295C",
        primary_darken: "#337D70",
        warning: "#FFD339",
        disabled: "#BEDFD9",
      },
    },
  },
  plugins: [require("@tailwindcss/forms")],
};
