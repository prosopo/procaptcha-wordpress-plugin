import { z, type ZodType } from "zod";
import type { Site } from "./site.js";
import { siteSettingsSchema } from "./settings/siteSettingsSchema.js";
import { captchaUsageSchema } from "../captchaUsage/captchaUsageSchema.js";
import { accountSchema } from "../../account/accountSchema.js";

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
