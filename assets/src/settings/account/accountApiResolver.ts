import type { Account } from "./account.js";
import type { ApiCredentials } from "../apiCredentials.js";

interface AccountApiResolver {
	resolveAccount(apiCredentials: ApiCredentials): Promise<Account | null>;
}

export type { AccountApiResolver };
