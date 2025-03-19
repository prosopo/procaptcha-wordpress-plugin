import { z, type ZodType } from "zod";
import type { Site } from "./site.js";
import { accountSchema } from "#settings/account/accountSchema.js";
import { siteSettingsSchema } from "#settings/statistics/site/settings/siteSettingsSchema.js";
import { captchaUsageSchema } from "#settings/statistics/captchaUsage/captchaUsageSchema.js";

const siteSchema = z.object({
	account: accountSchema,
	name: z.string(),
	settings: siteSettingsSchema,
	monthlyUsage: z.object({
		limit: z.number(),
		image: captchaUsageSchema,
		pow: captchaUsageSchema,
	}),
}) satisfies ZodType<Site>;

export { siteSchema };
