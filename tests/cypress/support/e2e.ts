// ***********************************************************
// This example support/e2e.js is processed and
// loaded automatically before your test files.
//
// This is a great place to put global configuration and
// behavior that modifies Cypress.
//
// You can change the location of this file or turn off
// automatically serving support files with the
// 'supportFile' configuration option.
//
// You can read more here:
// https://on.cypress.io/configuration
// ***********************************************************

// Import commands.js using ES2015 syntax:
import "./commands";
import { Runnable } from "mocha";

// Alternatively you can use CommonJS syntax:
// require('./commands')

// do not fail on JS errors, since plugins add their own JS, and we don't care about them.
Cypress.on("uncaught:exception", (err: Error, runnable: Runnable): false => {
	return false;
});

// default collectTypes:
// ['cons:log','cons:info', 'cons:warn', 'cons:error', 'cy:log', 'cy:xhr', 'cy:request', 'cy:intercept', 'cy:command']
require("cypress-terminal-report/src/installLogsCollector")();
