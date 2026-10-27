import Collection from "./collection.js";

interface UsageLabels {
	title: string;
	total: string;
	proofOfWork: string;
	image: string;
	slider: string;
	iconOrder: string;
	audio: string;
}

interface StateLabels {
	lastRefreshedAt: string;
	refreshNow: string;
	failedToLoad: string;
	loading: string;
}

interface TrafficDataLabels {
	title: string;
	message: string;
	buttonLabel: string;
}

interface AboutLabels {
	title: string;
	message: string;
	buttonLabel: string;
}

interface ChallengeLabels {
	title: string;
	current: string;
	changeOnPaidPlan: string;
	changeInPortal: string;
	buttonLabel: string;
	types: Record<string, string>;
}

interface Config {
	getSiteKey(): string;

	getSecretKey(): string;

	getUsageLabels(): UsageLabels;

	getStateLabels(): StateLabels;

	getTrafficDataLabels(): TrafficDataLabels;

	getChallengeLabels(): ChallengeLabels;

	getAboutLabels(): AboutLabels;

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
			slider: usageLabels.getString("slider"),
			iconOrder: usageLabels.getString("iconOrder"),
			audio: usageLabels.getString("audio"),
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
			message: trafficDataLabels.getString("message"),
			buttonLabel: trafficDataLabels.getString("buttonLabel"),
		};
	}

	public getAboutLabels(): AboutLabels {
		const aboutLabels = this.data.getSubCollection("aboutLabels");

		return {
			title: aboutLabels.getString("title"),
			message: aboutLabels.getString("message"),
			buttonLabel: aboutLabels.getString("buttonLabel"),
		};
	}

	public getChallengeLabels(): ChallengeLabels {
		const challengeLabels = this.data.getSubCollection("challengeLabels");
		const types = challengeLabels.getSubCollection("types");

		return {
			title: challengeLabels.getString("title"),
			current: challengeLabels.getString("current"),
			changeOnPaidPlan: challengeLabels.getString("changeOnPaidPlan"),
			changeInPortal: challengeLabels.getString("changeInPortal"),
			buttonLabel: challengeLabels.getString("buttonLabel"),
			types: Object.fromEntries(
				[
					"frictionless",
					"pow",
					"image",
					"puzzle",
					"iconOrder",
					"audio",
				].map((type: string): [string, string] => [
					type,
					types.getString(type),
				]),
			),
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
	UsageLabels,
	StateLabels,
	TrafficDataLabels,
	ChallengeLabels,
	AboutLabels,
};
