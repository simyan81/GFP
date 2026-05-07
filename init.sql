DROP TABLE IF EXISTS type_transaction;
CREATE TABLE type_transaction
(
  id int NOT NULL AUTO_INCREMENT,
  date_ajouter datetime,
  description varchar(255),
  ordre int,
  primary key (id)
);




DROP TABLE IF EXISTS type_depense;
CREATE TABLE type_depense
(
  id int NOT NULL AUTO_INCREMENT 50,
  date_ajouter datetime,
  description varchar(255),
  symbole int,
  ordre int,
  primary key (id)
);




DROP TABLE IF EXISTS type_compte;
CREATE TABLE type_compte
(
  id int NOT NULL AUTO_INCREMENT,
  date_ajouter datetime,
  description varchar(255),
  ordre int,
  primary key (id)
);




DROP TABLE IF EXISTS groupes;
CREATE TABLE groupes
(
  id int NOT NULL AUTO_INCREMENT,
  date_ajouter datetime,
  description varchar(255),
  est_effacer int,
  ordre int,
  primary key (id)
);




DROP TABLE IF EXISTS comptes;
CREATE TABLE comptes
(
  id int NOT NULL AUTO_INCREMENT,
  date_ajouter datetime,
  description varchar(255),
  groupe_id int,
  type_compte_id int,
  methode_calcule_interet double,
  taux_interet decimal(5,2),
  est_effacer int,
  ordre int,
  primary key (id)
);




DROP TABLE IF EXISTS depenses;
CREATE TABLE depenses
(
  id int NOT NULL AUTO_INCREMENT,
  date_ajouter datetime,
  date_depense datetime,
  compte_id int,
  type_transaction_id int,
  type_depense_id int,
  description varchar(255),
  notes varchar(255),
  montant decimal(12, 2), /* type float peux donner des erreur de precission, ex : 1.55 = 1.5499999523162842 */
  /* symbole int, */
  pointer int not null default 0,
  recurrence_id int,
  reconcilier_reference int not null default 0,
  transfert_reference int,
  est_effacer int,
  primary key (id)
);




DROP TABLE IF EXISTS genere; /* Old database */ 
DROP TABLE IF EXISTS recurrence;
CREATE TABLE recurrence
(
  id int NOT NULL AUTO_INCREMENT,
  date_ajouter datetime,
  date_debut datetime,
  date_fin datetime,
  type_interval int,
  interval_valeur int,
  compte_id int,
  type_transaction_id int,
  transfert_compte_id int,
  type_depense_id int,
  description varchar(255),
  notes varchar(255),
  montant decimal(12, 2), /* type float peux donner des erreur de precission, ex : 1.55 = 1.5499999523162842 */
  /* symbole int, */
  auto_type_interval int,
  auto_interval_valeur int,
  date_derniere_depense_ajouter datetime,
  ordre int,
  primary key (id)
);















TRUNCATE type_transaction;
ALTER TABLE type_transaction AUTO_INCREMENT=1;
INSERT INTO type_transaction (id, date_ajouter, description, ordre) VALUES (1, now(), 'Normale', 2);
INSERT INTO type_transaction (id, date_ajouter, description, ordre) VALUES (2, now(), 'Transfert', 4);
INSERT INTO type_transaction (id, date_ajouter, description, ordre) VALUES (3, now(), 'Réconciliation', 6);
INSERT INTO type_transaction (id, date_ajouter, description, ordre) VALUES (4, now(), 'Ajustement', 8);
INSERT INTO type_transaction (id, date_ajouter, description, ordre) VALUES (5, now(), 'Intérêt', 10);




TRUNCATE type_depense;
INSERT INTO `type_depense` (`id`, `date_ajouter`, `description`, `symbole`, `ordre`) VALUES
    (1, now(), 'Autres', 0, 2),
    (2, now(), 'Transfert', 0, 4),
    (3, now(), 'Réconciliation', 0, 6),
    (4, now(), 'Épagne', 0, 8),
    (5, now(), 'Intérêt', 0, 10);
ALTER TABLE type_depense AUTO_INCREMENT=50;
INSERT INTO `type_depense` (, `date_ajouter`, `description`, `symbole`, `ordre`) VALUES
    (now(), 'Paie', 1, 12),
    (now(), 'Épicerie', 0, 14),
    (now(), 'Restaurant', 0, 16),
    (now(), 'Gaz', 0, 18),
    (now(), 'Assurance', 0, 20),
    (now(), 'Véhicule', 0, 22);




TRUNCATE type_compte;
ALTER TABLE type_compte AUTO_INCREMENT=1;
INSERT INTO type_compte (id, date_ajouter, description, ordre) VALUES (1, now(), 'Inconnue', 2);
INSERT INTO type_compte (id, date_ajouter, description, ordre) VALUES (2, now(), 'Débit', 4);
INSERT INTO type_compte (id, date_ajouter, description, ordre) VALUES (3, now(), 'Crédit', 6);
INSERT INTO type_compte (id, date_ajouter, description, ordre) VALUES (4, now(), 'Prêt', 8);
INSERT INTO type_compte (id, date_ajouter, description, ordre) VALUES (5, now(), 'Épagne', 10);




TRUNCATE groupes;
ALTER TABLE groupes AUTO_INCREMENT=0;
INSERT INTO `groupes` (`id`, `date_ajouter`, `description`, `ordre`) VALUES (1, now(), 'Default', 2);
INSERT INTO `groupes` (`id`, `date_ajouter`, `description`, `ordre`) VALUES (2, now(), 'Épagne', 4);




TRUNCATE comptes;
ALTER TABLE comptes AUTO_INCREMENT=0;
INSERT INTO comptes (id, date_ajouter, description, groupe_id, type_compte_id, ordre) VALUES (1, now(), 'Courant', 1 , 1, 2);
INSERT INTO comptes (id, date_ajouter, description, groupe_id, type_compte_id, ordre) VALUES (2, now(), 'Épagne', 2, 4, 4);




TRUNCATE depenses;
ALTER TABLE depenses AUTO_INCREMENT=0;




TRUNCATE recurrence;
ALTER TABLE recurrence AUTO_INCREMENT=0;



