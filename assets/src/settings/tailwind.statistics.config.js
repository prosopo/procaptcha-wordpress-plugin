const path = require("path");
const {addIconSelectors} = require("@iconify/tailwind");

module.exports = {
    content: [
        path.resolve(__dirname, "./statistics/*.tsx")
    ],
    theme: {
        extend: require("./tailwind-theme.json"),
    },
    plugins: [addIconSelectors(["material-symbols", "eos-icons"])],
};
