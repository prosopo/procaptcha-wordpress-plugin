import { createIntegrationConnector } from "#integration/connector/integrationConnectorFactory.js";
import { NinjaFormsIntegration } from "#integrations/plugins/ninja-forms/ninjaFormsIntegration.js";

const ninjaFormsIntegration = new NinjaFormsIntegration();

const integrationConnector = createIntegrationConnector();
integrationConnector.connectIntegration(ninjaFormsIntegration);
