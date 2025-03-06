import * as React from "react";
import { StatCurrentState, StatState, StatStateElement } from "./statState";
import { Config, ConfigClass } from "./config";
import { UsageInfo, UsageInfoElement } from "./usageInfo";
import { InfoBox, InfoBoxElement } from "./infoBox";
import NumberUtils from "./numberUtils";
import { TrafficData, TrafficDataElement } from "./trafficData";
import ModuleLogger from "../../logger/moduleLogger";
import LoggerFactory from "../../logger/loggerFactory";
import LoggerInterface from "../../interfaces/loggerInterface";
import { Api } from "./api";
import type { Account } from "./account/account";

interface AppState {
	statState: StatState;
	usageInfo: UsageInfo;
	accountInfo: InfoBox;
	captchaSettings: InfoBox;
	domains: InfoBox;
	trafficData: TrafficData;
}

class App extends React.Component<object, AppState> {
	private api: Api | null = null;
	private config: Config;
	private numberUtils: NumberUtils;
	private logger: LoggerInterface;
	private accountTier: string;

	constructor(props) {
		super(props);

		const loggerFactory = new LoggerFactory();

		this.accountTier = "";
		this.config = new ConfigClass();
		this.logger = loggerFactory.makeLogger(
			"statistics",
			new ModuleLogger(),
		);
		this.numberUtils = new NumberUtils();

		this.state = this.getInitialState();
	}

	protected async getApi(): Promise<Api> {
		if (null === this.api) {
			const ApiClass = (await import("./api")).Api;
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
			accountInfo: {
				title: this.config.getAccountLabels().title,
				icon: "material-symbols--account-circle",
				items: [
					{
						label: this.config.getAccountLabels().tier,
						value: "...",
					},
					{
						label: this.config.getAccountLabels().name,
						value: "...",
					},
					{
						label: this.config.getAccountLabels().email,
						value: "...",
					},
				],
			},
			captchaSettings: {
				title: this.config.getCaptchaSettingsLabels().title,
				icon: "material-symbols--settings",
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
				icon: "material-symbols--domain",
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
			accountInfo: {
				...actualState.accountInfo,
				items: [
					{
						label: this.config.getAccountLabels().tier,
						value: account.tier.toUpperCase(),
					},
					{
						label: this.config.getAccountLabels().name,
						value: account.name,
					},
					/* fixme remove from label{
                         label: this.config.getAccountLabels().email,
                         value: account.email,
                     },*/
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
			accountInfo,
			captchaSettings,
			domains,
			trafficData,
		} = this.state;

		return (
			<div className="flex flex-col gap-5">
				<StatStateElement
					labels={statState.labels}
					state={statState.state}
					reload={statState.reload}
				/>
				<div className="grid gap-8 grid-cols-2">
					{/* Usage Info */}
					<UsageInfoElement
						numberUtils={usageInfo.numberUtils}
						labels={usageInfo.labels}
						limits={usageInfo.limits}
						image={usageInfo.image}
						pow={usageInfo.pow}
					/>
					{/* Account Info */}
					<InfoBoxElement
						title={accountInfo.title}
						icon={accountInfo.icon}
						items={accountInfo.items}
					/>
					{/* Captcha Settings */}
					<InfoBoxElement
						title={captchaSettings.title}
						icon={captchaSettings.icon}
						items={captchaSettings.items}
					/>
					{/* Domains */}
					<InfoBoxElement
						title={domains.title}
						icon={domains.icon}
						items={domains.items}
					/>
					{/* Traffic Data */}
					<TrafficDataElement
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

export default App;
