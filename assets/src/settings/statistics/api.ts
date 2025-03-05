import {stringToU8a, u8aToHex} from "@polkadot/util";
import {getPairAsync} from "@prosopo/keyring";
import {Config} from "./config";
import Collection from "./collection";
import LoggerInterface from "../../interfaces/loggerInterface";

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

interface WpDetails {
    statusCode: number;
    body: {
        name: string,
    }
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

        // fixme
        console.log("getLoginToken is called3");

        /* if ("" !== this.loginToken) {
             return this.loginToken;
         }*/

        // fixme
        console.log("making a sign");

        const timestamp = Date.now();
        const signature = await this.sign(
            this.config.getSecretKey(),
            timestamp.toString(),
        );

        // fixme
        console.log("making wp-details request");

        const data = await this.request("https://api.prosopo.io/sites/wp-details", {
            siteKey: this.config.getSiteKey(),
            signature: signature,
            timestamp: timestamp,
        });

        // fixme
        console.log("wp-details response", data);

        return this.loginToken;
    }

    public async getUserData(): Promise<UserData> {
        const loginToken = await this.getLoginToken();

        const rawResponse = await this.request(
            "https://api.prosopo.io/getuserdata2",// fixme
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
            "https://api.prosopo.io/getusersettings2",// fixme
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
}

export {UserData, UserSettings, Api};
