import type { WebComponent } from "#webComponent/webComponent.js";
import type { ApiCredentials } from "#settings/apiCredentials.js";
import type { AccountApiResolver } from "#settings/account/api/accountApiResolver.js";
import { AccountTiers } from "#settings/account/accountTiers.js";

class GeneralSettingsWebComponent implements WebComponent {
	public constructor(
		private readonly apiCredentials: ApiCredentials,
		private readonly accountApiResolver: AccountApiResolver,
	) {}

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
		const account = await this.accountApiResolver.resolveAccount(
			this.apiCredentials,
		);
		const accountTier = account?.tier || "";

		if (AccountTiers.FREE === accountTier) {
			tierUpgradeBanner.setAttribute("data-visible", "true");
		}
	}
}

export { GeneralSettingsWebComponent };
