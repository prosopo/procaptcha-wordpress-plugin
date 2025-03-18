import Collection from "./collection.js";

interface AccountLabels {
	title: string;
	name: string;
	tier: string;
}

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

interface CaptchaSettingsLabels {
	title: string;
	type: string;
	frictionlessThreshold: string;
	powDifficulty: string;
	level: {
		low: string;
		normal: string;
		high: string;
	};
	types: {
		proofOfWork: string;
		image: string;
		frictionless: string;
	};
}

interface DomainLabels {
	title: string;
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

	getAccountLabels(): AccountLabels;

	getUsageLabels(): UsageLabels;

	getStateLabels(): StateLabels;

	getCaptchaSettingsLabels(): CaptchaSettingsLabels;

	getDomainLabels(): DomainLabels;

	getTrafficDataLabels(): TrafficDataLabels;

	getCallToUpgradeElementMarkup(): string;

	isDebugMode(): boolean;

	getAccountApiEndpoint(): string;
}

declare global {
	interface Window {
		prosopoProcaptchaWpSettings: object;
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

	public getAccountLabels(): AccountLabels {
		const accountLabels = this.data.getSubCollection("accountLabels");

		return {
			title: accountLabels.getString("title"),
			name: accountLabels.getString("name"),
			tier: accountLabels.getString("tier"),
		};
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

	public getCaptchaSettingsLabels(): CaptchaSettingsLabels {
		const captchaSettingsLabels = this.data.getSubCollection(
			"captchaSettingsLabels",
		);

		const level = captchaSettingsLabels.getSubCollection("level");
		const types = captchaSettingsLabels.getSubCollection("types");

		return {
			title: captchaSettingsLabels.getString("title"),
			type: captchaSettingsLabels.getString("type"),
			frictionlessThreshold: captchaSettingsLabels.getString(
				"frictionlessThreshold",
			),
			powDifficulty: captchaSettingsLabels.getString("powDifficulty"),
			level: {
				low: level.getString("low"),
				normal: level.getString("normal"),
				high: level.getString("high"),
			},
			types: {
				proofOfWork: types.getString("proofOfWork"),
				image: types.getString("image"),
				frictionless: types.getString("frictionless"),
			},
		};
	}

	public getDomainLabels(): DomainLabels {
		const domainLabels = this.data.getSubCollection("domainLabels");

		return {
			title: domainLabels.getString("title"),
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

export {
	Config,
	ConfigClass,
	AccountLabels,
	UsageLabels,
	StateLabels,
	TrafficDataLabels,
};
