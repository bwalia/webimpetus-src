Cypress.Commands.add('submitForm', (selector) => {
  cy.get(selector).submit();
});

Cypress.Commands.add('fillField', (selector, value) => {
  cy.get(selector).clear().type(value);
});

Cypress.Commands.add('checkA11y', () => {
  cy.checkA11y();
});
