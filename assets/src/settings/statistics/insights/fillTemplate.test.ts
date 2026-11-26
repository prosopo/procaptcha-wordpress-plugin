import { describe, expect, it } from "vitest";
import { fillTemplate } from "./fillTemplate.js";

describe("fillTemplate", () => {
	it("replaces every named placeholder", () => {
		expect(
			fillTemplate("{used} of {limit}", { used: "5", limit: "10" }),
		).toBe("5 of 10");
	});

	it("keeps placeholders without a value", () => {
		expect(fillTemplate("{used} of {limit}", { used: "5" })).toBe(
			"5 of {limit}",
		);
	});

	it("leaves a template without placeholders unchanged", () => {
		expect(fillTemplate("no placeholders", {})).toBe("no placeholders");
	});
});
