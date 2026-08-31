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
 * `SiteSettings` as it arrives on the wire, before the ladder is collapsed to
 * the single number the rest of the plugin reads. Declared separately because
 * the schema below transforms on parse, so its input and output types differ
 * and `ZodType` needs both.
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
 * `frictionlessThreshold` is read as "number, or the two-rung ladder object"
 * because both are live at once: the portal moved the field to the ladder,
 * but records migrate in the background and the plugin ships independently
 * of the portal, so an install can be talking to either shape.
 *
 * The plugin only displays the puzzle rung, so the ladder collapses to it and
 * the image rung is ignored. The union still has to be here: without it a
 * ladder object fails `z.number()`, and because the whole site response is one
 * parse, that failure takes out the entire statistics tab rather than one row.
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
