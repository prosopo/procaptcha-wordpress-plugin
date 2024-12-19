const { defineConfig } = require("cypress");

module.exports = defineConfig({
  e2e: {
    setupNodeEvents(on, config) {
      require("cypress-terminal-report/src/installLogsPrinter")(on, {
        outputRoot: config.projectRoot + "/cypress/",
        specRoot: "cypress/e2e",
        outputTarget: {
          "logs|html": "html",
        },
      });
    },
    baseUrl: "http://procaptcha.local",
    watchForFileChanges: false, // Disable auto-run on file changes.
    defaultCommandTimeout: 6000, // default 4000.
    viewportWidth: 1280, // default 1000.
    viewportHeight: 720, // default 660.
  },
});
