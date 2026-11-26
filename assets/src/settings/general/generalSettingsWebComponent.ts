import type { WebComponent } from "#utils/webComponent/webComponent.js";
import type { ApiCredentials } from "#settings/procaptcha/api/apiCredentials.js";

import { ProcaptchaAccountTiers } from "#settings/procaptcha/procaptchaAccount.js";
import type { ProcaptchaAccountResolver } from "#settings/procaptcha/api/apiClient.js";
import { connectColorFields } from "./colorField.js";

class GeneralSettingsWebComponent implements WebComponent {
	public constructor(
		private readonly apiCredentials: ApiCredentials,
		private readonly accountApiResolver: ProcaptchaAccountResolver,
	) {}

	constructComponent(element: HTMLElement): void {
		// The settings form sits beside this component, in the same root.
		const root = element.getRootNode();

		if (root instanceof ShadowRoot || root instanceof Document) {
			connectColorFields(root);
		}

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

		if (ProcaptchaAccountTiers.FREE === accountTier) {
			tierUpgradeBanner.setAttribute("data-visible", "true");
		}
	}
}

export { GeneralSettingsWebComponent };
