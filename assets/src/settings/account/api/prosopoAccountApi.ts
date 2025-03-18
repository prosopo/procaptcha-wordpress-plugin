import type Logger from "../../../logger/logger.js";
import type { AccountApiResolver } from "./accountApiResolver.js";
import type { Account } from "../account.js";
import type { ApiCredentials } from "../../apiCredentials.js";

class ProsopoAccountApi implements AccountApiResolver {
	public constructor(
		protected readonly accountEndpointUrl: string,
		protected readonly logger: Logger,
	) {}

	public async resolveAccount(
		credentials: ApiCredentials,
	): Promise<Account | null> {
		try {
			return this.getAccount(credentials);
		} catch (error) {
			this.logger.warning("Account can not be resolved", {
				error: error,
				credentials: credentials,
			});

			return null;
		}
	}

	protected async getAccount(credentials: ApiCredentials): Promise<Account> {
		if (credentials.canSignMessage()) {
			const accountEndpointResponse =
				await this.requestAccountEndpoint(credentials);

			return await this.parseAccountEndpointResponse(
				accountEndpointResponse,
			);
		}

		throw new Error("Provided site credentials can not sign messages");
	}

	protected async requestAccountEndpoint(
		credentials: ApiCredentials,
	): Promise<unknown> {
		return await this.requestProsopoEndpoint(
			this.accountEndpointUrl,
			credentials,
			{
				siteKey: credentials.publicKey,
			},
		);
	}

	protected async requestProsopoEndpoint(
		endpointUrl: string,
		credentials: ApiCredentials,
		bodyFields: object,
	): Promise<unknown> {
		const timestamp = Date.now();

		const messageSignature = await credentials.signMessage(
			timestamp.toString(),
		);

		bodyFields = {
			...bodyFields,
			signature: messageSignature,
			timestamp: timestamp,
		};

		return await this.requestJsonEndpoint(endpointUrl, bodyFields);
	}

	protected async requestJsonEndpoint(
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

	protected async parseAccountEndpointResponse(
		accountEndpointResponse: unknown,
	): Promise<Account> {
		const { accountSchema } = await import("../accountSchema.js");

		return accountSchema.parse(accountEndpointResponse);
	}
}

export { ProsopoAccountApi };
