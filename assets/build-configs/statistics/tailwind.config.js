const path = require("path");
const tailwindTheme = require("../tailwind-theme.json");
const { addIconSelectors } = require("@iconify/tailwind");

module.exports = {
	content: [path.resolve(__dirname, "../../src/statistics/*.tsx")],
	theme: {
		extend: tailwindTheme,
	},
	plugins: [addIconSelectors(["material-symbols", "eos-icons"])],
};
