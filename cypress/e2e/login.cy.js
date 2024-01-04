describe(`Workstation login test ${Cypress.env("TARGET_ENV")} environment`, () => {

  let urlStr = 'https://int-my.workstation.co.uk/#/login'
  const targetEnv = Cypress.env("TARGET_ENV");
  if (targetEnv) {
    urlStr = `https://${targetEnv}-my.workstation.co.uk/#/login`
  }

  it('passes', () => {
    cy.visit(urlStr).debug()

    var login_username_str = Cypress.env('login_username')
    var login_password_str = Cypress.env('login_password')

    cy.get('#email').type(login_username_str)
    cy.get('#password').type(login_password_str)
    cy.get('.btn_1').click()
    cy.get(':nth-child(2) > .single_user_pil > .action_btns > .action_btn > .far').click()

    var userNameStr = makeStringOfLength({min:0, max:0})+makelcStringOfLength({min:3, max:5})+' Singh'

    cy.get('#inputName').invoke('val', '')
    cy.get('#inputName').type(userNameStr)
    
    userNameStr = toLowerCaseStr(userNameStr)
    cy.get('#inputEmail').invoke('val', '')
    cy.get('#inputEmail').type(userNameStr+'@yourdomain.com')

    cy.get(':nth-child(1) > .col-md-12 > .form-control').invoke('val', '')
    cy.get(':nth-child(1) > .col-md-12 > .form-control').type('This is a Cypress Test - ' + Date.now())

    //cy.get('#npassword').type('Password1!')
    //cy.get('.npassword > .form-control').type('Password1!')
    // cy.get('#chngpwd > .btn').click()
    cy.get('#userform > .btn').click()
  })


  function makeStringOfLength({min, max}) {

    const length = Math.random() * (max - min + 1) + min
    const characters = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ'; //abcdefghijklmnopqrstuvwxyz0123456789
  
    let result = '';
  
    for (let i = 0; i < length; i++) {
      result += characters.charAt(Math.floor(Math.random() * characters.length));
    }
    return result;
  }
  
  function makelcStringOfLength({min, max}) {

    const length = Math.random() * (max - min + 1) + min
    const characters = 'abcdefghijklmnopqrstuvwxyz';
  
    let result = '';
  
    for (let i = 0; i < length; i++) {
      result += characters.charAt(Math.floor(Math.random() * characters.length));
    }
    return result;
  }

  function toLowerCaseStr(pString) {

    pString = pString.toLowerCase();
    
    return pString;
  }

  
})

