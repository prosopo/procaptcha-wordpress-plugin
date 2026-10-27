import Collection from "./collection.js";

interface UsageLabels {
	title: string;
	total: string;
	proofOfWork: string;
	image: string;
	puzzle: string;
	iconOrder: string;
	audio: string;
}

interface StateLabels {
	lastRefreshedAt: string;
	refreshNow: string;
	tryAgain: string;
	failedToLoad: string;
	loading: string;
}

interface TrafficDataLabels {
	title: string;
	message: string;
	buttonLabel: string;
}

interface InsightsLabels {
	botsTitle: string;
	botsCaption: string;
	invisibleTitle: string;
	invisibleCaption: string;
	dailyTitle: string;
	dailyUsed: string;
	dailyForecast: string;
	dailyForecastOver: string;
	showTable: string;
	hideTable: string;
	dayColumn: string;
	requestsColumn: string;
	totalRow: string;
	setupTitle: string;
	served: string;
	solved: string;
	checked: string;
	setupOk: string;
	setupWarn: string;
	setupWarnLink: string;
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

	getInsightsLabels(): InsightsLabels;

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
			puzzle: usageLabels.getString("puzzle"),
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
			tryAgain: stateLabels.getString("tryAgain"),
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

	public getInsightsLabels(): InsightsLabels {
		const labels = this.data.getSubCollection("insightsLabels");

		return {
			botsTitle: labels.getString("botsTitle"),
			botsCaption: labels.getString("botsCaption"),
			invisibleTitle: labels.getString("invisibleTitle"),
			invisibleCaption: labels.getString("invisibleCaption"),
			dailyTitle: labels.getString("dailyTitle"),
			dailyUsed: labels.getString("dailyUsed"),
			dailyForecast: labels.getString("dailyForecast"),
			dailyForecastOver: labels.getString("dailyForecastOver"),
			showTable: labels.getString("showTable"),
			hideTable: labels.getString("hideTable"),
			dayColumn: labels.getString("dayColumn"),
			requestsColumn: labels.getString("requestsColumn"),
			totalRow: labels.getString("totalRow"),
			setupTitle: labels.getString("setupTitle"),
			served: labels.getString("served"),
			solved: labels.getString("solved"),
			checked: labels.getString("checked"),
			setupOk: labels.getString("setupOk"),
			setupWarn: labels.getString("setupWarn"),
			setupWarnLink: labels.getString("setupWarnLink"),
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
	InsightsLabels,
};
