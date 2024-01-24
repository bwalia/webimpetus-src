describe(`Workstation Sprints test on ${Cypress.env("TARGET_ENV")} environment`, () => {    
    const targetEnv = Cypress.env("TARGET_ENV");
    let urlStr = 'https://int-my.workstation.co.uk/#/login'

    if (targetEnv) {
      urlStr = `https://${targetEnv}-my.workstation.co.uk/#/login`
    }
  
    var login_username_str = Cypress.env('login_username');
    var login_password_str = Cypress.env('login_password');
  
    it('Verifying Sprints actions', () => {

    // Login with valid credentials
    cy.visit(urlStr);
    cy.get('#email').clear();
    cy.get('#password').clear(); 
    cy.get('#email').type(login_username_str);
    cy.get('#password').type(login_password_str);
    cy.get('button[type="submit"]').click();    
    cy.get('select[id="uuidBusinessIdSwitcher"]').select("QA Test Workspace", {force: true})
    cy.wait(2000);

    // Open the Sprints module from menu
    cy.get('a[href="/sprints"]').click({ multiple: true });
    cy.wait(1000);

    // Creating a new Sprint
    cy.contains('a', 'Add').click();
    cy.wait(1000);    
    cy.get('input[name="sprint_name"]').clear();
    cy.get('input[name="sprint_name"]').type("Cypress Sprint");
    // Verifying that the End date should not be earlier than the Start date.
    cy.get('input[id="start_date"]').type("19/02/2024");
    cy.get('input[id="end_date"]').type("20/02/2023", {force: true});
    cy.get('button[type="submit"]').click();    
    cy.get('span[id="sprintsEndDateErr"]').should("contain", "Sprint end date should be greater than the sprint start date.");    
    // Correcting End date if got the expected EndDateErr
    cy.get('input[id="end_date"]').clear();
    cy.get('input[id="end_date"]').type("20/02/2024");
    // Verifying that the Note field is mandatory.
    cy.get('textarea[name="note"]').clear({force: true});
    cy.get('button[type="submit"]').click();    
    cy.get('div[id="undefined"]').should("contain", "This field is required");    
    // Adding value to Note field
    cy.get('textarea[name="note"]').type("This sprint is created by Cypress");
    cy.get('button[type="submit"]').click();    

    // Editing the Sprint created by cypress
    cy.contains('a', 'Sprints').click();
    cy.scrollTo('right');
    cy.get(`tr:contains('Cypress Sprint') div[class="dropdown"]`).click();
    cy.contains('a', 'Edit').click();
    cy.get('textarea[name="note"]').type(" Updated now");
    cy.contains('button', 'Submit').click();    
    cy.wait(2000);
    // Verifying the Sprint is updated successfully
    cy.get('div[class="alert alert-success"]').should("contain", "Data updated Successfully!");    
    cy.get(`tr:contains('Cypress Sprint')`).should("contain", "Updated");

    // Verifying the search filter
    cy.contains('label', 'Search:').type("cypress")
    cy.wait(1000)
    cy.get(`tr:contains('Cypress Sprint')`).should('be.visible');

    cy.contains('label', 'Search:').type("xyz")
    cy.wait(1000)
    cy.get(`tr:contains('Cypress Sprint')`).should('not.be.visible');
    cy.contains('label', 'Search:').clear()  

    })
    
})