import { z } from "zod";
import { captchaUsageSchema } from "./captchaUsageSchema.js";

type CaptchaUsage = z.infer<typeof captchaUsageSchema>;

export type { CaptchaUsage };
