import { FormIntegration } from "./form/formIntegration.js";
import { WidgetIntegration } from "./widget/widgetIntegration.js";
import { createIntegrationConnector } from "#integration/connector/integrationConnectorFactory.js";

const formIntegration = new FormIntegration();
const widgetIntegration = new WidgetIntegration();

const integrationConnector = createIntegrationConnector();

integrationConnector.connectIntegration(formIntegration);
integrationConnector.connectIntegration(widgetIntegration);
