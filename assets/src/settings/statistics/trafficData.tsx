import * as React from "react";
import {TrafficDataLabels} from "./config";
import {BoxElement} from "./box";
import LoggerInterface from "../../interfaces/loggerInterface";

interface TrafficData {
    isSupported: boolean;
    labels: TrafficDataLabels;
    logger: LoggerInterface;
    classes?: string;
}

class TrafficDataElement extends React.Component<TrafficData> {
    render() {
        const labels = this.props.labels;

        let content = null;

        if (false === this.props.isSupported) {
            content = <p>{labels.upgradeNotice}</p>;
        } else {
            // fixme visit portal notice
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

export {TrafficDataElement, TrafficData};
