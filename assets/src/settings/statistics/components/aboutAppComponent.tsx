import * as React from "react";
import { SectionComponent } from "./sectionComponent.js";
import { CallToActionComponent } from "./callToActionComponent.js";
import type { AboutLabels } from "#settings/statistics/config.js";

interface AboutAppComponentProperties {
	labels: AboutLabels;
}

class AboutAppComponent extends React.Component<AboutAppComponentProperties> {
	render() {
		const { labels } = this.props;

		return (
			<SectionComponent
				title={labels.title}
				icon="icon-[material-symbols--preview]"
			>
				<CallToActionComponent
					message={labels.message}
					buttonComponentProperties={{
						label: labels.buttonLabel,
						href: "https://portal.prosopo.io/?utm_source=procaptcha-wp&utm_medium=plugin&utm_campaign=settings&utm_content=statistics-about",
						icon: "icon-[material-symbols--settings-account-box-outline]",
					}}
				/>
			</SectionComponent>
		);
	}
}

export { AboutAppComponent };
