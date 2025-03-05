import {z} from "zod";
import {captchaUsageSchema} from "./captchaUsage/captchaUsageSchema";

const accountSchema = z.object({
    name: z.string(),
    tier: z.string(),
    tierRequestQuota: z.number(),
    settings: z.object({
        frictionlessThreshold: z.number(),
        powDifficulty: z.number(),
        captchaType: z.string(),
        domains: z.string().array(),
    }),
    monthlyUsage: z.object({
        limit: z.number(),
        image: captchaUsageSchema,
        pow: captchaUsageSchema,
    }),
});

export {accountSchema};
