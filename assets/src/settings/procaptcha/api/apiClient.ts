import type Logger from "#utils/logger/logger.js";
import type { ApiCredentials } from "#settings/procaptcha/api/apiCredentials.js";
import type { ProcaptchaAccount } from "#settings/procaptcha/procaptchaAccount.js";
import type { ProcaptchaSite } from "#settings/procaptcha/procaptchaSite.js";

export type ProcaptchaAccountResolver = {
	resolveAccount(
		apiCredentials: ApiCredentials,
	): Promise<ProcaptchaAccount | null>;
};

export type ProcaptchaSiteResolver = {
	resolveSite(apiCredentials: ApiCredentials): Promise<ProcaptchaSite | null>;
};

export class ApiClient
	implements ProcaptchaAccountResolver, ProcaptchaSiteResolver
{
	public constructor(
		protected readonly accountEndpointUrl: string,
		protected readonly logger: Logger,
	) {}

	public async resolveAccount(
		apiCredentials: ApiCredentials,
	): Promise<ProcaptchaAccount | null> {
		try {
			return this.fetchAccount(apiCredentials);
		} catch (error) {
			this.logger.warning("Account can not be resolved", {
				error: error,
				apiCredentials,
			});

			return null;
		}
	}

	public async resolveSite(
		apiCredentials: ApiCredentials,
	): Promise<ProcaptchaSite | null> {
		try {
			return this.fetchSite(apiCredentials);
		} catch (error) {
			this.logger.warning("Account can not be resolved", {
				error: error,
				apiCredentials,
			});

			return null;
		}
	}

	protected async fetchAccount(
		apiCredentials: ApiCredentials,
	): Promise<ProcaptchaAccount> {
		if (apiCredentials.canSign()) {
			const accountData = await this.callAccountEndpoint(apiCredentials);

			const { procaptchaAccountSchema } = await import(
				"../procaptchaAccount.js"
			);

			return procaptchaAccountSchema.parse(accountData);
		}

		throw new Error("Provided site credentials cannot sign");
	}

	protected async fetchSite(
		apiCredentials: ApiCredentials,
	): Promise<ProcaptchaSite> {
		if (apiCredentials.canSign()) {
			const accountData = await this.callAccountEndpoint(apiCredentials);

			const siteData =
				Object === accountData?.constructor
					? {
							...accountData,
							account: accountData,
						}
					: {};

			const { procaptchaSiteSchema } = await import(
				"../procaptchaSite.js"
			);

			return procaptchaSiteSchema.parse(siteData);
		}

		throw new Error("Provided site credentials cannot sign messages");
	}

	protected async callAccountEndpoint(
		apiCredentials: ApiCredentials,
	): Promise<unknown> {
		return await this.callApiEndpoint(
			this.accountEndpointUrl,
			apiCredentials,
			{
				siteKey: apiCredentials.publicKey,
			},
		);
	}

	protected async callApiEndpoint(
		endpointUrl: string,
		apiCredentials: ApiCredentials,
		bodyFields: object,
	): Promise<unknown> {
		const timestamp = Date.now();

		const messageSignature = await apiCredentials.signMessage(
			timestamp.toString(),
		);

		bodyFields = {
			...bodyFields,
			signature: messageSignature,
			timestamp: timestamp,
		};

		return await this.callEndpoint(endpointUrl, bodyFields);
	}

	protected async callEndpoint(
		endpointUrl: string,
		bodyFields: object,
	): Promise<unknown> {
		this.logger.debug("Requesting endpoint", {
			endpointUrl: endpointUrl,
			bodyFields: bodyFields,
		});

		let response: Response;

		try {
			response = await fetch(endpointUrl, {
				method: "POST",
				headers: {
					"Content-Type": "application/json",
				},
				body: JSON.stringify(bodyFields),
			});
		} catch (networkError) {
			this.logger.warning("Network error", {
				networkError: networkError,
				endpointUrl: endpointUrl,
				bodyFields: bodyFields,
			});

			throw networkError;
		}

		if (200 === response.status) {
			const jsonResponse = await response.json();

			this.logger.debug("Endpoint request is successfully completed", {
				endpointUrl: endpointUrl,
				jsonResponse: jsonResponse,
			});

			return jsonResponse;
		} else {
			this.logger.warning("Endpoint request has failed", {
				statusCode: response.status,
				endpointUrl: endpointUrl,
				bodyFields: bodyFields,
			});

			throw Error("Endpoint request has failed");
		}
	}
}
