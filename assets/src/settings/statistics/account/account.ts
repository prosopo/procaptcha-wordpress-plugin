import { z } from "zod";
import type { accountSchema } from "./accountSchema.js";

type Account = z.infer<typeof accountSchema>;

export { Account };
