import type { SiteApiCredentials } from "#settings/api/siteApiCredentials.js";
import { z, ZodType } from "zod";

export interface ProcaptchaAccount {
	tier: string;
	tierRequestQuota: number;
}

export const procaptchaAccountSchema = z.object({
	tier: z.string(),
	tierRequestQuota: z.number(),
}) satisfies ZodType<ProcaptchaAccount>;

export interface ProcaptchaAccountResolver {
	resolveAccount(
		siteCredentials: SiteApiCredentials,
	): Promise<ProcaptchaAccount | null>;
}

export enum ProcaptchaAccountTiers {
	FREE = "free",
}
