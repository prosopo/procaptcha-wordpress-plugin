import { AccountTiers } from "../account/accountTiers.js";
import { ProsopoApi } from "../prosopoApi.js";
import LoggerFactory from "../../logger/loggerFactory.js";
import PluginModuleLogger from "../../logger/plugin/pluginModuleLogger.js";
import { ConfigClass, type Config } from "../statistics/config.js";
import { Account } from "../account/account.js";
import { accountSchema } from "../account/accountSchema.js";

class GeneralSettings {
	private readonly config: Config;
	private readonly prosopoApi: ProsopoApi;

	public constructor() {
		const loggerFactory = new LoggerFactory();
		const pluginModuleLogger = new PluginModuleLogger();
		const logger = loggerFactory.createLogger(
			"general-settings",
			pluginModuleLogger,
		);

		this.config = new ConfigClass();
		this.prosopoApi = new ProsopoApi(logger);
	}

	public async displayUpgradeBannerForFreeAccountTier(): Promise<void> {
		const currentAccount = await this.getCurrentAccount();
		const accountTier = currentAccount?.tier || "";

		if (AccountTiers.FREE === accountTier) {
			this.displayUpgradeTierBanner();
		}
	}

	protected displayUpgradeTierBanner(): void {
		// todo
	}

	protected async getCurrentAccount(): Promise<Account | null> {
		const siteKey = this.config.getSiteKey();
		const secretKey = this.config.getSecretKey();

		return siteKey.length > 0 && secretKey.length > 0
			? this.resolveAccount(siteKey, secretKey)
			: null;
	}

	protected async resolveAccount(
		sitePublicKey: string,
		sitePrivateKey: string,
	): Promise<Account> {
		const account = await this.prosopoApi.requestEndpoint(
			this.config.getAccountApiEndpoint(),
			sitePrivateKey,
			{
				siteKey: sitePublicKey,
			},
		);

		return accountSchema.parse(account);
	}
}

const generalSettings = new GeneralSettings();

generalSettings.displayUpgradeBannerForFreeAccountTier();
