/** @type {import('tailwindcss').Config} */
module.exports = {
    content: [
        "./src/**/*.{html,ts}",
    ],
    theme: {
        extend: {
            colors: {
                primary: '#3b82f6', // Blue 500
                secondary: '#64748b', // Slate 500
            }
        },
    },
    plugins: [],
}
