import type Logger from "../../utils/logger/logger.js";
import type { SiteApiCredentials } from "#settings/api/siteApiCredentials.js";
import type {
	ProcaptchaAccount,
	ProcaptchaAccountResolver,
} from "#settings/api/procaptchaAccount.js";
import type {
	ProcaptchaSite,
	ProcaptchaSiteResolver,
} from "#settings/api/procaptchaSite.js";

export class ApiClient
	implements ProcaptchaAccountResolver, ProcaptchaSiteResolver
{
	public constructor(
		protected readonly accountEndpointUrl: string,
		protected readonly logger: Logger,
	) {}

	public async resolveAccount(
		siteCredentials: SiteApiCredentials,
	): Promise<ProcaptchaAccount | null> {
		try {
			return this.fetchAccount(siteCredentials);
		} catch (error) {
			this.logger.warning("Account can not be resolved", {
				error: error,
				siteCredentials,
			});

			return null;
		}
	}

	public async resolveSite(
		siteCredentials: SiteApiCredentials,
	): Promise<ProcaptchaSite | null> {
		try {
			return this.fetchSite(siteCredentials);
		} catch (error) {
			this.logger.warning("Account can not be resolved", {
				error: error,
				siteCredentials,
			});

			return null;
		}
	}

	protected async fetchAccount(
		siteCredentials: SiteApiCredentials,
	): Promise<ProcaptchaAccount> {
		if (siteCredentials.canSign()) {
			const accountData = await this.callAccountEndpoint(siteCredentials);

			const { procaptchaAccountSchema } = await import(
				"./procaptchaAccount.js"
			);

			return procaptchaAccountSchema.parse(accountData);
		}

		throw new Error("Provided site credentials cannot sign");
	}

	protected async fetchSite(
		siteCredentials: SiteApiCredentials,
	): Promise<ProcaptchaSite> {
		if (siteCredentials.canSign()) {
			const accountData = await this.callAccountEndpoint(siteCredentials);

			const siteData =
				Object === accountData?.constructor
					? {
							...accountData,
							account: accountData,
						}
					: {};

			const { procaptchaSiteSchema } = await import(
				"./procaptchaSite.js"
			);

			return procaptchaSiteSchema.parse(siteData);
		}

		throw new Error("Provided site credentials cannot sign messages");
	}

	protected async callAccountEndpoint(
		siteCredentials: SiteApiCredentials,
	): Promise<unknown> {
		return await this.callApiEndpoint(
			this.accountEndpointUrl,
			siteCredentials,
			{
				siteKey: siteCredentials.publicKey,
			},
		);
	}

	protected async callApiEndpoint(
		endpointUrl: string,
		siteCredentials: SiteApiCredentials,
		bodyFields: object,
	): Promise<unknown> {
		const timestamp = Date.now();

		const messageSignature = await siteCredentials.signMessage(
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
