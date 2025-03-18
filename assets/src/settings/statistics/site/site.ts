import type { SiteSettings } from "./settings/siteSettings.js";
import type { CaptchaUsage } from "../captchaUsage/captchaUsage.js";
import type { Account } from "../../account/account.js";

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
