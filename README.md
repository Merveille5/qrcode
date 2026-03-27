DROP TABLE IF EXISTS fonction;

CREATE TABLE fonction (
  id_fonction INT NOT NULL AUTO_INCREMENT,
  libelle VARCHAR(50) NOT NULL,
  PRIMARY KEY (id_fonction)
);

INSERT INTO fonction (id_fonction, libelle) VALUES
(1, 'Directeur'),
(2, 'Agent'),
(3, 'Stagiaire');

DROP TABLE IF EXISTS `agent`;
CREATE TABLE IF NOT EXISTS `agent` (
  `matricule` INT NOT NULL AUTO_INCREMENT,
  `nom` VARCHAR(50) NOT NULL,
  `postnom` VARCHAR(50) NULL,
  `prenom` VARCHAR(50) NULL,
  `sexe` ENUM('M','F')  NOT NULL,
  `id_fonction` INT NOT NULL,
  `regime_prestation` INT NOT NULL,
  PRIMARY KEY (`matricule`),
  KEY `fk_agent_fonction` (`id_fonction`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Déclencheur pour définir automatiquement le régime de prestation
--
DROP TRIGGER IF EXISTS `before_insert_agent`;
DELIMITER $$
CREATE TRIGGER `before_insert_agent`
BEFORE INSERT ON `agent`
FOR EACH ROW
BEGIN
    IF NEW.id_fonction = 1 THEN
        SET NEW.regime_prestation = 30;
    ELSEIF NEW.id_fonction = 2 THEN
        SET NEW.regime_prestation = 26;
    ELSEIF NEW.id_fonction = 3 THEN
        SET NEW.regime_prestation = 22;
    END IF;
END;
$$
DELIMITER ;

DROP TABLE IF EXISTS `conge`;
CREATE TABLE IF NOT EXISTS `conge` (
  `id_conge` INT NOT NULL AUTO_INCREMENT,
  `matricule` INT NOT NULL,
  `type_conge` ENUM('Maladie','Demenagement','Accouchement','Autre')  NOT NULL,
  `date_debut` DATE NOT NULL,
  `date_fin` DATE NOT NULL,
  PRIMARY KEY (`id_conge`),
  KEY `fk_conge_agent` (`matricule`),
  CONSTRAINT chk_conge_dates CHECK (date_fin >= date_debut)
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

DROP TABLE IF EXISTS `jour_ouvert`;
CREATE TABLE IF NOT EXISTS `jour_ouvert` (
  `id_jour` INT NOT NULL AUTO_INCREMENT,
  `date_jour` DATE NOT NULL,
  `est_ouvert` TINYINT(1) NOT NULL DEFAULT 1,
  PRIMARY KEY (`id_jour`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

DROP TABLE IF EXISTS `statut`;
CREATE TABLE IF NOT EXISTS `statut` (
  `id_statut` INT NOT NULL AUTO_INCREMENT,
  `libelle` VARCHAR(50) COLLATE utf8mb4_general_ci NOT NULL,
  PRIMARY KEY (`id_statut`)
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

INSERT INTO `statut` (`id_statut`, `libelle`) VALUES
(1, 'Present'),
(2, 'Absent'),
(3, 'Retard'),
(4, 'Malade');

DROP TABLE IF EXISTS `presence`;
CREATE TABLE IF NOT EXISTS `presence` (
  `id_presence` INT NOT NULL AUTO_INCREMENT,
  `matricule` INT NOT NULL,
  `date_presence` DATE NOT NULL,
  `heure_arrivee` TIME DEFAULT NULL,
  `heure_depart` TIME DEFAULT NULL,
  `id_statut` INT NOT NULL,
  PRIMARY KEY (`id_presence`),
  UNIQUE KEY `unique_presence` (`matricule`,`date_presence`),
  KEY `fk_presence_statut` (`id_statut`),
  KEY `idx_presence_date` (`date_presence`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------
-- Contraintes
-- --------------------------------------------------------

ALTER TABLE `agent`
  ADD CONSTRAINT `fk_agent_fonction` FOREIGN KEY (`id_fonction`) REFERENCES `fonction` (`id_fonction`) ON DELETE RESTRICT ON UPDATE CASCADE;

ALTER TABLE `conge`
  ADD CONSTRAINT `fk_conge_agent` FOREIGN KEY (`matricule`) REFERENCES `agent` (`matricule`) ON DELETE CASCADE ON UPDATE CASCADE;

ALTER TABLE `presence`
  ADD CONSTRAINT `fk_presence_agent` FOREIGN KEY (`matricule`) REFERENCES `agent` (`matricule`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_presence_statut` FOREIGN KEY (`id_statut`) REFERENCES `statut` (`id_statut`) ON DELETE RESTRICT ON UPDATE CASCADE;

COMMIT;

 
