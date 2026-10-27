import * as React from "react";
import type CaptchaUsageNumberUtils from "#settings/statistics/captchaUsage/captchaUsageNumberUtils.js";
import type { InsightsLabels } from "#settings/statistics/config.js";
import { SectionComponent } from "#settings/statistics/components/sectionComponent.js";
import { fillTemplate } from "./fillTemplate.js";

interface SetupCheckComponentProperties {
	served: number;
	solved: number;
	checked: number;
	labels: InsightsLabels;
	numberUtils: CaptchaUsageNumberUtils;
}

interface Stage {
	label: string;
	value: number;
}

// Below this, enough solved tokens go unverified that a form is probably
// accepting submissions without asking Prosopo whether the token is real.
const HEALTHY_CHECK_RATE = 80;

const VERIFY_DOCS_URL =
	"https://docs.prosopo.io/en/basics/server-side-verification/?utm_source=procaptcha-wp&utm_medium=plugin&utm_campaign=settings&utm_content=statistics-setup-check";

class SetupCheckComponent extends React.Component<SetupCheckComponentProperties> {
	render() {
		const { served, solved, checked, labels, numberUtils } = this.props;

		const stages: Stage[] = [
			{ label: labels.served, value: served },
			{ label: labels.solved, value: solved },
			{ label: labels.checked, value: checked },
		];
		const checkRate = Math.round(numberUtils.getPercent(checked, solved));
		const healthy = 0 === solved || checkRate >= HEALTHY_CHECK_RATE;

		return (
			<SectionComponent
				title={labels.setupTitle}
				icon="icon-[material-symbols--fact-check-outline]"
			>
				<div className="flex flex-col gap-5">
					<div className="flex flex-col gap-3">
						{stages.map((stage: Stage) => {
							const width = numberUtils.getPercent(
								stage.value,
								served,
							);
							return (
								<div
									key={stage.label}
									className="flex flex-col gap-1"
								>
									<div className="flex justify-between gap-4">
										<p className="m-0">{stage.label}</p>
										<p className="m-0 font-medium">
											{numberUtils.formatNumber(
												stage.value,
											)}
										</p>
									</div>
									<div className="h-2 rounded-full bg-purple-light overflow-hidden">
										<div
											className="h-full rounded-full bg-blue"
											style={{
												width: `${numberUtils.visualizePercent(width)}%`,
											}}
										></div>
									</div>
								</div>
							);
						})}
					</div>

					{healthy ? (
						<p className="m-0 flex items-start gap-2 text-success-dark">
							<span className="icon-[material-symbols--check-circle-outline] w-5 h-5 shrink-0"></span>
							<span>
								{fillTemplate(labels.setupOk, {
									percent: String(checkRate),
								})}
							</span>
						</p>
					) : (
						<div className="flex items-start gap-2 rounded py-3 px-4 bg-pink-light text-navy">
							<span className="icon-[material-symbols--warning-outline] w-5 h-5 shrink-0 text-warning-dark"></span>
							<div className="flex flex-col items-start gap-1">
								<p className="m-0">
									{fillTemplate(labels.setupWarn, {
										percent: String(checkRate),
									})}
								</p>
								<a
									className="text-blue"
									target="_blank"
									rel="noreferrer"
									href={VERIFY_DOCS_URL}
								>
									{labels.setupWarnLink}
								</a>
							</div>
						</div>
					)}
				</div>
			</SectionComponent>
		);
	}
}

export { SetupCheckComponent };
