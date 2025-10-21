import { defineConfig } from 'cypress';
import cypressGrep from 'cypress-grep/src/plugin';

// Allows baseUrl override via CYPRESS_BASE_URL env variable (for CI, staging, etc)
const baseUrl = process.env.CYPRESS_BASE_URL || 'https://dev001.workstation.co.uk/';

export default defineConfig({
  e2e: {
    baseUrl,
    specPattern: 'cypress/e2e/**/*.cy.{js,ts}',
    supportFile: 'cypress/support/e2e.ts',
    setupNodeEvents(on, config) {
      cypressGrep(config);
      return config;
    },
    video: true,
    screenshotsFolder: 'cypress/screenshots',
    videosFolder: 'cypress/videos',
    retries: 2,
    defaultCommandTimeout: 8000,
    env: {
      grepFilterSpecs: true,
      grepOmitFiltered: true,
    },
  },
  component: {
    devServer: {
      framework: 'react', // or 'vue', adjust as needed
      bundler: 'webpack',
    },
    specPattern: 'cypress/component/**/*.cy.{js,ts,jsx,tsx}',
  },
});
