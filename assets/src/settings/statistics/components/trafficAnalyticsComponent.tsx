import * as React from "react";
import { TrafficDataLabels } from "../config.js";
import { SectionComponent } from "./sectionComponent.js";
import Logger from "../../../logger/logger.js";
import { AccountTiers } from "../account/accountTiers.js";
import { CallToActionComponent } from "./callToActionComponent.js";
import { PromoComponent } from "./promoComponent.js";

interface TrafficAnalyticsComponentProperties {
	accountTier: string;
	labels: TrafficDataLabels;
	logger: Logger;
	classes?: string;
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

	protected getContentElement(): React.ReactNode {
		return AccountTiers.FREE === this.props.accountTier
			? this.getCallToUpgradeElement()
			: this.getCallToVisitPortalElement();
	}

	protected getCallToUpgradeElement(): React.ReactNode {
		/* fixme translate */
		return (
			<PromoComponent
				title={"Unlock Analytics with Pro tier"}
				icon={"icon-[material-symbols--family-star]"}
				items={[
					"Up to 1M monthly requests",
					"Rapid technical support",
					"Unlimited number of sites",
					"Advanced user management",
					"Traffic analytics and statistics",
				]}
				actionLinkComponentProperties={{
					label: "Upgrade",
					href: "https://portal.prosopo.io/",
					icon: "icon-[material-symbols--upgrade]",
				}}
			/>
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
