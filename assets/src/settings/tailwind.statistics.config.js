import path from "path";
import { addIconSelectors } from "@iconify/tailwind";
import tailwindTheme from "./tailwind-theme.json";

module.exports = {
	content: [path.resolve(__dirname, "./statistics/*.tsx")],
	theme: {
		extend: tailwindTheme,
	},
	plugins: [addIconSelectors(["material-symbols", "eos-icons"])],
};
