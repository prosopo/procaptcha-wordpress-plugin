import {z} from "zod";
import type {accountSchema} from "./accountSchema";

type Account = z.infer<typeof accountSchema>;

export {Account};
