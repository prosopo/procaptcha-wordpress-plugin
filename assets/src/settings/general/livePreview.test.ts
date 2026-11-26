import { describe, expect, it } from "vitest";
import { type AppearanceFields, previewAppearance } from "./livePreview.js";

const fields: AppearanceFields = {
	theme: "theme",
	shape: "shape",
	font: "font",
	palette: [
		{ key: "primary", field: "palette_primary" },
		{ key: "textSecondary", field: "palette_text_secondary" },
	],
};

const values = (entries: Record<string, string>): Map<string, string> =>
	new Map(Object.entries(entries));

describe("previewAppearance", () => {
	it("uses the theme and corners from the form", () => {
		expect(
			previewAppearance(
				values({ theme: "dark", shape: "square" }),
				fields,
			),
		).toEqual({ theme: "dark", shape: "square" });
	});

	it("falls back to the defaults when a choice is missing", () => {
		expect(previewAppearance(values({}), fields)).toEqual({
			theme: "light",
			shape: "rounded",
		});
	});

	it("maps colour fields to the widget's palette keys", () => {
		expect(
			previewAppearance(
				values({
					theme: "light",
					shape: "rounded",
					palette_primary: "#0A7D3B",
					palette_text_secondary: "#abc",
				}),
				fields,
			).palette,
		).toEqual({ primary: "#0a7d3b", textSecondary: "#aabbcc" });
	});

	it("leaves out blank and invalid colours, so the theme default shows", () => {
		const appearance = previewAppearance(
			values({ palette_primary: "", palette_text_secondary: "blue" }),
			fields,
		);

		expect(appearance).not.toHaveProperty("palette");
	});

	it("passes a safe font list and drops an unsafe one", () => {
		expect(
			previewAppearance(values({ font: ' "Inter", serif ' }), fields)
				.font,
		).toBe('"Inter", serif');
		expect(
			previewAppearance(values({ font: "x; } body {" }), fields),
		).not.toHaveProperty("font");
	});
});
