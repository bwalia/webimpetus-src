describe(`Workstation Timeslips test on ${Cypress.env("TARGET_ENV")} environment`, () => {    
    const targetEnv = Cypress.env("TARGET_ENV");
    let urlStr = 'https://int-my.workstation.co.uk/#/login'

    if (targetEnv) {
      urlStr = `https://${targetEnv}-my.workstation.co.uk/#/login`
    }
  
    var login_username_str = Cypress.env('login_username');
    var login_password_str = Cypress.env('login_password');
    var randomString = Cypress.env('epochTime')

    it('Verifying Tasks actions', () => {

    // Login with valid credentials
    cy.visit(urlStr);
    cy.get('#email').clear();
    cy.get('#password').clear(); 
    cy.get('#email').type(login_username_str);
    cy.get('#password').type(login_password_str);
    cy.get('button[type="submit"]').click();    
    cy.get('select[id="uuidBusinessIdSwitcher"]').select("QA Test Workspace", {force: true})
    cy.wait(2000);

    // Open the Timeslips module from menu
    cy.get('a[href="/timeslips"]').click({ multiple: true });
    cy.wait(1000);

    // Creating a new Task
    cy.contains('a', 'Add').click();
    cy.wait(1000);    
    // cy.get('select[id="projects_id"]').select(`Cypress Project ${randomString}`, {force: true});
    // cy.get('select[id="customers_id"]').select(`Cypress Customer ${randomString}`, {force: true});
    // cy.get('select[id="contacts_id"]').select(`Cypress ${randomString}`, {force: true});
    // cy.get('input[id="name"]').type(`Cypress Task ${randomString}`);
    // cy.get('select[id="reported_by"]').select(`Cypress User ${randomString}`, {force: true});
    // cy.get('input[id="start_date"]').type("19/02/2024");
    // cy.get('input[id="end_date"]').type("20/02/2024", {force: true});
    // cy.get('input[id="estimated_hour"]').type("1");
    // cy.get('input[id="rate"]').type("5");
    // cy.get('select[id="status"]').select("open", {force: true});

    // cy.get('select[id="assigned_to"]').select(`Cypress Employee ${randomString}`, {force: true});
    // cy.get('select[id="active"]').select("Active", {force: true});
    // cy.get('select[name="category"]').select("in-progress", {force: true});
    // cy.get('select[name="priority"]').select("Medium", {force: true});
    // cy.get('select[name="sprint_id"]').select(`Cypress Sprint ${randomString}`, {force: true});
    // cy.get('button[type="submit"]').click();    

    // // Editing the Task created by cypress
    // cy.contains('a', 'Tasks').click();
    // cy.scrollTo('right');
    // cy.get(`tr:contains('Cypress Task ${randomString}') div[class="dropdown"]`).click();
    // cy.contains('a', 'Edit').click();
    // cy.get('select[id="active"]').select("Completed", {force: true});
    // cy.contains('button', 'Submit').click();    
    // cy.wait(2000);
    // // Verifying the Task is updated successfully
    // cy.get('div[class="alert alert-success"]').should("contain", "Data updated Successfully!");    
    // cy.get(`tr:contains('Cypress Task ${randomString}')`).should("contain", "Completed");

    // // Verifying the Task status can be updated successfully
    // cy.contains('a', 'Tasks').click();
    // cy.scrollTo('right');
    // cy.get(`tr:contains('Cypress Task ${randomString}') div[class="dropdown"]`).click();
    // cy.contains('a', 'Edit').click();
    // cy.get('select[id="status"]').select("In Review", {force: true});
    // cy.contains('button', 'Submit').click();    
    // cy.wait(2000);
    // // Verifying the Status is updated successfully
    // cy.get('div[class="alert alert-success"]').should("contain", "Data updated Successfully!");    
    // cy.get(`tr:contains('Cypress Task ${randomString}')`).should("contain", "InReview");

    // // Verifying the task clone action
    // cy.contains('a', 'Tasks').click();
    // cy.scrollTo('right');
    // cy.get(`tr:contains('Cypress Task ${randomString}') div[class="dropdown"]`).click();
    // cy.contains('a', 'Clone').click();
    // cy.wait(2000);
    // cy.get('div[class="alert alert-success"]').should("contain", "Data cloned Successfully!");   
    // cy.get('input[id="name"]').type(" 2");
    // cy.contains('button', 'Submit').click();    

    // // Verifying the search filter
    // cy.contains('a', 'Tasks').click();
    // cy.contains('label', 'Search:').type(`${randomString}`)
    // cy.wait(1000)
    // cy.get(`tr:contains('Cypress Task ${randomString}')`).should('be.visible');

    cy.contains('label', 'Search:').type("Walia")
    cy.wait(1000)
    //cy.get(`tr:contains('Cypress Timeslip ${randomString}')`).should('not.be.visible');
    cy.contains('label', 'Search:').clear()  

    })
    
})