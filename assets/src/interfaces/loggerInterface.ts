import logLevel from "./logLevel";

interface LoggerInterface {
	log(level: logLevel, message: string, args?: object): void;

	debug(message: string, args?: object): void;

	warning(message: string, args?: object): void;
}

export default LoggerInterface;
