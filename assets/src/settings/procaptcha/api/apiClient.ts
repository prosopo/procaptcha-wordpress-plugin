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
	constructor(
		protected readonly siteEndpointUrl: string,
		protected readonly logger: Logger,
	) {}

	async resolveSite(
		apiCredentials: ApiCredentials,
	): Promise<ProcaptchaSite | null> {
		const { procaptchaSiteSchema } = await import("../procaptchaSite.js");

		try {
			const siteData = await this.makeApiRequest(
				this.siteEndpointUrl,
				apiCredentials,
				{
					siteKey: apiCredentials.publicKey,
				},
			);

			return procaptchaSiteSchema.parse(siteData);
		} catch (error) {
			this.logger.warning("Site cannot be resolved", {
				error,
				apiCredentials: String(apiCredentials),
			});

			return null;
		}
	}

	resolveAccount = async (
		apiCredentials: ApiCredentials,
	): Promise<ProcaptchaAccount | null> =>
		(await this.resolveSite(apiCredentials))?.account ?? null;

	protected async makeApiRequest(
		endpointUrl: string,
		apiCredentials: ApiCredentials,
		fields: Record<string, unknown>,
	): Promise<unknown> {
		const jwt = await apiCredentials.getJwt();

		return this.requestEndpoint(
			endpointUrl,
			{
				Authorization: `Bearer ${jwt}`,
			},
			fields,
		);
	}

	protected async requestEndpoint(
		url: string,
		headers: Record<string, string>,
		fields: Record<string, unknown>,
	): Promise<unknown> {
		const response = await this.requestUrl(url, {
			method: "POST",
			headers: {
				...headers,
				"Content-Type": "application/json",
			},
			body: JSON.stringify(fields),
		});

		if (200 === response.status) {
			const jsonResponse = await response.json();

			this.logger.debug("Endpoint request completed", {
				jsonResponse,
			});

			return jsonResponse;
		}

		this.logger.warning("Endpoint request failed", {
			url,
			headers,
			fields,
			statusCode: response.status,
		});

		throw Error("Endpoint request failed");
	}

	protected async requestUrl(
		url: string,
		request: RequestInit,
	): Promise<Response> {
		this.logger.debug("Requesting url", {
			url,
			request,
		});

		try {
			return await fetch(url, request);
		} catch (networkError) {
			this.logger.warning("Network error", {
				url,
				request,
				networkError,
			});

			throw networkError;
		}
	}
}
