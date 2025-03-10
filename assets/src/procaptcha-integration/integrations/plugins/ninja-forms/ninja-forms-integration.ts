import { createIntegrationConnector } from "../../../connector/integrationConnectorFactory.js";
import { NinjaFormsIntegration } from "./ninjaFormsIntegration.js";

const ninjaFormsIntegration = new NinjaFormsIntegration();

const integrationConnector = createIntegrationConnector();
integrationConnector.connectIntegration(ninjaFormsIntegration);
