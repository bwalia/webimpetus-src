describe(`Workstation Clean Test data on ${Cypress.env("TARGET_ENV")} environment`, () => {    
    const targetEnv = Cypress.env("TARGET_ENV");
    let urlStr = 'https://int-my.workstation.co.uk/#/login'

    if (targetEnv) {
      urlStr = `https://${targetEnv}-my.workstation.co.uk/#/login`
    }
  
    var login_username_str = Cypress.env('login_username');
    var login_password_str = Cypress.env('login_password');
    var randomString = Cypress.env('epochTime')

    it('Cleaning Cypress Test data', () => {

    // Login with valid credentials
    cy.visit(urlStr);
    cy.get('#email').clear();
    cy.get('#password').clear(); 
    cy.get('#email').type(login_username_str);
    cy.get('#password').type(login_password_str);
    cy.get('button[type="submit"]').click();    
    cy.get('select[id="uuidBusinessIdSwitcher"]').select("QA Test Workspace", {force: true})
    cy.wait(2000);

    // Deleting the User created by cypress
    cy.contains('a', 'Users').click();
    cy.get(`tr:contains('Cypress User ${randomString}') div[class="dropdown"]`).click();
    cy.contains('a', 'Delete').click();
    cy.on('window:confirm', (str) => {expect(str).to.equal('Are you sure want to delete?')});
    cy.on('window:confirm', () => true);
    cy.wait(2000);
    cy.get('div[class="alert alert-success"]').should("contain", "Data deleted Successfully!");    

    // Deleting the Employee created by cypress
    cy.contains('a', 'Employees').click();
    cy.wait(1000)
    cy.get(`tr:contains('Cypress Employee ${randomString}') div[class="dropdown"]`).click();
    cy.contains('a', 'Delete').click();
    cy.on('window:confirm', (str) => {expect(str).to.equal('Are you sure want to delete?')});
    cy.on('window:confirm', () => true);
    cy.wait(2000);
    cy.get('div[class="alert alert-success"]').should("contain", "Data deleted Successfully!");   
    
    // Deleting the Contact created by cypress
    cy.contains('a', 'Contacts').click();
    cy.get(`tr:contains('Cypress ${randomString}') div[class="dropdown"]`).click();
    cy.contains('a', 'Delete').click();
    cy.on('window:confirm', (str) => {expect(str).to.equal('Are you sure want to delete?')});
    cy.on('window:confirm', () => true);
    cy.wait(2000);
    cy.get('div[class="alert alert-success"]').should("contain", "Data deleted Successfully!");    

    // Deleting the Customer created by cypress
    cy.contains('a', 'Customers').click();
    cy.get(`tr:contains('Cypress Customer ${randomString}') div[class="dropdown"]`).click();
    cy.contains('a', 'Delete').click();
    cy.on('window:confirm', (str) => {expect(str).to.equal('Are you sure want to delete?')});
    cy.on('window:confirm', () => true);
    cy.wait(2000);
    cy.get('div[class="alert alert-success"]').should("contain", "Data deleted Successfully!");    
    
    // Deleting the Project created by cypress
    cy.contains('a', 'Projects').click();
    cy.wait(1000)
    cy.get(`tr:contains('Cypress Project ${randomString}') div[class="dropdown"]`).click();
    cy.contains('a', 'Delete').click();
    cy.on('window:confirm', (str) => {expect(str).to.equal('Are you sure want to delete?')});
    cy.on('window:confirm', () => true);
    cy.wait(2000);
    cy.get('div[class="alert alert-success"]').should("contain", "Data deleted Successfully!");    

    // Deleting the Sprint created by cypress
    cy.contains('a', 'Sprints').click();
    cy.wait(1000)
    cy.get(`tr:contains('Cypress Sprint ${randomString}') div[class="dropdown"]`).click();
    cy.contains('a', 'Delete').click();
    cy.on('window:confirm', (str) => {expect(str).to.equal('Are you sure want to delete?')});
    cy.on('window:confirm', () => true);
    cy.wait(2000);
    cy.get('div[class="alert alert-success"]').should("contain", "Data deleted Successfully!");  
    
    
    // Deleting the Category created by cypress
    cy.contains('a', 'Categories').click();
    cy.wait(1000)
    cy.get(`tr:contains('Cypress Category ${randomString}') div[class="dropdown"]`).click();
    cy.contains('a', 'Delete').click();
    cy.on('window:confirm', (str) => {expect(str).to.equal('Are you sure want to delete?')});
    cy.on('window:confirm', () => true);
    cy.wait(2000);
    cy.get('div[class="alert alert-success"]').should("contain", "Data deleted Successfully!");  


    // Deleting the Task cloned by cypress
    cy.contains('a', 'Tasks').click();
    cy.wait(1000)
    cy.get(`tr:contains('Cypress Task 2 ${randomString}') div[class="dropdown"]`).click();
    cy.contains('a', 'Delete').click();
    cy.on('window:confirm', (str) => {expect(str).to.equal('Are you sure want to delete?')});
    cy.on('window:confirm', () => true);
    cy.wait(2000);
    cy.get('div[class="alert alert-success"]').should("contain", "Data deleted Successfully!");   

    // Deleting the Tasks created by cypress
    cy.contains('a', 'Tasks').click();
    cy.wait(1000)
    cy.get(`tr:contains('Cypress Task ${randomString}') div[class="dropdown"]`).click();
    cy.contains('a', 'Delete').click();
    cy.on('window:confirm', (str) => {expect(str).to.equal('Are you sure want to delete?')});
    cy.on('window:confirm', () => true);
    cy.wait(2000);
    cy.get('div[class="alert alert-success"]').should("contain", "Data deleted Successfully!");  


    // Deleting the Enquiry created by cypress
    cy.contains('a', 'Enquiries').click();
    cy.wait(1000)
    cy.get(`tr:contains('${randomString}') div[class="dropdown"]`).click();
    cy.contains('a', 'Delete').click();
    cy.on('window:confirm', (str) => {expect(str).to.equal('Are you sure want to delete?')});
    cy.on('window:confirm', () => true);
    cy.wait(2000);
    cy.get('div[class="alert alert-success"]').should("contain", "Data deleted Successfully!");  

    // Deleting the Domain created by cypress
    cy.contains('a', 'Domains').click();
    cy.wait(1000)
    cy.get(`tr:contains('www.${randomString}.com') div[class="dropdown"]`).click();
    cy.contains('a', 'Delete').click();
    cy.on('window:confirm', (str) => {expect(str).to.equal('Are you sure want to delete?')});
    cy.on('window:confirm', () => true);
    cy.wait(2000);
    cy.get('div[class="alert alert-success"]').should("contain", "Data deleted Successfully!");    
    })
    
})