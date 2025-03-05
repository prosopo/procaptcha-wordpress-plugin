const path = require("path");

module.exports = {
    content: [
        path.resolve(
            __dirname,
            "../../../prosopo-procaptcha/src/views/settings/*.blade.php",
        ),
    ],
    theme: {
        extend: require("./tailwind-theme.json"),
    },
};
