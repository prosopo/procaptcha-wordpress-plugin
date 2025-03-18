import { ProsopoAccountApi } from "../../../account/api/prosopoAccountApi.js";
import type { SiteApiResolver } from "./siteApiResolver.js";
import type { ApiCredentials } from "../../../apiCredentials.js";
import type { Site } from "../site.js";

class ProsopoSiteApi extends ProsopoAccountApi implements SiteApiResolver {
	public async resolveSite(
		credentials: ApiCredentials,
	): Promise<Site | null> {
		try {
			return this.getSite(credentials);
		} catch (error) {
			this.logger.warning("Account can not be resolved", {
				error: error,
				credentials: credentials,
			});

			return null;
		}
	}

	protected async getSite(credentials: ApiCredentials): Promise<Site> {
		if (credentials.canSignMessage()) {
			const accountEndpointResponse =
				await this.requestAccountEndpoint(credentials);

			return await this.parseSiteEndpointResponse(
				accountEndpointResponse,
			);
		}

		throw new Error("Provided site credentials can not sign messages");
	}

	protected async parseSiteEndpointResponse(
		accountEndpointResponse: unknown,
	): Promise<Site> {
		const account = await this.parseAccountEndpointResponse(
			accountEndpointResponse,
		);

		const siteEndpointResponse =
			Object === accountEndpointResponse?.constructor
				? {
						...accountEndpointResponse,
						account: account,
					}
				: {};

		const { siteSchema } = await import("../siteSchema.js");

		return siteSchema.parse(siteEndpointResponse);
	}
}

export { ProsopoSiteApi };
