import * as React from "react";
import { StateLabels } from "../config.js";

enum StatCurrentState {
	LOADING = "loading",
	LOADED = "ok",
	FAILED = "failed",
}

interface AppStatusComponentProperties {
	state: StatCurrentState;
	reload: () => void;
	labels: StateLabels;
}

class AppStatusComponent extends React.Component<AppStatusComponentProperties> {
	private readonly stateElementVendors: {
		[key in StatCurrentState]: () => React.ReactNode;
	};

	constructor(appStatus: AppStatusComponentProperties) {
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
			<div className="flex items-center gap-1.5 text-warning-dark">
				<span className="icon-[eos-icons--arrow-rotate] w-5 h-5"></span>
				<p>{this.props.labels.loading}</p>
			</div>
		);
	}

	protected getLoadingFailedElement(): React.ReactNode {
		return (
			<div className="flex items-start gap-3 rounded py-4 px-5 bg-pink-light text-navy">
				<span className="icon-[material-symbols--error-outline] w-5 h-5 shrink-0 text-pink-dark"></span>
				<div className="flex flex-col items-start gap-2">
					<p className="m-0">{this.props.labels.failedToLoad}</p>
					<button
						onClick={this.props.reload}
						className="underline cursor-pointer bg-transparent [border:none] p-0 text-blue transition hover:text-blue-dark"
					>
						{this.props.labels.tryAgain}
					</button>
				</div>
			</div>
		);
	}

	protected getLoadingCompleteElement(): React.ReactNode {
		const labels = this.props.labels;

		const currentDateTimeLabel = this.getDateTimeLabel(new Date());

		return (
			<div className="flex gap-1.5 items-center text-success-dark">
				<span className="icon-[material-symbols--check-circle-outline] w-5 h-5"></span>
				<p className="">{labels.lastRefreshedAt}</p>
				<p className="font-medium">{currentDateTimeLabel}</p>
				<p>-</p>
				<button
					onClick={this.props.reload}
					className="underline cursor-pointer transition hover:text-navy"
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

export { StatCurrentState, AppStatusComponent, AppStatusComponentProperties };
