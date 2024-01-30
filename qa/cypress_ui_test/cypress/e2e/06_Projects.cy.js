describe(`Workstation Projects test on ${Cypress.env("TARGET_ENV")} environment`, () => {    
    const targetEnv = Cypress.env("TARGET_ENV");
    let urlStr = 'https://int-my.workstation.co.uk/#/login'

    if (targetEnv) {
      urlStr = `https://${targetEnv}-my.workstation.co.uk/#/login`
    }
  
    var login_username_str = Cypress.env('login_username');
    var login_password_str = Cypress.env('login_password');
    var randomString = Cypress.env('epochTime')

    it('Verifying Projects actions', () => {

    // Login with valid credentials
    cy.visit(urlStr);
    cy.get('#email').clear();
    cy.get('#password').clear(); 
    cy.get('#email').type(login_username_str);
    cy.get('#password').type(login_password_str);
    cy.get('button[type="submit"]').click();    
    cy.get('select[id="uuidBusinessIdSwitcher"]').select("QA Test Workspace", {force: true})
    cy.wait(2000);

    // Open the Projects module from menu
    cy.get('a[href="/projects"]').click({ multiple: true });
    cy.wait(1000);

    // Creating a new Project
    cy.contains('a', 'Add').click();
    cy.wait(1000);    
    cy.get('select[id="customers_id"]').select( `Cypress Customer ${randomString}` , {force: true})
    cy.get('input[id="name"]').type(`Cypress Project ${randomString}`);
    // Verifying that the Deadline date should not be earlier than the Start date.
    cy.get('input[id="start_date"]').type("19/02/2024");
    cy.get('input[id="deadline_date"]').type("20/02/2023");
    cy.get('button[type="submit"]').click();    
    cy.get('span[id="deadlineError"]').should("contain", "Deadline date should be greater than the start date.");    
    // Correcting deadline date if got the expected deadline-error
    cy.get('input[id="deadline_date"]').clear();
    cy.get('input[id="deadline_date"]').type("20/02/2024");
    cy.get('select[id="currency"]').select("USD", {force: true})
    cy.get('button[type="submit"]').click();    

    // Editing the project created by cypress
    cy.contains('a', 'Projects').click();
    cy.scrollTo('right');
    cy.get(`tr:contains('Cypress Project ${randomString}') div[class="dropdown"]`).click();
    cy.contains('a', 'Edit').click();
    cy.get('select[id="currency"]').select("EUR", {force: true})
    cy.contains('button', 'Submit').click();    
    cy.wait(2000);
    // Verifying the Project is updated successfully
    cy.get('div[class="alert alert-success"]').should("contain", "Data updated Successfully!");    
    cy.get(`tr:contains('Cypress Project ${randomString}')`).should("contain", "EUR");

    // Verifying the project status can be updated successfully
    cy.contains('a', 'Projects').click();
    cy.scrollTo('right');
    cy.get(`tr:contains('Cypress Project ${randomString}') div[class="dropdown"]`).click();
    cy.contains('a', 'Edit').click();
    cy.get('select[id="active"]').select("Completed", {force: true})
    cy.contains('button', 'Submit').click();    
    cy.wait(2000);
    // Verifying the Project is updated successfully
    cy.get('div[class="alert alert-success"]').should("contain", "Data updated Successfully!");    
    cy.get(`tr:contains('Cypress Project ${randomString}')`).should("contain", "Completed");

    // Verifying the search filter
    cy.contains('label', 'Search:').type(`${randomString}`)
    cy.wait(1000)
    cy.get(`tr:contains('Cypress Project ${randomString}')`).should('be.visible');

    cy.contains('label', 'Search:').type("xyz")
    cy.wait(1000)
    cy.get(`tr:contains('Cypress Project ${randomString}')`).should('not.be.visible');
    cy.contains('label', 'Search:').clear()  

    })
    
})