import {z} from "zod";
import {captchaUsageSchema} from "./captchaUsageSchema";

type CaptchaUsage = z.infer<typeof captchaUsageSchema>;

export type {CaptchaUsage};