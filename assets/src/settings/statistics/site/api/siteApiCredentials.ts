import type { ApiCredentials } from "#settings/apiCredentials.js";

class SiteApiCredentials implements ApiCredentials {
	public constructor(
		private readonly accountApiCredentials: ApiCredentials,
	) {}

	public get publicKey(): string {
		return this.accountApiCredentials.publicKey;
	}

	public canSignMessage(): boolean {
		return this.accountApiCredentials.canSignMessage();
	}

	public async signMessage(message: string): Promise<string> {
		return await this.accountApiCredentials.signMessage(message);
	}
}

export { SiteApiCredentials };
