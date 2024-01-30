describe(`Workstation Domains test on ${Cypress.env("TARGET_ENV")} environment`, () => {    
    const targetEnv = Cypress.env("TARGET_ENV");
    let urlStr = 'https://int-my.workstation.co.uk/#/login'

    if (targetEnv) {
      urlStr = `https://${targetEnv}-my.workstation.co.uk/#/login`
    }
  
    var login_username_str = Cypress.env('login_username');
    var login_password_str = Cypress.env('login_password');
    var randomString = Cypress.env('epochTime')

    it('Verifying Domains actions', () => {

    // Login with valid credentials
    cy.visit(urlStr);
    cy.get('#email').clear();
    cy.get('#password').clear(); 
    cy.get('#email').type(login_username_str);
    cy.get('#password').type(login_password_str);
    cy.get('button[type="submit"]').click();    
    cy.get('select[id="uuidBusinessIdSwitcher"]').select("QA Test Workspace", {force: true})
    cy.wait(2000);

    // Open the Domains module from menu
    cy.get('a[href="/domains"]').click({ multiple: true });
    cy.wait(1000);

    // Creating a new Domain
    cy.contains('a', 'Add').click();
    cy.wait(1000);    
    cy.get('select[id="uuid"]').select(`Cypress Customer ${randomString}`, {force: true})
    cy.get('input[id="name"]').type(`www.${randomString}.com`);
    cy.get('textarea[name="notes"]').type("This Domain is created by Cypress");
    cy.get('button[type="submit"]').click();    
    
    // Editing the Domain created by cypress
    cy.contains('a', 'Domains').click();
    cy.scrollTo('right');
    cy.get(`tr:contains('www.${randomString}.com') div[class="dropdown"]`).click();
    cy.contains('a', 'Edit').click();
    cy.get('textarea[name="notes"]').type(" Updated now");
    cy.contains('button', 'Submit').click();    
    cy.wait(2000);
    // Verifying the Domain is updated successfully
    cy.get('div[class="alert alert-success"]').should("contain", "Data updated Successfully!");    
    cy.get(`tr:contains('www.${randomString}.com')`).should("contain", "Updated");

    // Verifying the search filter
    cy.contains('label', 'Search:').type(`${randomString}`)
    cy.wait(1000)
    cy.get(`tr:contains('www.${randomString}.com')`).should('be.visible');

    cy.contains('label', 'Search:').type("xyz")
    cy.wait(1000)
    cy.get(`tr:contains('www.${randomString}.com')`).should('not.be.visible');
    cy.contains('label', 'Search:').clear()  

    })
    
})