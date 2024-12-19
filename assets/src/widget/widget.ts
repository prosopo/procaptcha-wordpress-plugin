import LoggerFactory from "../logger/loggerFactory";
import FormValidator from "./formValidator";
import ModuleLogger from "../logger/moduleLogger";
import registerWebComponent from "../registerWebComponent";
import WidgetRenderer from "./widgetRenderer";

const loggerFactory = new LoggerFactory();
const moduleLogger = new ModuleLogger();

const widgetRenderer = new WidgetRenderer(
	loggerFactory.makeLogger("widget-renderer", moduleLogger),
);
const formValidator = new FormValidator(
	loggerFactory.makeLogger("form-validator", moduleLogger),
);

const webComponentRegistarLogger = loggerFactory.makeLogger(
	"web-component-registar",
	moduleLogger,
);

registerWebComponent(webComponentRegistarLogger, {
	name: "prosopo-procaptcha-wp-widget",
	componentController: widgetRenderer,
	processIfReconnected: false,
	// wait, case we need to make sure window.procaptcha is available.
	waitWindowLoadedInsteadOfDomLoaded: true,
});

registerWebComponent(webComponentRegistarLogger, {
	name: "prosopo-procaptcha-wp-form",
	componentController: formValidator,
	processIfReconnected: false,
	waitWindowLoadedInsteadOfDomLoaded: false,
});
