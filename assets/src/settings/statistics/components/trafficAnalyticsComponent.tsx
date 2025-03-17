import * as React from "react";
import { TrafficDataLabels } from "../config.js";
import { SectionComponent } from "./sectionComponent.js";
import Logger from "../../../logger/logger.js";
import { AccountTiers } from "../../account/accountTiers.js";
import { CallToActionComponent } from "./callToActionComponent.js";

interface TrafficAnalyticsComponentProperties {
	accountTier: string;
	labels: TrafficDataLabels;
	logger: Logger;
	classes?: string;
	callToUpgradeElementMarkup: string;
}

class TrafficAnalyticsComponent extends React.Component<TrafficAnalyticsComponentProperties> {
	public render() {
		const labels = this.props.labels;
		const content = this.getContentElement();

		const classes = this.props.classes || "";

		return (
			<SectionComponent
				title={labels.title}
				icon="icon-[material-symbols--analytics]"
				classes={classes}
			>
				{content}
			</SectionComponent>
		);
	}

	protected getContentElement(): React.ReactNode | null {
		const accountTier = this.props.accountTier;

		if (0 === accountTier.length) {
			return null;
		}

		return AccountTiers.FREE === this.props.accountTier
			? this.getCallToUpgradeElement()
			: this.getCallToVisitPortalElement();
	}

	protected getCallToUpgradeElement(): React.ReactNode {
		const { callToUpgradeElementMarkup } = this.props;

		return (
			<div
				dangerouslySetInnerHTML={{ __html: callToUpgradeElementMarkup }}
			></div>
		);
	}

	protected getCallToVisitPortalElement(): React.ReactNode {
		/*fixme translate*/
		return (
			<CallToActionComponent
				message={
					"Your tier includes access to the detailed traffic analytics. Visit the portal to see charts."
				}
				buttonComponentProperties={{
					label: "View the traffic analytics",
					href: "https://portal.prosopo.io/traffic",
					icon: "icon-[material-symbols--insert-chart]",
				}}
			/>
		);
	}
}

export { TrafficAnalyticsComponent, TrafficAnalyticsComponentProperties };
