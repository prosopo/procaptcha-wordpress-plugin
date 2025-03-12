import * as React from "react";
import { TrafficDataLabels } from "../config.js";
import { SectionComponent } from "./sectionComponent.js";
import Logger from "../../../logger/logger.js";
import { AccountTiers } from "../account/accountTiers.js";

interface TrafficAnalytics {
	accountTier: string;
	labels: TrafficDataLabels;
	logger: Logger;
	classes?: string;
}

class TrafficAnalyticsComponent extends React.Component<TrafficAnalytics> {
	public render() {
		const labels = this.props.labels;

		const content =
			AccountTiers.FREE === this.props.accountTier
				? this.getCallToUpgradeElement()
				: this.getCallToVisitPortalElement();

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

	protected getCallToUpgradeElement(): React.ReactNode {
		const labels = this.props.labels;

		/*todo turn into the link*/
		return <p>{labels.upgradeNotice}</p>;
	}

	protected getCallToVisitPortalElement(): React.ReactNode {
		/*fixme*/
		return <div>Visit the Portal to see traffic for all your sites.</div>;
	}
}

export { TrafficAnalyticsComponent, TrafficAnalytics };
