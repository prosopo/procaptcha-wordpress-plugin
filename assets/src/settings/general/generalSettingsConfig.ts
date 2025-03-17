import Collection from "../statistics/collection.js";

declare global {
	interface Window {
		prosopoProcaptchaWpSettings: object;
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
}

export { GeneralSettingsConfig };
