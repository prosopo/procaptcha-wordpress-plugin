import {
	type ProcaptchaAccount,
	procaptchaAccountSchema,
} from "#settings/procaptcha/procaptchaAccount.js";
import { z, type ZodType, type ZodTypeDef } from "zod";

export interface ProcaptchaSite {
	account: ProcaptchaAccount;
	name: string;
	settings: SiteSettings;
	monthlyUsage: {
		limit: number;
		image: CaptchaUsage;
		pow: CaptchaUsage;
	};
}

export interface SiteSettings {
	/**
	 * Lower rung of the frictionless score ladder: the score a session has to
	 * stay under to pass without being challenged. This is the value the
	 * plugin has always shown as "Frictionless Threshold".
	 */
	frictionlessThreshold: number;
	/**
	 * Upper rung: the score at or above which a session gets an image captcha
	 * rather than a puzzle. Optional because portals older than the ladder
	 * release do not send it.
	 */
	frictionlessImageThreshold?: number;
	powDifficulty: number;
	captchaType: string;
	domains: string[];
}

export interface CaptchaUsage {
	submissions: number;
	verifications: number;
	total: number;
}

/**
 * `SiteSettings` as it arrives on the wire, before the ladder is collapsed
 * to its lower rung. Declared separately because the schema below transforms
 * on parse, so its input and output types differ and `ZodType` needs both.
 */
export interface SiteSettingsInput
	extends Omit<SiteSettings, "frictionlessThreshold"> {
	frictionlessThreshold:
		| number
		| {
				frictionlessPuzzleThreshold?: number;
				frictionlessImageThreshold?: number;
		  };
}

export interface ProcaptchaSiteInput extends Omit<ProcaptchaSite, "settings"> {
	settings: SiteSettingsInput;
}

/**
 * Fallback for a ladder object that arrives without its lower rung. Matches
 * the portal's own default so the label the user sees does not change
 * meaning between the two shapes.
 */
const DEFAULT_FRICTIONLESS_THRESHOLD = 0.5;

/**
 * The API sends `frictionlessThreshold` as a plain number, and will keep
 * doing so — the plugin ships independently of the portal, so that field's
 * type is part of a contract the portal cannot change from under an install
 * that updates on its own schedule.
 *
 * It is nonetheless read here as "number, or the two-rung object", because
 * internally the portal did move to the object and a future endpoint (or a
 * self-hosted one) may pass it straight through. Both collapse to the puzzle
 * rung, which is what this field has always meant, so the rest of the plugin
 * keeps seeing a number.
 */
const frictionlessThresholdSchema = z
	.union([
		z.number(),
		z.object({
			frictionlessPuzzleThreshold: z.number().optional(),
			frictionlessImageThreshold: z.number().optional(),
		}),
	])
	.transform((value) =>
		"number" === typeof value
			? value
			: (value.frictionlessPuzzleThreshold ??
				DEFAULT_FRICTIONLESS_THRESHOLD),
	);

export const siteSettingsSchema = z.object({
	frictionlessThreshold: frictionlessThresholdSchema,
	frictionlessImageThreshold: z.number().optional(),
	powDifficulty: z.number(),
	captchaType: z.string(),
	domains: z.string().array(),
}) satisfies ZodType<SiteSettings, ZodTypeDef, SiteSettingsInput>;

export const captchaUsageSchema = z.object({
	submissions: z.number(),
	verifications: z.number(),
	total: z.number(),
}) satisfies ZodType<CaptchaUsage>;

export const procaptchaSiteSchema = z.object({
	account: procaptchaAccountSchema,
	name: z.string(),
	settings: siteSettingsSchema,
	monthlyUsage: z.object({
		limit: z.number(),
		image: captchaUsageSchema,
		pow: captchaUsageSchema,
	}),
}) satisfies ZodType<ProcaptchaSite, ZodTypeDef, ProcaptchaSiteInput>;
