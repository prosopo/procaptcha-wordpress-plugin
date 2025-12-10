export type ApiCredentials = {
	publicKey: string;

	canSign(): boolean;

	issueJwt(): Promise<string>;
};

export class SiteApiCredentials implements ApiCredentials {
	constructor(
		public readonly publicKey: string,
		private readonly privateKey: string,
	) {}

	public canSign(): boolean {
		return this.publicKey.length > 0 && this.privateKey.length > 0;
	}

	public async issueJwt(): Promise<string> {
		const getPair = (await import("@prosopo/keyring")).getPair;

		const keypair = getPair(this.privateKey, undefined, "sr25519", 42);

		return keypair.jwtIssue();
	}

	toString() {
		return [
			`public key: ${this.publicKey}`,
			this.canSign() ? "can sign" : "cannot sign",
		].join(", ");
	}
}
