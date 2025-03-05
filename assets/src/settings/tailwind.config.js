const path = require("path");
const {addIconSelectors} = require("@iconify/tailwind");

module.exports = {
    content: [
        path.resolve(
            __dirname,
            "../../../prosopo-procaptcha/src/views/settings/*.blade.php",
        ),
        path.resolve(__dirname, "./statistics/*.tsx")
    ],
    theme: {
        extend: {
            "colors": {
                "blue": "#2271b1",
                "blue-dark": "#135e96",
                "gray": "#8c8f94"
            },
            "fontFamily": {
                "sans": [
                    "ui-sans-serif",
                    "system-ui",
                    "sans-serif",
                    "Apple Color Emoji",
                    "Segoe UI Emoji",
                    "Segoe UI Symbol",
                    "Noto Color Emoji"
                ]
            }
        },
    },
    plugins: [addIconSelectors(["material-symbols", "eos-icons"])],
};
