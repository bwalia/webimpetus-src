describe(`Workstation Secret test on ${Cypress.env("TARGET_ENV")} environment`, () => {    
    const targetEnv = Cypress.env("TARGET_ENV");
    let urlStr = 'https://int-my.workstation.co.uk/#/login'

    if (targetEnv) {
      urlStr = `https://${targetEnv}-my.workstation.co.uk/#/login`
    }
  
    var login_username_str = Cypress.env('login_username');
    var login_password_str = Cypress.env('login_password');
    var randomString = Cypress.env('epochTime');

    it('Verifying Secret actions', () => {

    // Login with valid credentials
    cy.visit(urlStr);
    cy.get('#email').clear();
    cy.get('#password').clear(); 
    cy.get('#email').type(login_username_str);
    cy.get('#password').type(login_password_str);
    cy.get('button[type="submit"]').click();    
    cy.get('select[id="uuidBusinessIdSwitcher"]').select("QA Test Workspace", {force: true})
    cy.wait(2000);

    // Open the Secrets module from menu
    cy.get('a[href="/secrets"]').click({ multiple: true });
    cy.wait(1000);

    // Creating a new Secret
    cy.contains('a', 'Add').click();
    cy.wait(1000);    
    cy.get('input[id="title"]').type(`Cypress ${randomString}`);
    cy.get('textarea[name="key_value"]').type("Cypress secret value");
    cy.get('button[type="submit"]').click();    
    
    // Editing the Secret created by cypress
    cy.contains('a', 'Secrets').click();
    cy.scrollTo('right');
    cy.get(`tr:contains('Cypress ${randomString}') div[class="dropdown"]`).click();
    cy.contains('a', 'Edit').click(); 
    cy.get('input[id="title"]').type('{selectall}{backspace}');
    cy.get('input[id="title"]').type(`Cypress Secret ${randomString}`);

    cy.contains('button', 'Submit').click();    
    cy.wait(2000);
    // Verifying the Secret is updated successfully
    cy.get('div[class="alert alert-success"]').should("contain", "Data updated Successfully!");    
    cy.get(`tr:contains('Cypress Secret ${randomString}')`).should('be.visible');

    // Verifying the search filter
    cy.contains('label', 'Search:').type(`${randomString}`)
    cy.wait(1000)
    cy.get(`tr:contains('Cypress Secret ${randomString}')`).should('be.visible');

    cy.contains('label', 'Search:').type("xyz")
    cy.wait(1000)
    cy.get(`tr:contains('Cypress Secret ${randomString}')`).should('not.be.visible');
    cy.contains('label', 'Search:').clear()  

    })
    
})