import * as React from "react";
import { StateLabels } from "../config.js";
import { SectionComponent } from "./sectionComponent.js";

class AboutAppComponent extends React.Component {
	render() {
		return (
			/*fixme translate*/
			<SectionComponent
				title="Information By Your Site Key"
				icon="icon-[material-symbols--preview]"
			>
				<div className="flex justify-between items-center">
					<p className="flex-1">
						This page displays general statistics and information by
						your site key. To manage your account and site settings
						visit the Prosopo portal.
					</p>
					<div className="flex justify-center flex-1">
						<a
							target="_blank"
							rel="noreferrer"
							href="https://portal.prosopo.io/"
							className="flex items-center gap-2 px-4 py-2 text-sm rounded text-white bg-indigo-800 transition cursor-pointer
    hover:bg-indigo-700"
						>
							<span>Open the Portal</span>
							<span className="icon-[material-symbols--settings-account-box-outline] w-6 h-6"></span>
						</a>
					</div>
				</div>
			</SectionComponent>
		);
	}
}

export { AboutAppComponent };
