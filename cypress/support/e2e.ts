import './commands';
import 'cypress-axe';
import 'cypress-real-events/support';

beforeEach(() => {
  cy.injectAxe();
});
