import * as React from "react";
import {
	AppStatusComponent,
	AppStatusComponentProperties,
	StatCurrentState,
} from "./appStatusComponent.js";
import {
	TrafficAnalyticsComponent,
	TrafficAnalyticsComponentProperties,
} from "./trafficAnalyticsComponent.js";
import { AboutAppComponent } from "./aboutAppComponent.js";
import { BotsStoppedComponent } from "#settings/statistics/insights/botsStoppedComponent.js";
import { InvisibleShareComponent } from "#settings/statistics/insights/invisibleShareComponent.js";
import { DailyRequestsComponent } from "#settings/statistics/insights/dailyRequestsComponent.js";
import { SetupCheckComponent } from "#settings/statistics/insights/setupCheckComponent.js";
import type { CaptchaUsage } from "#settings/procaptcha/procaptchaSite.js";
import {
	ChallengeComponent,
	type ChallengeComponentProperties,
} from "./challengeComponent.js";
import {
	type ApiCredentials,
	SiteApiCredentials,
} from "#settings/procaptcha/api/apiCredentials.js";
import {
	CaptchaUsageComponent,
	type CaptchaUsageComponentProperties,
} from "#settings/statistics/captchaUsage/captchaUsageComponent.js";
import type Logger from "#utils/logger/logger.js";
import { type Config, ConfigClass } from "#settings/statistics/config.js";
import CaptchaUsageNumberUtils from "#settings/statistics/captchaUsage/captchaUsageNumberUtils.js";
import type { ProcaptchaSite } from "#settings/procaptcha/procaptchaSite.js";
import {
	ApiClient,
	type ProcaptchaSiteResolver,
} from "#settings/procaptcha/api/apiClient.js";

interface AppComponentProperties {
	logger: Logger;
}

interface AppState {
	statState: AppStatusComponentProperties;
	usageInfo: CaptchaUsageComponentProperties;
	trafficData: TrafficAnalyticsComponentProperties;
	challenge: ChallengeComponentProperties;
	site: ProcaptchaSite | null;
}

const usageOf = (site: ProcaptchaSite): CaptchaUsage[] =>
	[
		site.monthlyUsage.pow,
		site.monthlyUsage.image,
		site.monthlyUsage.puzzle,
		site.monthlyUsage.iconOrder,
		site.monthlyUsage.audio,
	].filter((usage): usage is CaptchaUsage => undefined !== usage);

const sumOf = (
	usages: CaptchaUsage[],
	pick: (usage: CaptchaUsage) => number,
): number =>
	usages.reduce(
		(sum: number, usage: CaptchaUsage): number => sum + pick(usage),
		0,
	);

class AppComponent extends React.Component<AppComponentProperties, AppState> {
	private readonly siteResolver: ProcaptchaSiteResolver;
	private readonly siteApiCredentials: ApiCredentials;
	private readonly config: Config;
	private readonly numberUtils: CaptchaUsageNumberUtils;
	private readonly logger: Logger;

	constructor(props: AppComponentProperties) {
		super(props);

		this.config = new ConfigClass();
		this.logger = props.logger;
		this.numberUtils = new CaptchaUsageNumberUtils();

		this.siteResolver = new ApiClient(
			this.config.getAccountApiEndpoint(),
			this.logger,
		);
		this.siteApiCredentials = new SiteApiCredentials(
			this.config.getSiteKey(),
			this.config.getSecretKey(),
		);

		this.state = this.getInitialState();
	}

	protected getInitialState(): AppState {
		return {
			statState: {
				state: StatCurrentState.LOADING,
				reload: this.reload.bind(this),
				labels: this.config.getStateLabels(),
			},
			usageInfo: {
				numberUtils: this.numberUtils,
				limits: {
					verifications: 0,
				},
				image: {
					submissions: 0,
					verifications: 0,
					total: 0,
				},
				pow: {
					submissions: 0,
					verifications: 0,
					total: 0,
				},
				labels: this.config.getUsageLabels(),
			},
			trafficData: {
				labels: this.config.getTrafficDataLabels(),
			},
			site: null,
			challenge: {
				labels: this.config.getChallengeLabels(),
				accountTier: "",
				captchaType: "",
				callToUpgradeElementMarkup:
					this.config.getCallToUpgradeElementMarkup(),
			},
		};
	}

	protected markAsLoaded(): void {
		this.setState((actualState) => ({
			...actualState,
			statState: {
				...actualState.statState,
				state: StatCurrentState.LOADED,
			},
		}));
	}

	protected markAsFailed(): void {
		this.setState((actualState) => ({
			...actualState,
			statState: {
				...actualState.statState,
				state: StatCurrentState.FAILED,
			},
		}));
	}

	protected refreshUsage(site: ProcaptchaSite): void {
		this.setState((actualState) => ({
			...actualState,
			usageInfo: {
				...actualState.usageInfo,
				limits: {
					verifications: site.monthlyUsage.limit,
				},
				image: {
					submissions: site.monthlyUsage.image.submissions,
					verifications: site.monthlyUsage.image.verifications,
					total: site.monthlyUsage.image.total,
				},
				pow: {
					submissions: site.monthlyUsage.pow.submissions,
					verifications: site.monthlyUsage.pow.verifications,
					total: site.monthlyUsage.pow.total,
				},
				puzzle: site.monthlyUsage.puzzle,
				iconOrder: site.monthlyUsage.iconOrder,
				audio: site.monthlyUsage.audio,
			},
		}));
	}

	protected refreshChallenge(site: ProcaptchaSite): void {
		this.setState((actualState) => ({
			...actualState,
			site,
			challenge: {
				...actualState.challenge,
				accountTier: site.account.tier,
				captchaType: site.settings?.captchaType ?? "",
			},
		}));
	}

	protected async refreshData(): Promise<void> {
		const site = await this.siteResolver.resolveSite(
			this.siteApiCredentials,
		);

		if (site) {
			this.refreshUsage(site);
			this.refreshChallenge(site);

			this.markAsLoaded();

			return;
		}

		this.markAsFailed();
	}

	public componentDidMount(): void {
		this.refreshData();
	}

	public async reload(): Promise<void> {
		this.setState(this.getInitialState());

		await this.refreshData();
	}

	protected renderInsights(site: ProcaptchaSite): React.ReactNode {
		const labels = this.config.getInsightsLabels();
		const usages = usageOf(site);

		return (
			<>
				{site.monthlySessions && (
					<div className="grid gap-8 grid-cols-2">
						<BotsStoppedComponent
							sessions={site.monthlySessions}
							labels={labels}
							numberUtils={this.numberUtils}
						/>
						<InvisibleShareComponent
							sessions={site.monthlySessions}
							labels={labels}
							numberUtils={this.numberUtils}
						/>
					</div>
				)}
				<DailyRequestsComponent
					days={site.dailyRequests ?? []}
					used={sumOf(
						usages,
						(usage: CaptchaUsage) => usage.verifications,
					)}
					limit={site.monthlyUsage.limit}
					now={new Date()}
					labels={labels}
					numberUtils={this.numberUtils}
				/>
			</>
		);
	}

	public render() {
		const { statState, usageInfo, trafficData, challenge, site } =
			this.state;
		const usages = site ? usageOf(site) : [];

		return (
			<div className="flex flex-col gap-5">
				<AboutAppComponent labels={this.config.getAboutLabels()} />
				<AppStatusComponent
					labels={statState.labels}
					state={statState.state}
					reload={statState.reload}
				/>
				{StatCurrentState.LOADED === statState.state && site && (
					<>
						{this.renderInsights(site)}
						<div className="grid gap-8 grid-cols-2">
							<CaptchaUsageComponent
								numberUtils={usageInfo.numberUtils}
								labels={usageInfo.labels}
								limits={usageInfo.limits}
								image={usageInfo.image}
								pow={usageInfo.pow}
								puzzle={usageInfo.puzzle}
								iconOrder={usageInfo.iconOrder}
								audio={usageInfo.audio}
							/>
							<ChallengeComponent {...challenge} />
							<SetupCheckComponent
								served={sumOf(
									usages,
									(usage: CaptchaUsage) => usage.total,
								)}
								solved={sumOf(
									usages,
									(usage: CaptchaUsage) => usage.submissions,
								)}
								checked={sumOf(
									usages,
									(usage: CaptchaUsage) =>
										usage.verifications,
								)}
								labels={this.config.getInsightsLabels()}
								numberUtils={this.numberUtils}
							/>
							<TrafficAnalyticsComponent
								labels={trafficData.labels}
							/>
						</div>
					</>
				)}
			</div>
		);
	}
}

export { AppComponent };
