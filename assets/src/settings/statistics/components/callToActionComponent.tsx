import React from "react";
import { Link, LinkComponent } from "./linkComponent.js";

interface CallToAction {
	message: string;
	button: Link;
}

class CallToActionComponent extends React.Component<CallToAction> {
	public render(): React.ReactNode {
		const { message, button } = this.props;

		return (
			<div className="flex justify-between items-center">
				<p className="flex-1">{message}</p>
				<div className="flex justify-center flex-1">
					<LinkComponent {...button} />
				</div>
			</div>
		);
	}
}

export { CallToActionComponent };
