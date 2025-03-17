import { z, ZodType } from "zod";
import type { SiteSettings } from "./siteSettings.js";

const siteSettingsSchema = z.object({
	frictionlessThreshold: z.number(),
	powDifficulty: z.number(),
	captchaType: z.string(),
	domains: z.string().array(),
}) satisfies ZodType<SiteSettings>;

export { siteSettingsSchema };
