const views = [
  'payments',
  'accounts',
  'products',
  'receipts',
  'accounting-periods',
  'trial-balance',
  'profit-loss',
  'balance-sheet',
  'journal-entries',
  'sales_invoices',
  'webpages',
  'blocks',
  'tasks',
  'templates',
  'domains',
  'documents',
  'contacts',
  'services',
  'businesses',
  'email_campaigns',
  'employees',
  'projects',
  'enquiries',
  'purchase_orders',
  'categories',
  'purchase_invoices',
  'tenants',
  'jobapps',
  'blog',
  'secrets',
  'jobs',
  'user_business',
  'tags',
  'bookmarks',
  'launchpad/manage',
  'vat_returns',
  'roles',
  'knowledge_base',
  'gallery',
  'sprints',
  'taxes',
];

describe('All Views Load and CRUD Operations', () => {
  before(() => {
    cy.visit('/');
    cy.get('#email').type('admin@admin.com');
    cy.get('#password').type('%1F0N0qAq%');
  cy.get('form#loginform button[type=submit].btn_1.full_width.text-center').click();
    cy.url().should('not.include', '/login');
  });
  views.forEach((view) => {
    describe(`${view} view`, () => {
      it(`[smoke][views] loads /${view} page`, () => {
        cy.visit(`/${view}`);
        cy.get('h1, h2, [data-cy=page-title]').should('exist');
      });

      it(`[crud][views] can create a new record in /${view}`, () => {
        cy.visit(`/${view}`);
        cy.get('[data-cy=create], .btn-create, .fa-plus').first().click({force:true});
        cy.get('form').should('exist');
        // Fill required fields (example, adjust selectors as needed)
        cy.get('form input, form select, form textarea').first().type('Test Value', {force:true});
        cy.get('form').submit();
        cy.contains('created').should('exist');
      });

      it(`[crud][views] can update a record in /${view}`, () => {
        cy.visit(`/${view}`);
        cy.get('[data-cy=edit], .btn-edit, .fa-edit').first().click({force:true});
        cy.get('form').should('exist');
        cy.get('form input, form select, form textarea').first().clear().type('Updated Value', {force:true});
        cy.get('form').submit();
        cy.contains('updated').should('exist');
      });

      it(`[crud][views] can delete a record in /${view}`, () => {
        cy.visit(`/${view}`);
        cy.get('[data-cy=delete], .btn-delete, .fa-trash').first().click({force:true});
        cy.contains('deleted').should('exist');
      });
    });
  });
});
