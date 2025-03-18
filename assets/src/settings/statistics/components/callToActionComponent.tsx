import React from "react";
import { LinkComponentProperties, LinkComponent } from "./linkComponent.js";

interface CallToActionComponentProperties {
	message: string;
	buttonComponentProperties: LinkComponentProperties;
}

class CallToActionComponent extends React.Component<CallToActionComponentProperties> {
	public render(): React.ReactNode {
		const { message, buttonComponentProperties } = this.props;

		return (
			<div className="flex justify-between items-center">
				<p className="flex-1">{message}</p>
				<div className="flex justify-center flex-1">
					<LinkComponent {...buttonComponentProperties} />
				</div>
			</div>
		);
	}
}

export { CallToActionComponent };
