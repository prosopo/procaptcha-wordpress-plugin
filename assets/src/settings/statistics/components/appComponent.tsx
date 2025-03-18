import * as React from "react";
import {
	StatCurrentState,
	AppStatusComponent,
	AppStatusComponentProperties,
} from "./appStatusComponent.js";
import { Config, ConfigClass } from "../config.js";
import {
	CaptchaUsageComponent,
	CaptchaUsageComponentProperties,
} from "../captchaUsage/captchaUsageComponent.js";
import { ListComponent, ListComponentProperties } from "./listComponent.js";
import CaptchaUsageNumberUtils from "../captchaUsage/captchaUsageNumberUtils.js";
import {
	TrafficAnalyticsComponent,
	TrafficAnalyticsComponentProperties,
} from "./trafficAnalyticsComponent.js";
import Logger from "../../../logger/logger.js";
import { AboutAppComponent } from "./aboutAppComponent.js";
import type { ProsopoAccountApi } from "../../account/prosopoAccountApi.js";
import type { Account } from "../../account/account.js";
import type { Site } from "../site/site.js";
import type { SiteSettings } from "../site/settings/siteSettings.js";
import { accountSchema } from "../../account/accountSchema.js";
import { siteSchema } from "../site/siteSchema.js";
import type { AccountApiResolver } from "../../account/accountApiResolver.js";
import type { SiteApiResolver } from "../site/siteApiResolver.js";
import type { ApiCredentials } from "../../apiCredentials.js";

interface AppComponentProperties {
	logger: Logger;
}

interface AppState {
	statState: AppStatusComponentProperties;
	usageInfo: CaptchaUsageComponentProperties;
	accountInformation: ListComponentProperties;
	captchaSettings: ListComponentProperties;
	domains: ListComponentProperties;
	trafficData: TrafficAnalyticsComponentProperties;
}

class AppComponent extends React.Component<AppComponentProperties, AppState> {
	private readonly accountApiResolver: AccountApiResolver;
	private readonly siteApiResolver: SiteApiResolver;
	private readonly accountCredentials: ApiCredentials;
	private readonly siteCredentials: ApiCredentials;
	private readonly config: Config;
	private readonly numberUtils: CaptchaUsageNumberUtils;
	private readonly logger: Logger;

	constructor(props: AppComponentProperties) {
		super(props);

		this.config = new ConfigClass();
		this.logger = props.logger;
		this.numberUtils = new CaptchaUsageNumberUtils();

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
			accountInformation: {
				title: this.config.getAccountLabels().title,
				icon: "icon-[material-symbols--account-circle]",
				items: [
					{
						label: this.config.getAccountLabels().tier,
						value: "...",
					},
					{
						label: this.config.getAccountLabels().name,
						value: "...",
					},
				],
			},
			captchaSettings: {
				title: this.config.getCaptchaSettingsLabels().title,
				icon: "icon-[material-symbols--settings]",
				items: [
					{
						label: this.config.getCaptchaSettingsLabels().type,
						value: "...",
					},
					{
						label: this.config.getCaptchaSettingsLabels()
							.frictionlessThreshold,
						value: "...",
					},
					{
						label: this.config.getCaptchaSettingsLabels()
							.powDifficulty,
						value: "...",
					},
				],
			},
			domains: {
				title: this.config.getDomainLabels().title,
				icon: "icon-[material-symbols--domain]",
				items: [],
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

	protected refreshUserData(site: Site): void {
		this.setState((actualState) => ({
			...actualState,
			accountInformation: {
				...actualState.accountInformation,
				items: [
					{
						label: this.config.getAccountLabels().tier,
						value: site.account.tier.toUpperCase(),
					},
					{
						label: this.config.getAccountLabels().name,
						value: site.name,
					},
				],
			},
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

	protected getPowDifficultyLabel(powDifficulty: number): string {
		const levelLabels = this.config.getCaptchaSettingsLabels().level;

		if (powDifficulty === 4) {
			return levelLabels.normal;
		}

		return powDifficulty < 4 ? levelLabels.low : levelLabels.high;
	}

	protected getFrictionlessThresholdLabel(
		frictionlessThreshold: number,
	): string {
		const levelLabels = this.config.getCaptchaSettingsLabels().level;

		if (frictionlessThreshold >= 0.4 && frictionlessThreshold <= 0.6) {
			return levelLabels.normal;
		}

		return frictionlessThreshold < 0.4 ? levelLabels.high : levelLabels.low;
	}

	protected getTypeLabel(type: string): string {
		const typeLabels = this.config.getCaptchaSettingsLabels().types;

		switch (type) {
			case "image":
				return typeLabels.image;
			case "pow":
				return typeLabels.proofOfWork;
			default:
				return typeLabels.frictionless;
		}
	}

	protected refreshSiteSettings(siteSettings: SiteSettings): void {
		const domains = siteSettings.domains
			.filter((domain) => "*" !== domain)
			.map((domain, index) => {
				return {
					label: "#" + (index + 1),
					value: domain,
				};
			});

		this.setState((actualState) => ({
			...actualState,
			captchaSettings: {
				...actualState.captchaSettings,
				items: [
					{
						label: this.config.getCaptchaSettingsLabels().type,
						value: this.getTypeLabel(siteSettings.captchaType),
					},
					{
						label: this.config.getCaptchaSettingsLabels()
							.frictionlessThreshold,
						value: this.getFrictionlessThresholdLabel(
							siteSettings.frictionlessThreshold,
						),
					},
					{
						label: this.config.getCaptchaSettingsLabels()
							.powDifficulty,
						value: this.getPowDifficultyLabel(
							siteSettings.powDifficulty,
						),
					},
				],
			},
			domains: {
				...actualState.domains,
				items: domains,
			},
		}));
	}

	protected refreshTrafficData(account: Account): void {
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
		const site = await this.siteApiResolver.resolveSite(
			this.siteCredentials,
		);

		if (site) {
			this.refreshUserData(site);
			this.refreshSiteSettings(site.settings);
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
		const {
			statState,
			usageInfo,
			accountInformation,
			captchaSettings,
			domains,
			trafficData,
		} = this.state;

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
					<ListComponent
						title={accountInformation.title}
						icon={accountInformation.icon}
						items={accountInformation.items}
					/>
					<ListComponent
						title={captchaSettings.title}
						icon={captchaSettings.icon}
						items={captchaSettings.items}
					/>
					<ListComponent
						title={domains.title}
						icon={domains.icon}
						items={domains.items}
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
