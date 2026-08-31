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

import type { ProcaptchaAccount } from "#settings/procaptcha/procaptchaAccount.js";
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
}

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
				accountTier: "",
				logger: this.logger,
				labels: this.config.getTrafficDataLabels(),
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
			},
		}));
	}

	protected refreshTrafficData(account: ProcaptchaAccount): void {
		this.setState((actualState) => ({
			...actualState,
			trafficData: {
				...actualState.trafficData,
				accountTier: account.tier,
				labels: this.config.getTrafficDataLabels(),
				callToUpgradeElementMarkup:
					this.config.getCallToUpgradeElementMarkup(),
			},
		}));
	}

	protected async refreshData(): Promise<void> {
		const site = await this.siteResolver.resolveSite(
			this.siteApiCredentials,
		);

		if (site) {
			this.refreshUsage(site);
			this.refreshTrafficData(site.account);

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

	public render() {
		const { statState, usageInfo, trafficData } = this.state;

		return (
			<div className="flex flex-col gap-5">
				<AboutAppComponent />
				<AppStatusComponent
					labels={statState.labels}
					state={statState.state}
					reload={statState.reload}
				/>
				<div className="grid gap-8 grid-cols-2">
					<CaptchaUsageComponent
						numberUtils={usageInfo.numberUtils}
						labels={usageInfo.labels}
						limits={usageInfo.limits}
						image={usageInfo.image}
						pow={usageInfo.pow}
					/>
					<TrafficAnalyticsComponent
						classes="col-span-2"
						logger={trafficData.logger}
						accountTier={trafficData.accountTier}
						labels={trafficData.labels}
						callToUpgradeElementMarkup={
							trafficData.callToUpgradeElementMarkup
						}
					/>
				</div>
			</div>
		);
	}
}

export { AppComponent };
