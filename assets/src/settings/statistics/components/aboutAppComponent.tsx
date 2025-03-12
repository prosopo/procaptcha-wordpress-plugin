import * as React from "react";
import { StateLabels } from "../config.js";
import { SectionComponent } from "./sectionComponent.js";
import { LinkComponent } from "./linkComponent.js";
import { CallToActionComponent } from "./callToActionComponent.js";

class AboutAppComponent extends React.Component {
	render() {
		return (
			/*fixme translate*/
			<SectionComponent
				title="Information By Your Site Key"
				icon="icon-[material-symbols--preview]"
			>
				<CallToActionComponent
					message={
						"This page displays general statistics and information by your site key. To manage your account and site settings visit the Prosopo portal."
					}
					button={{
						label: "Open the Portal",
						href: "https://portal.prosopo.io",
						icon: "icon-[material-symbols--settings-account-box-outline]",
					}}
				/>
			</SectionComponent>
		);
	}
}

export { AboutAppComponent };
