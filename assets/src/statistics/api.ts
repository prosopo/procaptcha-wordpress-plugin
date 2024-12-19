import { stringToU8a, u8aToHex } from "@polkadot/util";
import { getPairAsync } from "@prosopo/contract";
import { Config } from "./config";
import Collection from "./collection";
import LoggerInterface from "../interfaces/logger";

interface UserData {
	email: string;
	name: string;
	tier: string;
	monthlyUsage: {
		limits: {
			verifications: number;
		};
		image: {
			submissions: number;
			verifications: number;
			total: number;
		};
		pow: {
			submissions: number;
			verifications: number;
			total: number;
		};
	};
}

interface UserSettings {
	captchaType: string;
	frictionlessThreshold: number;
	powDifficulty: number;
	domains: Array<string>;
}

interface TrafficDataItem {
	hour: number;
	day: number;
	month: number;
	year: number;
	powCount: number;
	imageCount: number;
}

class Api {
	private logger: LoggerInterface;
	private config: Config;
	private loginToken: string;

	constructor(config: Config, logger: LoggerInterface) {
		this.logger = logger;
		this.config = config;

		this.loginToken = "";
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

	protected async request(url: string, args: object): Promise<unknown> {
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

	protected async getLoginToken(): Promise<string> {
		if ("" !== this.loginToken) {
			return this.loginToken;
		}

		const timestamp = Date.now();
		const signature = await this.sign(
			this.config.getSecretKey(),
			timestamp.toString(),
		);

		const data = await this.request("https://api.prosopo.io/requestlogin", {
			auth: {
				siteKey: this.config.getSiteKey(),
				signature: signature,
				timestamp: timestamp,
			},
		});

		this.loginToken =
			true === data.hasOwnProperty("token") ? data["token"] : "";

		return this.loginToken;
	}

	public async getUserData(): Promise<UserData> {
		const loginToken = await this.getLoginToken();

		const rawResponse = await this.request(
			"https://api.prosopo.io/getuserdata",
			{
				token: loginToken,
			},
		);
		const userDataResponse =
			"object" === typeof rawResponse ? rawResponse : {};
		const userData = new Collection(userDataResponse);

		const monthlyCaptchaRequests = userData.getSubCollection(
			"monthlyCaptchaRequests",
		);

		const limits = monthlyCaptchaRequests.getSubCollection("limits");
		const image = monthlyCaptchaRequests.getSubCollection("image");
		const pow = monthlyCaptchaRequests.getSubCollection("pow");

		return {
			email: userData.getString("email"),
			name: userData.getString("name"),
			tier: userData.getString("tier"),
			monthlyUsage: {
				limits: {
					verifications: limits.getNumber("verifications"),
				},
				image: {
					submissions: image.getNumber("submissions"),
					verifications: image.getNumber("verifications"),
					total: image.getNumber("total"),
				},
				pow: {
					submissions: pow.getNumber("submissions"),
					verifications: pow.getNumber("verifications"),
					total: pow.getNumber("total"),
				},
			},
		};
	}

	public async getUserSettings(): Promise<UserSettings> {
		const loginToken = await this.getLoginToken();

		const rawResponse = await this.request(
			"https://api.prosopo.io/getusersettings",
			{
				token: loginToken,
			},
		);
		const userSettingsResponse =
			"object" === typeof rawResponse ? rawResponse : {};

		const userSettings = new Collection(userSettingsResponse);

		const domainsList = userSettings.getArray("domains");

		const domains: string[] = [];

		domainsList.forEach((domain: unknown) => {
			if ("string" !== typeof domain || "*" === domain) {
				return;
			}

			domains.push(domain);
		});

		return {
			captchaType: userSettings.getString("captchaType"),
			frictionlessThreshold: userSettings.getNumber(
				"frictionlessThreshold",
			),
			powDifficulty: userSettings.getNumber("powDifficulty"),
			domains: domains,
		};
	}

	public async getTrafficData(): Promise<TrafficDataItem[]> {
		const loginToken = await this.getLoginToken();

		const trafficDataResponse = await this.request(
			"https://api.prosopo.io/gettrafficdata",
			{
				token: loginToken,
			},
		);
		const trafficDataItems =
			true === Array.isArray(trafficDataResponse)
				? trafficDataResponse
				: [];

		const trafficData: TrafficDataItem[] = [];

		trafficDataItems.forEach((item: object) => {
			const itemData = new Collection(item);
			const id = itemData.getSubCollection("_id");
			const pow = itemData.getSubCollection("pow");
			const image = itemData.getSubCollection("image");

			trafficData.push({
				hour: id.getNumber("h"),
				day: id.getNumber("d"),
				month: id.getNumber("m"),
				year: id.getNumber("y"),
				powCount: pow.getNumber("serverChecked"),
				imageCount: image.getNumber("serverChecked"),
			});
		});

		return trafficData;
	}
}

export { UserData, UserSettings, TrafficDataItem, Api };
