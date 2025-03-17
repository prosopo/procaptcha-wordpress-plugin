import type { SiteSettings } from "./settings/siteSettings.js";
import type { CaptchaUsage } from "../captchaUsage/captchaUsage.js";

interface Site {
	name: string;
	settings: SiteSettings;
	monthlyUsage: {
		limit: number;
		image: CaptchaUsage;
		pow: CaptchaUsage;
	};
}

export type { Site };
