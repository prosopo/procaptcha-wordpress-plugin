import {
	type ProcaptchaAccount,
	procaptchaAccountSchema,
} from "#settings/procaptcha/procaptchaAccount.js";
import { z, type ZodType } from "zod";

/**
 * Only the parts of the site response the statistics page actually renders:
 * the monthly captcha counts, the account tier and the site's challenge type.
 *
 * Zod strips keys a schema does not declare, and the whole response is a
 * single parse, so everything beyond the original image and PoW counts is
 * optional and falls back to undefined rather than failing it: older API
 * deployments do not send the newer counts, and `settings` has changed shape
 * before (`frictionlessThreshold`).
 */
export interface ProcaptchaSite {
	account: ProcaptchaAccount;
	settings?: {
		captchaType?: string;
	};
	monthlyUsage: {
		limit: number;
		image: CaptchaUsage;
		pow: CaptchaUsage;
		puzzle?: CaptchaUsage;
		iconOrder?: CaptchaUsage;
		audio?: CaptchaUsage;
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

const optionalUsageSchema = captchaUsageSchema.optional().catch(undefined);

export const procaptchaSiteSchema = z.object({
	account: procaptchaAccountSchema,
	settings: z
		.object({ captchaType: z.string().optional() })
		.optional()
		.catch(undefined),
	monthlyUsage: z.object({
		limit: z.number(),
		image: captchaUsageSchema,
		pow: captchaUsageSchema,
		puzzle: optionalUsageSchema,
		iconOrder: optionalUsageSchema,
		audio: optionalUsageSchema,
	}),
}) satisfies ZodType<ProcaptchaSite, z.ZodTypeDef, unknown>;
