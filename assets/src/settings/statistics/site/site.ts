import type { SiteSettings } from "./settings/siteSettings.js";
import type { Account } from "#settings/account/account.js";
import type { CaptchaUsage } from "#settings/statistics/captchaUsage/captchaUsage.js";

interface Site {
	account: Account;
	name: string;
	settings: SiteSettings;
	monthlyUsage: {
		limit: number;
		image: CaptchaUsage;
		pow: CaptchaUsage;
	};
}

export type { Site };
