import * as React from "react";
import { StateLabels } from "../config.js";

enum StatCurrentState {
	LOADING = "loading",
	LOADED = "ok",
	FAILED = "failed",
}

interface AppStatus {
	state: StatCurrentState;
	reload: () => void;
	labels: StateLabels;
}

class AppStatusComponent extends React.Component<AppStatus> {
	getCurrentState() {
		const { labels } = this.props;

		switch (this.props.state) {
			case StatCurrentState.LOADING:
				return (
					<div className="flex items-center gap-1.5 text-yellow-500">
						<span className="icon-[eos-icons--arrow-rotate] w-5 h-5"></span>
						<p>{labels.loading}</p>
					</div>
				);
			case StatCurrentState.FAILED:
				return (
					<div className="flex items-center gap-1.5 text-red-500">
						<span className="icon-[eos-icons--critical-bug] w-5 h-5"></span>
						<p>{labels.failedToLoad}</p>
					</div>
				);
			case StatCurrentState.LOADED:
				const now = new Date();
				const hours =
					now.getHours() < 10
						? "0" + now.getHours()
						: now.getHours().toString();
				const minutes =
					now.getMinutes() < 10
						? "0" + now.getMinutes()
						: now.getMinutes().toString();

				return (
					<div className="flex gap-1.5 items-center text-green-500">
						<span className="icon-[material-symbols--check-circle-outline] w-5 h-5"></span>
						<p className="">{labels.lastRefreshedAt}</p>
						<p className="font-medium">
							{hours}:{minutes}
						</p>
						<p>-</p>
						<button
							onClick={this.props.reload}
							className="underline cursor-pointer transition hover:text-green-700"
						>
							{labels.refreshNow}
						</button>
					</div>
				);
		}
	}

	render() {
		const { labels } = this.props;

		return (
			<div className="flex flex-col gap-y-6">
				<a
					target="_blank"
					rel="noreferrer"
					href="https://portal.prosopo.io/"
					className="py-1.5 px-3 bg-blue text-white rounded transition cursor-pointer
    hover:bg-blue-dark"
				>
					<div></div>
					{labels.toChangeVisitPortal}
				</a>

				{this.getCurrentState()}
			</div>
		);
	}
}

export { StatCurrentState, AppStatusComponent, AppStatus };
