import LoggerFactory from "../logger/loggerFactory.js";
import PluginModuleLogger from "../logger/plugin/pluginModuleLogger.js";
import { WebComponentRegistrar } from "./webComponent/webComponentRegistrar.js";
import { WidgetComponentsRegistrar } from "./widgetComponentsRegistrar.js";
import { WidgetFactory } from "./widgetFactory.js";

const loggerFactory = new LoggerFactory();
const moduleLogger = new PluginModuleLogger();

const componentLogger = loggerFactory.makeLogger(
	"web-component-registrar",
	moduleLogger,
);
const componentRegistrar = new WebComponentRegistrar(componentLogger);
const widgetComponentsRegistrar = new WidgetComponentsRegistrar(
	componentRegistrar,
);

const widgetFactory = new WidgetFactory(
	loggerFactory,
	moduleLogger,
	widgetComponentsRegistrar,
);

widgetFactory.createWidget();
