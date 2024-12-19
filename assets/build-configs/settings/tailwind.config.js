const path = require("path");
const tailwindTheme = require("../tailwind-theme.json");

module.exports = {
	content: [
		path.resolve(
			__dirname,
			"../../../prosopo-procaptcha/src/views/settings/*.blade.php",
		),
	],
	theme: {
		extend: tailwindTheme,
	},
	plugins: [],
};
