import { stringToU8a, u8aToHex } from "@polkadot/util";
import { getPairAsync } from "@prosopo/keyring";
import { Config } from "./config";
import LoggerInterface from "../../interfaces/loggerInterface";
import type { Account } from "./account/account";
import { accountSchema } from "./account/accountSchema";

class Api {
	private logger: LoggerInterface;
	private config: Config;

	constructor(config: Config, logger: LoggerInterface) {
		this.logger = logger;
		this.config = config;
	}

	protected async sign(secretKey: string, message: string): Promise<string> {
		const keypair = await getPairAsync(secretKey, undefined, "sr25519", 42);

		const sign = keypair.sign(stringToU8a(message));

		const sing = u8aToHex(sign);

		this.logger.debug("Created sign", {
			message: message,
			sign: sign,
		});

		return sing;
	}

	protected async makeRequest(url: string, args: object): Promise<unknown> {
		let isNetworkError = true;

		this.logger.debug("Making request", {
			url: url,
			args: args,
		});

		try {
			const response = await fetch(url, {
				method: "POST",
				headers: {
					"Content-Type": "application/json",
				},
				body: JSON.stringify(args),
			});

			isNetworkError = false;

			if (200 !== response.status) {
				this.logger.warning("Request failed", {
					status: response.status,
					url: url,
					args: args,
				});

				throw Error("Request failed");
			}

			const json = await response.json();

			this.logger.debug("Request successfully complete", {
				url: url,
				json: json,
			});

			return json;
		} catch (error) {
			if (true === isNetworkError) {
				this.logger.warning("Network error", {
					error: error,
					url: url,
					args: args,
				});
			}

			throw error;
		}
	}

	public async getAccount(): Promise<Account> {
		const timestamp = Date.now();
		const signature = await this.sign(
			this.config.getSecretKey(),
			timestamp.toString(),
		);

		const apiResponse = await this.makeRequest(
			"https://api.prosopo.io/sites/wp-details",
			{
				siteKey: this.config.getSiteKey(),
				signature: signature,
				timestamp: timestamp,
			},
		);

		const account = accountSchema.parse(apiResponse);

		// hide '*' from the domains list.
		account.settings.domains = account.settings.domains.filter(
			(domain) => "*" !== domain,
		);

		return account;
	}
}

export { Api };
