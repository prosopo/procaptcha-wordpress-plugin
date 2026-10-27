import * as React from "react";
import { SectionComponent } from "./sectionComponent.js";
import type { ChallengeLabels } from "#settings/statistics/config.js";
import { ProcaptchaAccountTiers } from "#settings/procaptcha/procaptchaAccount.js";
import { LinkComponent } from "./linkComponent.js";

interface ChallengeComponentProperties {
	labels: ChallengeLabels;
	accountTier: string;
	captchaType: string;
	callToUpgradeElementMarkup: string;
}

// The challenge type is a property of the site key, set in the portal: the
// widget ignores any type the page asks for.
class ChallengeComponent extends React.Component<ChallengeComponentProperties> {
	public render() {
		const { labels, accountTier, captchaType } = this.props;

		if (0 === accountTier.length) {
			return null;
		}

		const isFree = ProcaptchaAccountTiers.FREE === accountTier;
		const typeLabel =
			labels.types[captchaType] || labels.types["frictionless"];

		return (
			<SectionComponent
				title={labels.title}
				icon="icon-[material-symbols--shield-person-outline]"
			>
				<div className="flex flex-col gap-6">
					<div className="flex justify-between gap-4">
						<p className="text-gray">{labels.current}</p>
						<p className="font-medium">{typeLabel}</p>
					</div>

					{isFree ? (
						<div className="flex flex-col gap-4">
							<p>{labels.changeOnPaidPlan}</p>
							<div
								dangerouslySetInnerHTML={{
									__html: this.props
										.callToUpgradeElementMarkup,
								}}
							></div>
						</div>
					) : (
						<div className="flex flex-col items-start gap-4">
							<p>{labels.changeInPortal}</p>
							<LinkComponent
								label={labels.buttonLabel}
								href="https://portal.prosopo.io/?utm_source=procaptcha-wp&utm_medium=plugin&utm_campaign=settings&utm_content=statistics-challenge"
								icon="icon-[material-symbols--settings-account-box-outline]"
							/>
						</div>
					)}
				</div>
			</SectionComponent>
		);
	}
}

export { ChallengeComponent, ChallengeComponentProperties };
