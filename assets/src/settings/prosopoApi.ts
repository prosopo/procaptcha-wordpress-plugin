import { getPairAsync } from "@prosopo/keyring";
import { stringToU8a, u8aToHex } from "@polkadot/util";
import type Logger from "../logger/logger.js";

class ProsopoApi {
	public constructor(private readonly logger: Logger) {}

	public async requestEndpoint(
		endpointUrl: string,
		sitePrivateKey: string,
		bodyFields: object,
	): Promise<unknown> {
		const timestamp = Date.now();

		const messageSignature = await this.signMessage(
			sitePrivateKey,
			timestamp.toString(),
		);

		bodyFields = {
			...bodyFields,
			signature: messageSignature,
			timestamp: timestamp,
		};

		return await this.fetchEndpointResponse(endpointUrl, bodyFields);
	}

	protected async signMessage(
		secretKey: string,
		message: string,
	): Promise<string> {
		const keypair = await getPairAsync(secretKey, undefined, "sr25519", 42);

		const sign = keypair.sign(stringToU8a(message));

		const hexSign = u8aToHex(sign);

		this.logger.debug("Created sign", {
			message: message,
			sign: sign,
		});

		return hexSign;
	}

	protected async fetchEndpointResponse(
		endpointUrl: string,
		bodyFields: object,
	): Promise<unknown> {
		this.logger.debug("Requesting endpoint", {
			url: endpointUrl,
			bodyFields: bodyFields,
		});

		let endpointResponse: Response;

		try {
			endpointResponse = await fetch(endpointUrl, {
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

		if (200 === endpointResponse.status) {
			const jsonEndpointResponse = await endpointResponse.json();

			this.logger.debug("Endpoint request is successfully completed", {
				endpointUrl: endpointUrl,
				jsonEndpointResponse: jsonEndpointResponse,
			});

			return jsonEndpointResponse;
		} else {
			this.logger.warning("Endpoint request is failed", {
				statusCode: endpointResponse.status,
				url: endpointUrl,
				bodyFields: bodyFields,
			});

			throw Error("Endpoint request is failed");
		}
	}
}

export { ProsopoApi };
