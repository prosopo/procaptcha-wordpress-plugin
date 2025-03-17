import { Account } from "../account/account.js";
import { accountSchema } from "../account/accountSchema.js";
import type { WebComponent } from "../../webComponent/webComponent.js";
import type { ProsopoApi } from "../prosopoApi.js";
import type Logger from "../../logger/logger.js";
import { AccountTiers } from "../account/accountTiers.js";

class GeneralSettingsWebComponent implements WebComponent {
	private prosopoApi: ProsopoApi | null = null;

	public constructor(
		private readonly accountApiEndpoint: string,
		private readonly publicSiteKey: string,
		private readonly privateSiteKey: string,
		private readonly logger: Logger,
	) {
		this.prosopoApi = null;
	}

	constructComponent(element: HTMLElement): void {
		const tierUpgradeBanner = element.querySelector(
			".general-procaptcha-settings__tier-upgrade",
		);

		if (tierUpgradeBanner instanceof HTMLElement) {
			this.displayUpgradeBannerForFreeAccountTier(tierUpgradeBanner);
		}
	}

	protected async displayUpgradeBannerForFreeAccountTier(
		tierUpgradeBanner: HTMLElement,
	): Promise<void> {
		const account = await this.getAccount();

		if (AccountTiers.FREE === account.tier) {
			tierUpgradeBanner.setAttribute("data-visible", "true");
		}
	}

	protected async getAccount(): Promise<Account> {
		const prosopoApi = await this.getProsopoApi();

		const account = await prosopoApi.requestEndpoint(
			this.accountApiEndpoint,
			this.privateSiteKey,
			{
				siteKey: this.publicSiteKey,
			},
		);

		return accountSchema.parse(account);
	}

	protected async getProsopoApi(): Promise<ProsopoApi> {
		if (null === this.prosopoApi) {
			const ProsopoApiClass = (await import("../prosopoApi.js"))
				.ProsopoApi;
			this.prosopoApi = new ProsopoApiClass(this.logger);
		}

		return this.prosopoApi;
	}
}

export { GeneralSettingsWebComponent };
