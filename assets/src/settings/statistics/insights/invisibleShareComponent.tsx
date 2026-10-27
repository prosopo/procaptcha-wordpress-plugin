import * as React from "react";
import type CaptchaUsageNumberUtils from "#settings/statistics/captchaUsage/captchaUsageNumberUtils.js";
import type { MonthlySessions } from "#settings/procaptcha/procaptchaSite.js";
import type { InsightsLabels } from "#settings/statistics/config.js";
import { SectionComponent } from "#settings/statistics/components/sectionComponent.js";
import { fillTemplate } from "./fillTemplate.js";

interface InvisibleShareComponentProperties {
	sessions: MonthlySessions;
	labels: InsightsLabels;
	numberUtils: CaptchaUsageNumberUtils;
}

// One share of a whole, so a meter: the track is a lighter step of the fill's hue.
class InvisibleShareComponent extends React.Component<InvisibleShareComponentProperties> {
	render() {
		const { sessions, labels, numberUtils } = this.props;
		const percent: number = Math.round(
			numberUtils.getPercent(sessions.passedInvisibly, sessions.total),
		);

		return (
			<SectionComponent
				title={labels.invisibleTitle}
				icon="icon-[material-symbols--visibility-off-outline]"
			>
				<div className="flex flex-col gap-3">
					<p className="m-0 text-4xl font-semibold leading-none text-navy">
						{percent}%
					</p>
					<div
						className="h-2 rounded-full bg-purple-light overflow-hidden"
						role="meter"
						aria-valuemin={0}
						aria-valuemax={100}
						aria-valuenow={percent}
						aria-label={labels.invisibleTitle}
					>
						<div
							className="h-full rounded-full bg-blue"
							style={{ width: `${percent}%` }}
						></div>
					</div>
					<p className="m-0 text-gray">
						{fillTemplate(labels.invisibleCaption, {
							percent: String(percent),
						})}
					</p>
				</div>
			</SectionComponent>
		);
	}
}

export { InvisibleShareComponent };
