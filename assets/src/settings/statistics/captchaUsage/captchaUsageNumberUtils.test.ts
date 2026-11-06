import { afterEach, describe, expect, it, vi } from "vitest";
import CaptchaUsageNumberUtils from "./captchaUsageNumberUtils.js";

const utils: CaptchaUsageNumberUtils = new CaptchaUsageNumberUtils();

describe("CaptchaUsageNumberUtils", () => {
	afterEach(() => {
		vi.unstubAllGlobals();
	});

	it("returns 0 percent when the value or the total is 0", () => {
		expect(utils.getPercent(0, 10)).toBe(0);
		expect(utils.getPercent(5, 0)).toBe(0);
	});

	it("rounds the percent to two decimals", () => {
		expect(utils.getPercent(1, 3)).toBe(33.33);
		expect(utils.getPercent(2, 3)).toBe(66.67);
	});

	it("raises tiny non-zero percents to 3 so they stay visible", () => {
		expect(utils.visualizePercent(0)).toBe(0);
		expect(utils.visualizePercent(0.5)).toBe(3);
		expect(utils.visualizePercent(3)).toBe(3);
		expect(utils.visualizePercent(42)).toBe(42);
	});

	it("formats numbers in the page language", () => {
		vi.stubGlobal("document", { documentElement: { lang: "de-DE" } });
		expect(utils.formatNumber(1234567)).toBe("1.234.567");
	});

	it("formats numbers as en-GB when the page has no language", () => {
		vi.stubGlobal("document", { documentElement: { lang: "" } });
		expect(utils.formatNumber(1234567)).toBe("1,234,567");
	});
});
