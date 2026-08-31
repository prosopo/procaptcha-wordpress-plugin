import Collection from "./collection.js";

interface UsageLabels {
	title: string;
	total: string;
	proofOfWork: string;
	image: string;
}

interface StateLabels {
	lastRefreshedAt: string;
	refreshNow: string;
	failedToLoad: string;
	loading: string;
}

interface TrafficDataLabels {
	title: string;
	chartTitle: string;
	powSubmissions: string;
	imageSubmissions: string;
	time: string;
	submissionsCount: string;
}

interface Config {
	getSiteKey(): string;

	getSecretKey(): string;

	getUsageLabels(): UsageLabels;

	getStateLabels(): StateLabels;

	getTrafficDataLabels(): TrafficDataLabels;

	getCallToUpgradeElementMarkup(): string;

	isDebugMode(): boolean;

	getAccountApiEndpoint(): string;
}

declare global {
	interface Window {
		prosopoProcaptchaWpSettings: Record<string, unknown>;
	}
}

class ConfigClass implements Config {
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

	public getUsageLabels(): UsageLabels {
		const usageLabels = this.data.getSubCollection("usageLabels");

		return {
			title: usageLabels.getString("title"),
			total: usageLabels.getString("total"),
			proofOfWork: usageLabels.getString("proofOfWork"),
			image: usageLabels.getString("image"),
		};
	}

	public isDebugMode(): boolean {
		return this.data.getBool("isDebugMode");
	}

	public getStateLabels(): StateLabels {
		const stateLabels = this.data.getSubCollection("stateLabels");

		return {
			lastRefreshedAt: stateLabels.getString("lastRefreshedAt"),
			refreshNow: stateLabels.getString("refreshNow"),
			failedToLoad: stateLabels.getString("failedToLoad"),
			loading: stateLabels.getString("loading"),
		};
	}

	public getTrafficDataLabels(): TrafficDataLabels {
		const trafficDataLabels =
			this.data.getSubCollection("trafficDataLabels");

		return {
			title: trafficDataLabels.getString("title"),
			chartTitle: trafficDataLabels.getString("chartTitle"),
			powSubmissions: trafficDataLabels.getString("powSubmissions"),
			imageSubmissions: trafficDataLabels.getString("imageSubmissions"),
			time: trafficDataLabels.getString("time"),
			submissionsCount: trafficDataLabels.getString("submissionsCount"),
		};
	}

	public getCallToUpgradeElementMarkup(): string {
		return this.data.getString("callToUpgradeElementMarkup");
	}

	public getAccountApiEndpoint(): string {
		return this.data.getString("accountApiEndpoint");
	}
}

export { Config, ConfigClass, UsageLabels, StateLabels, TrafficDataLabels };
