import { z, type ZodType } from "zod";
import type { Site } from "./site.js";
import { siteSettingsSchema } from "./settings/siteSettingsSchema.js";
import { captchaUsageSchema } from "../captchaUsage/captchaUsageSchema.js";

const siteSchema = z.object({
	name: z.string(),
	settings: siteSettingsSchema,
	monthlyUsage: z.object({
		limit: z.number(),
		image: captchaUsageSchema,
		pow: captchaUsageSchema,
	}),
}) satisfies ZodType<Site>;

export { siteSchema };
