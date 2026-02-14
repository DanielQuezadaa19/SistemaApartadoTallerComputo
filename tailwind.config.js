/** @type {import('tailwindcss').Config} */
module.exports = {
  content: [
    "./dashboard.php",
    "./public/**/*.php",
    "./public/**/*.html"
  ],
  theme: {
    extend: {
      spacing: {
        '1/6': '16.666667%',
      },
      width: {
        '1/6': '16.666667%',
      }
    },
  },
  plugins: [],
}
