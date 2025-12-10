import * as ReactDOM from "react-dom/client";
import * as React from "react";
import { AppComponent } from "./appComponent.js";
import type { WebComponent } from "../../../utils/webComponent/webComponent.js";
import type Logger from "../../../utils/logger/logger.js";

class StatisticsWebComponent implements WebComponent {
	constructor(private readonly logger: Logger) {}

	constructComponent(element: HTMLElement): void {
		element.innerHTML = "";

		const root = ReactDOM.createRoot(element);

		root.render(
			<React.StrictMode>
				<AppComponent logger={this.logger} />
			</React.StrictMode>,
		);
	}
}

export { StatisticsWebComponent };
