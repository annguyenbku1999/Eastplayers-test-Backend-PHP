--
-- Table structure for table `User`
--
CREATE TABLE `Users` (
  `id` MEDIUMINT NOT NULL PRIMARY KEY AUTO_INCREMENT,
  `firstName` VARCHAR(255) CHARSET utf8 NOT NULL,
  `lastName` VARCHAR(255) CHARSET utf8 NOT NULL,
  `username` varchar(20) UNIQUE NOT NULL,
  `email` varchar(30) UNIQUE NOT NULL,
  `password` varchar(255) NOT NULL,
  `dateOfBirth` date NOT NULL,
  `gender` VARCHAR(10) CHARSET utf8 NOT NULL,
  `urlAvatar` varchar(100),
  `token` varchar(60),
  `companyWork` VARCHAR(255) CHARSET utf8,
  `created_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Table structure for table `Project`
--
CREATE TABLE `Projects` (
  `id` MEDIUMINT NOT NULL PRIMARY KEY AUTO_INCREMENT,
  `name` varchar(255) NOT NULL,
  `key` varchar(60) NOT NULL,
  `urlAvatar` varchar(100),
  `created_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Table structure for table `Projects_Users`
--
CREATE TABLE `Projects_Users` (
  `id` MEDIUMINT NOT NULL PRIMARY KEY AUTO_INCREMENT,
  `idProject` MEDIUMINT NOT NULL,
  `idUser` MEDIUMINT NOT NULL,
  `Type`  varchar(20) NOT NULL,
  `created_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Table structure for table `Session`
--
CREATE TABLE `Sessions` (
  `id` MEDIUMINT NOT NULL PRIMARY KEY AUTO_INCREMENT,
  `name` varchar(255) NOT NULL,
  `idProject` MEDIUMINT NOT NULL,
  `created_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Table structure for table `Task`
--
CREATE TABLE `Tasks` (
  `id` MEDIUMINT NOT NULL PRIMARY KEY AUTO_INCREMENT,
  `title` varchar(255) NOT NULL,
  `description` text,
  `idSession` MEDIUMINT NOT NULL,
  `urlAvatar` varchar(100),
  `created_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Table structure for table `Users_Tasks`
--
CREATE TABLE `Users_Tasks` (
  `id` MEDIUMINT NOT NULL PRIMARY KEY AUTO_INCREMENT,
  `idUser` MEDIUMINT NOT NULL,
  `idTask` MEDIUMINT NOT NULL,
  `created_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;



--
-- Add FOREIGN KEY for table `Sessions`
--
ALTER TABLE Sessions 
  ADD CONSTRAINT fk_Project_id
  FOREIGN KEY(idProject) 
  REFERENCES Projects(id);
--
-- Add FOREIGN KEY for table `Tasks`
--
ALTER TABLE Tasks 
  ADD CONSTRAINT fk_Session_id
  FOREIGN KEY(idSession) 
  REFERENCES Sessions(id);

--
-- Add FOREIGN KEY for table `Projects_Users`
--
ALTER TABLE Projects_Users 
  ADD CONSTRAINT fk_Project_id_inProjects_Users
  FOREIGN KEY(idProject) 
  REFERENCES Projects(id)
  ON DELETE CASCADE;

ALTER TABLE Projects_Users 
  ADD CONSTRAINT fk_User_id_inProjects_Users
  FOREIGN KEY(idUser) 
  REFERENCES Users(id)
  ON DELETE CASCADE;

--
-- Add FOREIGN KEY for table `Users_Tasks`
--
ALTER TABLE Users_Tasks 
  ADD CONSTRAINT fk_Task_id_inUsers_Tasks
  FOREIGN KEY(idTask) 
  REFERENCES Tasks(id);

ALTER TABLE Users_Tasks 
  ADD CONSTRAINT fk_User_id_inUsers_Tasks
  FOREIGN KEY(idUser) 
  REFERENCES Users(id);