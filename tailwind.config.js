/** @type {import('tailwindcss').Config} */
module.exports = {
  content: ["./assets/**/*.js","./assets/**/*.scss", "./templates/**/*.html.twig"],
  theme: {
    extend: {
      colors: {
        primary: {
          100: "#49AB9A",
          200: "#3E9E8D",
          300: "#62BCAC",
          400: "#287473",
          500: "#337D70",
          600: "#3D8E80",
          700: "#54C7B7",
          800: "#2E7881",
        },
        secondary: "#0A1B3A",
        secondaryLight: "#21295C",
        white: "#F5F5F5",
        black: "#1E1E1E",
      },
      // fontFamily: {
      //   sans: ["Lexend", "sans-serif"],
      // },
    },
  },
  plugins: [
      require("@tailwindcss/forms"),
  ],
};
