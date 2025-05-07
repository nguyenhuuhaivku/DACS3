import defaultTheme from "tailwindcss/defaultTheme";
import forms from "@tailwindcss/forms";

/** @type {import('tailwindcss').Config} */
export default {
    content: [
        "./vendor/laravel/framework/src/Illuminate/Pagination/resources/views/*.blade.php",
        "./storage/framework/views/*.php",
        "./resources/views/**/*.blade.php",
    ],

    theme: {
        extend: {
            fontFamily: {
                sans: ["Figtree", ...defaultTheme.fontFamily.sans],
            },
        },
    },
    plugins: [forms],
};

module.exports = {
    content: [
        "./resources/views/admin/dashboard.blade.php",
        "./resources/css/app.css",
        "./resources/js/app.js",
    ],
    theme: {
        extend: {},
    },
    plugins: [],
};
