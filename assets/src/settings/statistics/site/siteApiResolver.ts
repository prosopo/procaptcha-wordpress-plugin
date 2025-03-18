import type { Site } from "./site.js";
import type { ApiCredentials } from "../../apiCredentials.js";

interface SiteApiResolver {
	resolveSite(credentials: ApiCredentials): Promise<Site | null>;
}

export type { SiteApiResolver };
