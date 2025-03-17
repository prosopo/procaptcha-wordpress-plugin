import { z, ZodType } from "zod";
import type { Account } from "./account.js";

const accountSchema = z.object({
	tier: z.string(),
	tierRequestQuota: z.number(),
}) satisfies ZodType<Account>;

export { accountSchema };
