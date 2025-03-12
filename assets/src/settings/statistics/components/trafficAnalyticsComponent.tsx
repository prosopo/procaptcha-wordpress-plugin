import * as React from "react";
import { TrafficDataLabels } from "../config.js";
import { SectionComponent } from "./sectionComponent.js";
import Logger from "../../../logger/logger.js";

interface TrafficData {
	isSupported: boolean;
	labels: TrafficDataLabels;
	logger: Logger;
	classes?: string;
}

class TrafficAnalyticsComponent extends React.Component<TrafficData> {
	render() {
		const labels = this.props.labels;

		let content = null;

		if (false === this.props.isSupported) {
			content = <p>{labels.upgradeNotice}</p>;
		} else {
			// fixme visit portal notice
		}

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
}

export { TrafficAnalyticsComponent, TrafficData };
