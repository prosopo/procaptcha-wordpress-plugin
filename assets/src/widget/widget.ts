import LoggerFactory from "../logger/loggerFactory.js";
import PluginModuleLogger from "../logger/plugin/pluginModuleLogger.js";
import { WebComponentFactory } from "./webComponent/webComponentFactory.js";
import { WidgetComponents } from "./components/widgetComponents.js";

const loggerFactory = new LoggerFactory();
const moduleLogger = new PluginModuleLogger();

const componentLogger = loggerFactory.makeLogger(
	"web-component-registrar",
	moduleLogger,
);
const webComponentFactory = new WebComponentFactory(componentLogger);

const widgetComponents = new WidgetComponents(
	loggerFactory,
	moduleLogger,
	webComponentFactory,
);

widgetComponents.createWidgetComponents();
