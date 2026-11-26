import { describe, expect, it } from "vitest";
import { swatchColor } from "./colorField.js";

describe("swatchColor", () => {
	it("keeps a six-digit hex code, lowercased", () => {
		expect(swatchColor("#0A7D3B")).toBe("#0a7d3b");
	});

	it("expands a three-digit hex code for the picker", () => {
		expect(swatchColor("#f0a")).toBe("#ff00aa");
	});

	it("ignores surrounding spaces", () => {
		expect(swatchColor("  #123456 ")).toBe("#123456");
	});

	it("means the theme default when empty", () => {
		expect(swatchColor("")).toBeNull();
	});

	it("means the theme default for anything that is not a hex code", () => {
		for (const value of ["red", "#12345", "123456", "#ggg", "rgb(0,0,0)"]) {
			expect(swatchColor(value)).toBeNull();
		}
	});
});
