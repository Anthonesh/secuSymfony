describe('template spec', () => {
  it('ajouter un utilisateur', () => {
    cy.visit('https://securite.adrardev.fr/');

    cy.get('#register_firstName').type('Antho');
    cy.get('#register_name').type('nesh');
    cy.get('#register_email').type('anthony.dev@gmail.com');
    cy.get('#register_password_first').type('123456');
    cy.get('#register_password_second').type('123456');
    cy.get('button[type="submit"]').click();

    cy.get('strong').invoke("text").then((text) => {
      if (text === "L'utilisateur existe déjà !") {
        cy.log('L\'utilisateur existe déjà');
      } else {
        cy.log('Le compte : anthony.dev@gmail.com a été ajouté en BDD');
      }
    });
  });

  it('Mettre à jour l\'utilisateur avec succès', () => {
    cy.visit('https://securite.adrardev.fr/register/update/4');

    cy.get('#register_firstName').clear().type('newFirstName');
    cy.get('#register_name').clear().type('newLastName');
    cy.get('#register_email').clear().type('new.email@example.com');
    cy.get('#register_password_first').clear().type('newpassword');
    cy.get('#register_password_second').clear().type('newpassword');
    cy.get('button[type="submit"]').click();

    cy.get('strong').invoke("text").then((text) => {
      if (text === 'Le compte a été mis à jour') {
        cy.log('Le compte a été mis à jour avec succès');
      } else {
        cy.log('Le compte n\'existe pas');
      }
    });
  });
});

describe('Envoi d\'un test', () => {
  it('Envoi d\'un test vers le serveur', () => {
    cy.request({
      method: 'POST',
      url: 'https://securite.adrardev.fr/tests',
      body: {
        title: 'Mise à jour',
        date: '2023-11-06',
        statut: 'Succès'
      },
      failOnStatusCode: false,
    }).then((response) => {
      if (response.status === 200) {
        cy.log('Le test a été enregistré avec succès');
      } else {
        cy.log('Le compte n\'existe pas');
      }
    });
  });
});