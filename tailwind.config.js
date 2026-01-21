/** @type {import('tailwindcss').Config} */
module.exports = {
  content: [
    './resources/**/*.blade.php',
    './resources/**/*.vue',
    './resources/**/*.js',
  ],
  safelist: [
    "bg-[url('/img/login-bg.jpg')]",
    "bg-gray-500",
  ]
}
