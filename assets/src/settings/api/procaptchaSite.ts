import {
	type ProcaptchaAccount,
	procaptchaAccountSchema,
} from "#settings/api/procaptchaAccount.js";
import type { SiteApiCredentials } from "#settings/api/siteApiCredentials.js";
import { z, type ZodType } from "zod";

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

export interface ProcaptchaSiteResolver {
	resolveSite(
		siteCredentials: SiteApiCredentials,
	): Promise<ProcaptchaSite | null>;
}

export const siteSettingsSchema = z.object({
	frictionlessThreshold: z.number(),
	powDifficulty: z.number(),
	captchaType: z.string(),
	domains: z.string().array(),
}) satisfies ZodType<SiteSettings>;

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
}) satisfies ZodType<ProcaptchaSite>;
