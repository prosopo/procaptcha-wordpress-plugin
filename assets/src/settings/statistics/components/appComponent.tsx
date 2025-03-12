import * as React from "react";
import {
	StatCurrentState,
	AppStatusComponent,
	AppStatus,
} from "./appStatusComponent.js";
import { Config, ConfigClass } from "../config.js";
import {
	CaptchaUsageComponent,
	CaptchaUsage,
} from "./captchaUsageComponent.js";
import { ListComponent, List } from "./listComponent.js";
import NumberUtils from "../numberUtils.js";
import {
	TrafficAnalyticsComponent,
	TrafficData,
} from "./trafficAnalyticsComponent.js";
import PluginModuleLogger from "../../../logger/plugin/pluginModuleLogger.js";
import LoggerFactory from "../../../logger/loggerFactory.js";
import Logger from "../../../logger/logger.js";
import { Api } from "../api.js";
import type { Account } from "../account/account.js";
import { AboutComponent } from "./aboutComponent.js";

interface AppState {
	statState: AppStatus;
	usageInfo: CaptchaUsage;
	accountInformation: List;
	captchaSettings: List;
	domains: List;
	trafficData: TrafficData;
}

class AppComponent extends React.Component<object, AppState> {
	private api: Api | null = null;
	private config: Config;
	private numberUtils: NumberUtils;
	private logger: Logger;
	private accountTier: string;

	constructor(props) {
		super(props);

		const loggerFactory = new LoggerFactory();

		this.accountTier = "";
		this.config = new ConfigClass();
		this.logger = loggerFactory.createLogger(
			"statistics",
			new PluginModuleLogger(),
		);
		this.numberUtils = new NumberUtils();

		this.state = this.getInitialState();
	}

	protected async getApi(): Promise<Api> {
		if (null === this.api) {
			const ApiClass = (await import("../api.js")).Api;
			this.api = new ApiClass(this.config, this.logger);
		}

		return this.api;
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
				isSupported: false,
				logger: this.logger,
				labels: this.config.getTrafficDataLabels(),
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

	protected refreshUserData(account: Account): void {
		this.accountTier = account.tier;

		this.setState((actualState) => ({
			...actualState,
			accountInformation: {
				...actualState.accountInformation,
				items: [
					{
						label: this.config.getAccountLabels().tier,
						value: account.tier.toUpperCase(),
					},
					{
						label: this.config.getAccountLabels().name,
						value: account.name,
					},
				],
			},
			usageInfo: {
				...actualState.usageInfo,
				limits: {
					verifications: account.monthlyUsage.limit,
				},
				image: {
					submissions: account.monthlyUsage.image.submissions,
					verifications: account.monthlyUsage.image.verifications,
					total: account.monthlyUsage.image.total,
				},
				pow: {
					submissions: account.monthlyUsage.pow.submissions,
					verifications: account.monthlyUsage.pow.verifications,
					total: account.monthlyUsage.pow.total,
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

	protected refreshUserSettings(account: Account): void {
		this.setState((actualState) => ({
			...actualState,
			captchaSettings: {
				...actualState.captchaSettings,
				items: [
					{
						label: this.config.getCaptchaSettingsLabels().type,
						value: this.getTypeLabel(account.settings.captchaType),
					},
					{
						label: this.config.getCaptchaSettingsLabels()
							.frictionlessThreshold,
						value: this.getFrictionlessThresholdLabel(
							account.settings.frictionlessThreshold,
						),
					},
					{
						label: this.config.getCaptchaSettingsLabels()
							.powDifficulty,
						value: this.getPowDifficultyLabel(
							account.settings.powDifficulty,
						),
					},
				],
			},
			domains: {
				...actualState.domains,
				items: account.settings.domains.map((domain, index) => {
					return {
						label: "#" + (index + 1),
						value: domain,
					};
				}),
			},
		}));
	}

	protected async refreshData(): Promise<void> {
		try {
			const api = await this.getApi();
			const account = await api.getAccount();

			this.refreshUserData(account);
			this.refreshUserSettings(account);

			this.markAsLoaded();
		} catch (e) {
			this.logger.warning("Failed to refresh data", {
				error: e,
			});

			this.markAsFailed();
		}
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
				<AboutComponent />
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
						isSupported={trafficData.isSupported}
						labels={trafficData.labels}
					/>
				</div>
			</div>
		);
	}
}

export { AppComponent };
