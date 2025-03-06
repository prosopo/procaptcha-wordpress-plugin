import path from "path";
import tailwindTheme from "./tailwind-theme.json";

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
};
