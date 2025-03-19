import type { Site } from "#settings/statistics/site/site.js";
import type { ApiCredentials } from "#settings/apiCredentials.js";

interface SiteApiResolver {
	resolveSite(credentials: ApiCredentials): Promise<Site | null>;
}

export type { SiteApiResolver };
