import {
	type ProcaptchaAccount,
	procaptchaAccountSchema,
} from "#settings/procaptcha/procaptchaAccount.js";
import { z, type ZodType } from "zod";

/**
 * Only the parts of the site response the statistics page actually renders:
 * the monthly captcha counts and the account tier the traffic chart gates on.
 *
 * `settings` is deliberately absent. Zod strips keys a schema does not
 * declare, so whatever shape the portal sends for the captcha settings — and
 * `frictionlessThreshold` has already changed shape once — is ignored rather
 * than validated. The whole response is a single parse, so a field the page
 * does not display has no business being able to fail it.
 */
export interface ProcaptchaSite {
	account: ProcaptchaAccount;
	monthlyUsage: {
		limit: number;
		image: CaptchaUsage;
		pow: CaptchaUsage;
	};
}

export interface CaptchaUsage {
	submissions: number;
	verifications: number;
	total: number;
}

export const captchaUsageSchema = z.object({
	submissions: z.number(),
	verifications: z.number(),
	total: z.number(),
}) satisfies ZodType<CaptchaUsage>;

export const procaptchaSiteSchema = z.object({
	account: procaptchaAccountSchema,
	monthlyUsage: z.object({
		limit: z.number(),
		image: captchaUsageSchema,
		pow: captchaUsageSchema,
	}),
}) satisfies ZodType<ProcaptchaSite>;
