interface ApiCredentials {
	publicKey: string;

	canSignMessage(): boolean;

	signMessage(message: string): Promise<string>;
}

export type { ApiCredentials };
