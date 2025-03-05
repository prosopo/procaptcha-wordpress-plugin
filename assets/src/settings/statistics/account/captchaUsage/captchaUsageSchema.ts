import {z} from "zod";

const captchaUsageSchema = z.object({
    submissions: z.number(),
    verifications: z.number(),
    total: z.number(),
});

export {captchaUsageSchema};
