import { z, ZodType } from "zod";

export interface ProcaptchaAccount {
	tier: string;
	tierRequestQuota: number;
}

export const procaptchaAccountSchema = z.object({
	tier: z.string(),
	tierRequestQuota: z.number(),
}) satisfies ZodType<ProcaptchaAccount>;

export enum ProcaptchaAccountTiers {
	FREE = "free",
}
