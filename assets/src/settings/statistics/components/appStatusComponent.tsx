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
	private readonly stateElementVendors: {
		[key in StatCurrentState]: () => React.ReactNode;
	};

	constructor(appStatus: AppStatus) {
		super(appStatus);

		this.stateElementVendors = {
			[StatCurrentState.LOADING]:
				this.getLoadingInProgressElement.bind(this),
			[StatCurrentState.FAILED]: this.getLoadingFailedElement.bind(this),
			[StatCurrentState.LOADED]:
				this.getLoadingCompleteElement.bind(this),
		};
	}

	public render(): React.ReactNode {
		const stateElement = this.stateElementVendors[this.props.state]();

		return <div className="flex justify-between">{stateElement}</div>;
	}

	protected getLoadingInProgressElement(): React.ReactNode {
		return (
			<div className="flex items-center gap-1.5 text-yellow-500">
				<span className="icon-[eos-icons--arrow-rotate] w-5 h-5"></span>
				<p>{this.props.labels.loading}</p>
			</div>
		);
	}

	protected getLoadingFailedElement(): React.ReactNode {
		return (
			<div className="flex items-center gap-1.5 text-red-500">
				<span className="icon-[eos-icons--critical-bug] w-5 h-5"></span>
				<p>{this.props.labels.failedToLoad}</p>
			</div>
		);
	}

	protected getLoadingCompleteElement(): React.ReactNode {
		const labels = this.props.labels;

		const currentDateTimeLabel = this.getDateTimeLabel(new Date());

		return (
			<div className="flex gap-1.5 items-center text-green-500">
				<span className="icon-[material-symbols--check-circle-outline] w-5 h-5"></span>
				<p className="">{labels.lastRefreshedAt}</p>
				<p className="font-medium">{currentDateTimeLabel}</p>
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

	protected getDateTimeLabel(dateTime: Date): string {
		const hours = dateTime.getHours();
		const minutes = dateTime.getMinutes();

		return `${this.formatTimeNumber(hours)}:${this.formatTimeNumber(minutes)}`;
	}

	protected formatTimeNumber(value: number): string {
		return value < 10 ? "0" + value : value.toString();
	}
}

export { StatCurrentState, AppStatusComponent, AppStatus };
