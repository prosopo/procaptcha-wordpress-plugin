import * as React from "react";
import { SectionComponent } from "./sectionComponent.js";
import { CallToActionComponent } from "./callToActionComponent.js";
import type { TrafficDataLabels } from "#settings/statistics/config.js";

interface TrafficAnalyticsComponentProperties {
	labels: TrafficDataLabels;
	classes?: string;
}

// Traffic analytics is on every plan, Free included; the charts live in the portal.
class TrafficAnalyticsComponent extends React.Component<TrafficAnalyticsComponentProperties> {
	public render() {
		const { labels } = this.props;

		return (
			<SectionComponent
				title={labels.title}
				icon="icon-[material-symbols--analytics]"
				classes={this.props.classes || ""}
			>
				<CallToActionComponent
					message={labels.message}
					buttonComponentProperties={{
						label: labels.buttonLabel,
						href: "https://portal.prosopo.io/traffic?utm_source=procaptcha-wp&utm_medium=plugin&utm_campaign=settings&utm_content=statistics-traffic",
						icon: "icon-[material-symbols--insert-chart]",
					}}
				/>
			</SectionComponent>
		);
	}
}

export { TrafficAnalyticsComponent, TrafficAnalyticsComponentProperties };
