/** @type {import('tailwindcss').Config} */
module.exports = {
  darkMode: "class",
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
        white: "var(--white)",
        primary: {
          50: "var(--primary-50)",
          100: "var(--primary-100)",
          200: "var(--primary-200)",
          300: "var(--primary-300)",
          400: "var(--primary-400)",
          450: "var(--primary-450)",
          500: "var(--primary-500)",
          550: "var(--primary-550)",
          600: "var(--primary-600)",
          700: "var(--primary-700)",
          750: "var(--primary-750)",
          800: "var(--primary-800)",
          900: "var(--primary-900)",
          950: "var(--primary-950)",
        },
        secondary: {
          400: "var(--secondary-400)",
          500: "var(--secondary-500)",
          600: "var(--secondary-600)",
        },
        danger: {
          500: "var(--danger-500)",
        },
        error: {
          500: "var(--danger-500)",
        },
        success: {
          500: "var(--success-500)",
        },
        warning: {
          500: "var(--warning-500)",
        },
        info: {
          500: "var(--info-500)",
        },
        black: "var(--black)",
        green_blue: "var(--green_blue)",
        secondary_light: "var(--secondary-light)",
        primary_darken: "var(--primary-darken)",
        disabled: "var(--disabled)",

        "plain-darkmode": "var(--plain-darkmode)",
        "text-darkmode": "var(--text-darkmode)",
      },
    },
  },
  plugins: [require("@tailwindcss/forms")],
};
