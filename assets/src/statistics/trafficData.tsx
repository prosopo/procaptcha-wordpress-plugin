import * as React from "react";
import { ReactElement } from "react";
import { TrafficDataLabels } from "./config";
import { BoxElement } from "./box";
import { TrafficDataItem } from "./api";
import { Line } from "react-chartjs-2";
import {
	CategoryScale,
	Chart as ChartJS,
	Legend,
	LinearScale,
	LineElement,
	PointElement,
	Title,
	Tooltip,
} from "chart.js";
import LoggerInterface from "../interfaces/logger";

ChartJS.register(
	CategoryScale,
	LinearScale,
	PointElement,
	LineElement,
	Title,
	Tooltip,
	Legend,
);

interface TrafficData {
	isSupported: boolean;
	labels: TrafficDataLabels;
	items: TrafficDataItem[];
	logger: LoggerInterface;
	classes?: string;
}

class TrafficDataElement extends React.Component<TrafficData> {
	protected getChart(): ReactElement {
		const { items, labels } = this.props;

		const powCounts = items.map((item) => item.powCount);
		const imageCounts = items.map((item) => item.imageCount);

		const chartLabels = items.map((item) => {
			const date = new Date(
				item.year,
				item.month - 1,
				item.day,
				item.hour,
			);
			const monthName = date.toLocaleString("default", {
				month: "short",
			});

			return `${item.day} ${monthName} - ${item.hour}:00`;
		});

		this.props.logger.debug("chart data is ready", {
			chartLabels: chartLabels,
			powCounts: powCounts,
			imageCounts: imageCounts,
		});

		const chartData = {
			labels: chartLabels,
			datasets: [
				{
					label: labels.powSubmissions,
					data: powCounts,
					borderColor: "#22c55e",
					backgroundColor: "#22c55e",
					borderWidth: 2,
				},
				{
					label: labels.imageSubmissions,
					data: imageCounts,
					borderColor: "#eab308",
					backgroundColor: "#eab308",
					borderWidth: 2,
				},
			],
		};

		const options = {
			responsive: true,
			plugins: {
				legend: {
					position: "bottom" as const,
				},
				title: {
					display: true,
					text: labels.chartTitle,
				},
			},
			scales: {
				x: {
					title: {
						display: true,
						text: labels.time,
					},
				},
				y: {
					title: {
						display: true,
						text: labels.submissionsCount,
					},
				},
			},
		};

		return <Line data={chartData} options={options} />;
	}

	render() {
		const labels = this.props.labels;

		let content = null;

		if (false === this.props.isSupported) {
			content = <p>{labels.upgradeNotice}</p>;
		} else {
			content = this.getChart();
		}

		const classes = this.props.classes || "";

		return (
			<BoxElement
				title={labels.title}
				icon="material-symbols--analytics"
				classes={classes}
			>
				{content}
			</BoxElement>
		);
	}
}

export { TrafficDataElement, TrafficData };
