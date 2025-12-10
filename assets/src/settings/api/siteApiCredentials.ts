export class SiteApiCredentials {
	constructor(
		public readonly publicKey: string,
		private readonly privateKey: string,
	) {}

	public canSign(): boolean {
		return this.publicKey.length > 0 && this.privateKey.length > 0;
	}

	public async signMessage(message: string): Promise<string> {
		const getPairAsync = (await import("@prosopo/keyring")).getPairAsync;
		const { stringToU8a, u8aToHex } = await import("@polkadot/util");

		const keypair = await getPairAsync(
			this.privateKey,
			undefined,
			"sr25519",
			42,
		);

		const sign = keypair.sign(stringToU8a(message));

		return u8aToHex(sign);
	}

	public toString(): string {
		return {
			publicSiteKey: this.publicKey,
		}.toString();
	}
}
