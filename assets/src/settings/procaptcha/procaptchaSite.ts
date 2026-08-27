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
	 * rather than a puzzle. Arrives nested inside `frictionlessThreshold`, not
	 * as a sibling of it, and is absent whenever that field is still the bare
	 * number the pre-ladder API sends.
	 *
	 * Not to be confused with the settings' own `imageThreshold`, which is an
	 * unrelated image-captcha setting on a 0..1 scale.
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
 * `SiteSettings` as it arrives on the wire, before the ladder is split into
 * the two flat fields the rest of the plugin reads. Declared separately
 * because the schema below transforms on parse, so its input and output
 * types differ and `ZodType` needs both.
 */
export interface SiteSettingsInput
	extends Omit<
		SiteSettings,
		"frictionlessThreshold" | "frictionlessImageThreshold"
	> {
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
 * of the portal, so an install can be talking to either shape. A bare number
 * means what it always meant — the puzzle rung — and carries no image rung.
 */
const frictionlessThresholdSchema = z.union([
	z.number(),
	z.object({
		frictionlessPuzzleThreshold: z.number().optional(),
		frictionlessImageThreshold: z.number().optional(),
	}),
]);

/**
 * Split the ladder into the two flat fields the rest of the plugin reads, so
 * nothing downstream has to know which of the two wire shapes arrived.
 */
export const siteSettingsSchema = z
	.object({
		frictionlessThreshold: frictionlessThresholdSchema,
		powDifficulty: z.number(),
		captchaType: z.string(),
		domains: z.string().array(),
	})
	.transform(({ frictionlessThreshold, ...settings }) => ({
		...settings,
		frictionlessThreshold:
			"number" === typeof frictionlessThreshold
				? frictionlessThreshold
				: (frictionlessThreshold.frictionlessPuzzleThreshold ??
					DEFAULT_FRICTIONLESS_THRESHOLD),
		frictionlessImageThreshold:
			"number" === typeof frictionlessThreshold
				? undefined
				: frictionlessThreshold.frictionlessImageThreshold,
	})) satisfies ZodType<SiteSettings, ZodTypeDef, SiteSettingsInput>;

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
