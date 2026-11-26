import Collection from "#settings/statistics/collection.js";
import type { AppearanceFields } from "./livePreview.js";

declare global {
	interface Window {
		prosopoProcaptchaWpSettings: Record<string, unknown>;
	}
}

class GeneralSettingsConfig {
	private data: Collection;

	constructor() {
		const rawData =
			window.hasOwnProperty("prosopoProcaptchaWpSettings") &&
			"object" === typeof window["prosopoProcaptchaWpSettings"]
				? window["prosopoProcaptchaWpSettings"]
				: {};

		this.data = new Collection(rawData);
	}

	public getSiteKey(): string {
		return this.data.getString("siteKey");
	}

	public getSecretKey(): string {
		return this.data.getString("secretKey");
	}

	public getAccountApiEndpoint(): string {
		return this.data.getString("accountApiEndpoint");
	}

	public getAppearanceFields(): AppearanceFields {
		const fields = this.data.getSubCollection("appearanceFields");

		return {
			theme: fields.getString("theme"),
			shape: fields.getString("shape"),
			font: fields.getString("font"),
			palette: this.data.getArray("paletteFields").flatMap((item) => {
				if ("object" !== typeof item || null === item) {
					return [];
				}

				const entry = new Collection(item as Record<string, unknown>);
				const key = entry.getString("key");
				const field = entry.getString("field");

				return "" !== key && "" !== field ? [{ key, field }] : [];
			}),
		};
	}
}

export { GeneralSettingsConfig };
