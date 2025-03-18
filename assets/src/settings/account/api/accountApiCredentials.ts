import type { ApiCredentials } from "../../apiCredentials.js";

class AccountApiCredentials implements ApiCredentials {
	private readonly publicSiteKey: string;
	private readonly privateSiteKey: string;

	constructor(publicSiteKey: string, privateSiteKey: string) {
		this.publicSiteKey = publicSiteKey;
		this.privateSiteKey = privateSiteKey;
	}

	public get publicKey(): string {
		return this.publicSiteKey;
	}

	public toString(): string {
		return {
			publicSiteKey: this.publicSiteKey,
		}.toString();
	}

	public canSignMessage(): boolean {
		return this.publicSiteKey.length > 0 && this.privateSiteKey.length > 0;
	}

	public async signMessage(message: string): Promise<string> {
		const getPairAsync = (await import("@prosopo/keyring")).getPairAsync;
		const { stringToU8a, u8aToHex } = await import("@polkadot/util");

		const keypair = await getPairAsync(
			this.privateSiteKey,
			undefined,
			"sr25519",
			42,
		);

		const sign = keypair.sign(stringToU8a(message));

		return u8aToHex(sign);
	}
}

export { AccountApiCredentials };
