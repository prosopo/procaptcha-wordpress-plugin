export type ApiCredentials = {
	canSign(): boolean;

	getJwt(): Promise<string>;
};

export class SiteApiCredentials implements ApiCredentials {
	constructor(
		public readonly publicKey: string,
		private readonly privateKey: string,
	) {}

	canSign(): boolean {
		return this.publicKey.length > 0 && this.privateKey.length > 0;
	}

	async getJwt(): Promise<string> {
		if (this.canSign()) {
			return this.createJwt();
		}

		throw Error("JWT issuing requires complete credentials");
	}

	toString() {
		return [
			`public key: ${this.publicKey}`,
			this.canSign() ? "can sign" : "cannot sign",
		].join(", ");
	}

	protected async createJwt(): Promise<string> {
		const getPair = (await import("@prosopo/keyring")).getPair;

		const keypair = getPair(this.privateKey);

		// fixme change the keyring package version when it's released
		// @ts-expect-error tmp
		return keypair.jwtIssue();
	}
}
