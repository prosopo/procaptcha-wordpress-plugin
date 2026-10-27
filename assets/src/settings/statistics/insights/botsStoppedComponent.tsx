import * as React from "react";
import type CaptchaUsageNumberUtils from "#settings/statistics/captchaUsage/captchaUsageNumberUtils.js";
import type { MonthlySessions } from "#settings/procaptcha/procaptchaSite.js";
import type { InsightsLabels } from "#settings/statistics/config.js";
import { fillTemplate } from "./fillTemplate.js";

interface BotsStoppedComponentProperties {
	sessions: MonthlySessions;
	labels: InsightsLabels;
	numberUtils: CaptchaUsageNumberUtils;
}

// The page's one hero figure: what the plugin did for the site this month.
class BotsStoppedComponent extends React.Component<BotsStoppedComponentProperties> {
	render() {
		const { sessions, labels, numberUtils } = this.props;
		const stopped: number = sessions.blocked + sessions.rejected;

		return (
			<div className="flex flex-col justify-center gap-2 rounded py-6 px-7 bg-navy text-white">
				<p className="m-0 text-sm text-white/75">{labels.botsTitle}</p>
				<p className="m-0 text-5xl font-semibold leading-none text-white">
					{numberUtils.formatNumber(stopped)}
				</p>
				<p className="m-0 text-sm text-white/75">
					{fillTemplate(labels.botsCaption, {
						total: numberUtils.formatNumber(sessions.total),
					})}
				</p>
			</div>
		);
	}
}

export { BotsStoppedComponent };
