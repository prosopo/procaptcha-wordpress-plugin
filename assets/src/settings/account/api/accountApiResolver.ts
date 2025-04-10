import type { Account } from "#settings/account/account.js";
import type { ApiCredentials } from "#settings/apiCredentials.js";

interface AccountApiResolver {
	resolveAccount(apiCredentials: ApiCredentials): Promise<Account | null>;
}

export type { AccountApiResolver };
