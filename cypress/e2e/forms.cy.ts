describe('Comprehensive Form Testing', () => {
  beforeEach(() => {
    cy.visit('/form'); // Adjust path as needed
  });

  it('validates required fields (sync)', { tags: ['@forms', '@smoke'] }, () => {
    cy.get('[data-cy=name]').focus().blur();
    cy.contains('Name is required').should('be.visible');
  });

  it('validates async fields', { tags: ['@forms'] }, () => {
    cy.get('[data-cy=email]').type('taken@example.com');
    cy.contains('Checking...').should('be.visible');
    cy.contains('Email already taken').should('be.visible');
  });

  it('handles conditional fields', { tags: ['@forms'] }, () => {
    cy.get('[data-cy=has-pet]').check();
    cy.get('[data-cy=pet-name]').should('be.visible');
  });

  it('supports multi-step forms', { tags: ['@forms'] }, () => {
    cy.get('[data-cy=next-step]').click();
    cy.get('[data-cy=step-2]').should('be.visible');
  });

  it('uploads files', { tags: ['@forms'] }, () => {
    cy.get('[data-cy=file-upload]').selectFile('cypress/fixtures/sample.pdf');
    cy.contains('sample.pdf').should('be.visible');
  });

  it('shows i18n labels', { tags: ['@forms'] }, () => {
    cy.contains('Nombre').should('exist'); // Spanish label example
  });

  it('is accessible (a11y)', { tags: ['@forms', '@a11y'] }, () => {
    cy.checkA11y();
  });

  it('supports keyboard navigation', { tags: ['@forms'] }, () => {
    cy.get('[data-cy=name]').focus().tab();
    cy.focused().should('have.attr', 'data-cy', 'email');
  });

  it('autosaves progress', { tags: ['@forms'] }, () => {
    cy.get('[data-cy=name]').type('AutoSave User');
    cy.wait(1000); // Simulate debounce
    cy.contains('Progress saved').should('be.visible');
  });

  it('handles error states', { tags: ['@forms'] }, () => {
    cy.intercept('POST', '/api/form', { statusCode: 500 }).as('formSubmit');
    cy.get('[data-cy=submit]').click();
    cy.contains('Something went wrong').should('be.visible');
  });

  it('handles rate limiting (429)', { tags: ['@forms'] }, () => {
    cy.intercept('POST', '/api/form', { statusCode: 429 }).as('formSubmit');
    cy.get('[data-cy=submit]').click();
    cy.contains('Too many requests').should('be.visible');
  });
});
