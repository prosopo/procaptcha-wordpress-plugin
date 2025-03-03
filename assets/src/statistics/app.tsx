import * as React from "react";
import {StatCurrentState, StatState, StatStateElement} from "./statState";
import type {Api} from "./api";
import {Config, ConfigClass} from "./config";
import {UsageInfo, UsageInfoElement} from "./usageInfo";
import {InfoBox, InfoBoxElement} from "./infoBox";
import NumberUtils from "./numberUtils";
import {TrafficData, TrafficDataElement} from "./trafficData";
import ModuleLogger from "../logger/moduleLogger";
import LoggerFactory from "../logger/loggerFactory";
import LoggerInterface from "../interfaces/loggerInterface";

interface AppState {
    statState: StatState;
    usageInfo: UsageInfo;
    accountInfo: InfoBox;
    captchaSettings: InfoBox;
    domains: InfoBox;
    trafficData: TrafficData;
}

enum userTier {
    FREE = "free",
}

class App extends React.Component<object, AppState> {
    private api: Api | null = null;
    private config: Config;
    private numberUtils: NumberUtils;
    private logger: LoggerInterface;
    private userTier: string;

    constructor(props) {
        super(props);

        const loggerFactory = new LoggerFactory();

        this.userTier = "";
        this.config = new ConfigClass();
        this.logger = loggerFactory.makeLogger(
            "statistics",
            new ModuleLogger(),
        );
        this.numberUtils = new NumberUtils();

        this.state = this.getInitialState();

        this.refresh();
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
                refresh: this.refresh.bind(this),
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

    protected async refreshUserData(): Promise<void> {
        const api = await this.getApi();
        const userData = await api.getUserData();

        this.userTier = userData.tier;

        this.setState((actualState) => ({
            ...actualState,
            accountInfo: {
                ...actualState.accountInfo,
                items: [
                    {
                        label: this.config.getAccountLabels().tier,
                        value: userData.tier.toUpperCase(),
                    },
                    {
                        label: this.config.getAccountLabels().name,
                        value: userData.name,
                    },
                    {
                        label: this.config.getAccountLabels().email,
                        value: userData.email,
                    },
                ],
            },
            usageInfo: {
                ...actualState.usageInfo,
                limits: {
                    verifications: userData.monthlyUsage.limits.verifications,
                },
                image: {
                    submissions: userData.monthlyUsage.image.submissions,
                    verifications: userData.monthlyUsage.image.verifications,
                    total: userData.monthlyUsage.image.total,
                },
                pow: {
                    submissions: userData.monthlyUsage.pow.submissions,
                    verifications: userData.monthlyUsage.pow.verifications,
                    total: userData.monthlyUsage.pow.total,
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

    protected async refreshUserSettings(): Promise<void> {
        const api = await this.getApi();
        const userSettings = await api.getUserSettings();

        this.setState((actualState) => ({
            ...actualState,
            captchaSettings: {
                ...actualState.captchaSettings,
                items: [
                    {
                        label: this.config.getCaptchaSettingsLabels().type,
                        value: this.getTypeLabel(userSettings.captchaType),
                    },
                    {
                        label: this.config.getCaptchaSettingsLabels()
                            .frictionlessThreshold,
                        value: this.getFrictionlessThresholdLabel(
                            userSettings.frictionlessThreshold,
                        ),
                    },
                    {
                        label: this.config.getCaptchaSettingsLabels()
                            .powDifficulty,
                        value: this.getPowDifficultyLabel(
                            userSettings.powDifficulty,
                        ),
                    },
                ],
            },
            domains: {
                ...actualState.domains,
                items: userSettings.domains.map((domain, index) => {
                    return {
                        label: "#" + (index + 1),
                        value: domain,
                    };
                }),
            },
        }));
    }

    public async refresh(): Promise<void> {
        this.setState(this.getInitialState());

        try {
            await Promise.all([
                this.refreshUserData(),
                this.refreshUserSettings(),
            ]);

            this.markAsLoaded();

            // fixme remove
            /*if (userTier.FREE === this.userTier) {

            }
              await this.refreshTrafficData();*/
        } catch (e) {
            this.markAsFailed();
        }
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
                    refresh={statState.refresh}
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
