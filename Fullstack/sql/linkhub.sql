-- phpMyAdmin SQL Dump
-- version 5.2.0
-- https://www.phpmyadmin.net/
--
-- Hôte : 127.0.0.1
-- Généré le : ven. 26 mai 2023 à 10:15
-- Version du serveur : 10.4.27-MariaDB
-- Version de PHP : 8.2.0

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Base de données : `linkhub`
--
CREATE DATABASE IF NOT EXISTS `linkhub` DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci;
USE `linkhub`;

-- --------------------------------------------------------

--
-- Structure de la table `comments`
--

DROP TABLE IF EXISTS `comments`;
CREATE TABLE `comments` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `publication_id` int(11) NOT NULL,
  `parent_id` int(11) DEFAULT NULL,
  `content` text NOT NULL,
  `creation_date` date NOT NULL DEFAULT current_timestamp(),
  `who_comment` varchar(255) NOT NULL,
  `who_comment_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;

-- --------------------------------------------------------

--
-- Structure de la table `friend_list`
--

DROP TABLE IF EXISTS `friend_list`;
CREATE TABLE `friend_list` (
  `id` int(11) NOT NULL,
  `user_id1` int(11) NOT NULL,
  `user_id2` int(11) NOT NULL,
  `status` varchar(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Déchargement des données de la table `friend_list`
--

INSERT INTO `friend_list` (`id`, `user_id1`, `user_id2`, `status`) VALUES
(1, 1, 2, 'Friend'),
(2, 1, 3, 'Waiting'),
(3, 2, 1, 'Friend');

-- --------------------------------------------------------
-- Structure de la table `group`
--

CREATE TABLE `group` (
  `id` int(11) NOT NULL,
  `group_name` varchar(50) DEFAULT NULL,
  `event` varchar(50) DEFAULT NULL,
  `status` varchar(50) DEFAULT NULL,
  `description` varchar(500) DEFAULT NULL,
  `banner` varchar(50) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;

--
-- Déchargement des données de la table `group`
--

INSERT INTO `group` (`id`, `group_name`, `event`, `status`, `description`, `banner`) VALUES
(1, 'Hetic', 'meet-up', 'public', 'Une école d&#039;informatique trop incroyable ', 'banniere_par_defaut.png'),
(2, 'Facebook', 'meet-up', 'public', 'Facebook est un réseau social créé le 4 février 2004 par l\'Américain Mark Zuckerberg. Originellement réservé aux étudiants des grandes universités américaines (Harvard, puis Stanford, Columbia et Yale), il est ouvert au monde entier depuis septembre 2006.', 'banniere_par_defaut.png'),
(3, 'Sephora', 'meet-up', 'public', 'Le nom Sephora vient du personnage biblique Séphora, épouse de Moïse. Le groupe Nouvelles Galeries ouvre le premier magasin Sephora à Paris, en 1973. Ce magasin sera racheté par le groupe britannique BOOTS, qui ouvre 38 magasins en France.', 'banniere_par_defaut.png'),
(4, 'Pk', 'meet-up', 'private', 'Hautes études des technologies de l', 'banniere_par_defaut.png'),
(5, 'Chichi', 'meet-up', 'public', 'Chichi tres beau', 'banniere_par_defaut.png');

--
-- Index pour les tables déchargées
--

--
-- Index pour la table `group`
--
ALTER TABLE `group`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT pour les tables déchargées
--

--
-- AUTO_INCREMENT pour la table `group`
--
ALTER TABLE `group`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;

-- --------------------------------------------------------

--
-- Structure de la table `images`
--

DROP TABLE IF EXISTS `images`;
CREATE TABLE `images` (
  `id` int(11) NOT NULL,
  `name` varchar(255) NOT NULL,
  `type` varchar(255) NOT NULL,
  `size` int(11) NOT NULL,
  `path` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Structure de la table `members_group`
--

DROP TABLE IF EXISTS `members_group`;
CREATE TABLE `members_group` (
  `id` int(11) NOT NULL,
  `name` varchar(255) NOT NULL,
  `role` varchar(255) NOT NULL,
  `join_date` date NOT NULL DEFAULT current_timestamp(),
  `user_id` int(11) NOT NULL,
  `group_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;

--
-- Déchargement des données de la table `members_group`
--

INSERT INTO `members_group` (`id`, `name`, `role`, `join_date`, `user_id`, `group_id`) VALUES
(1, 'Alexis', 'Admin', '2023-05-12', 1, 4),
(2, 'Christelle', 'Admin', '2022-08-20', 5, 1),
(3, 'Hemmy', 'Admin', '2022-06-16', 3, 3),
(4, 'Luc', 'Admin', '2019-01-17', 4, 2),
(5, 'Alexis', 'Membre', '2023-05-15', 1, 1),
(6, 'Alexis', 'Membre', '2022-05-05', 1, 3),
(7, 'Hemmy', 'Membre', '2021-05-16', 3, 1),
(8, 'Hemmy', 'Membre', '2022-06-16', 3, 2),
(9, 'Hemmy', 'Membre', '2022-05-10', 3, 4);

-- --------------------------------------------------------

--
-- Structure de la table `messages`
--

DROP TABLE IF EXISTS `messages`;
CREATE TABLE `messages` (
  `id` int(11) NOT NULL,
  `content` text DEFAULT NULL,
  `expediteur_name` varchar(11) DEFAULT NULL,
  `expediteur_id` int(11) NOT NULL,
  `session_id` int(11) DEFAULT NULL,
  `date_envoi` datetime DEFAULT current_timestamp(),
  `modify_or_not` varchar(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;

--
-- Déchargement des données de la table `messages`
--

INSERT INTO `messages` (`id`, `content`, `expediteur_name`, `expediteur_id`, `session_id`, `date_envoi`, `modify_or_not`) VALUES
(1, "Salut ! Je viens de rentrer de mes vacances à la plage et c\'était incroyable ! Le sable chaud, les vagues et le soleil étaient parfaits. J\'ai passé la plupart de mes journées à me détendre sur la plage et à nager dans l\'océan.", 'Alexis', 1, 6, '2023-05-28 13:21:06', ''),
(2, "Salut ! Ça a l\'air génial ! Tu as fait d\'autres activités pendant tes vacances ?", 'Hemmy', 3, 6, '2023-05-28 13:21:14', ''),
(3, "Oui, j\'ai également fait de la plongée en apnée dans un récif corallien. C\'était comme nager dans un aquarium géant rempli de poissons colorés et de coraux magnifiques. J\'ai même eu la chance de voir une tortue de mer nager juste à côté de moi.", 'Alexis', 1, 6, '2023-05-28 13:21:20', ''),
(4, "Waouh, c\'est incroyable ! La plongée en apnée est sur ma liste de choses à faire. Tu recommandes un endroit en particulier pour le faire ?", 'Hemmy', 3, 6, '2023-05-28 13:21:25', ''),
(5, "Absolument ! J\'étais à Bali, en Indonésie, et les sites de plongée là-bas sont vraiment spectaculaires. Les eaux sont claires et regorgent de vie marine. Tu devrais certainement le mettre sur ta liste !", 'Alexis', 1, 6, '2023-05-28 13:21:29', ''),
(6, "Lol, vas-y. La prochaine fois, je viens avec toi", 'Hemmy', 3, 6, '2023-05-28 13:21:50', '');

--
-- Structure de la table `message_session`
--
DROP TABLE IF EXISTS `message_session`;
CREATE TABLE `message_session` (
  `id` int(11) NOT NULL,
  `type_discussion` varchar(11) NOT NULL,
  `type_id` int(11) NOT NULL,
  `type_id2` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Déchargement des données de la table `message_session`
--

INSERT INTO `message_session` (`id`, `type_discussion`, `type_id`, `type_id2`) VALUES
(1, 'private', 1, 3);

-- --------------------------------------------------------

--
-- Structure de la table `notifications`
--

DROP TABLE IF EXISTS `notifications`;
CREATE TABLE `notifications` (
  `id` int(11) NOT NULL,
  `from_type` varchar(11) NOT NULL,
  `from_type_id` int(11) NOT NULL,
  `notification_type` varchar(11) NOT NULL,
  `notif_type_id` int(11) NOT NULL,
  `receive_date` datetime NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Déchargement des données de la table `notifications`
--

INSERT INTO `notifications` (`id`, `from_type`, `from_type_id`, `notification_type`, `notif_type_id`, `receive_date`) VALUES
(1, 'user', 4, 'group_req', 4, '2023-05-25 23:04:51');

-- --------------------------------------------------------

--
-- Structure de la table `page`
--

DROP TABLE IF EXISTS `page`;
CREATE TABLE `page` (
  `id` int(11) NOT NULL,
  `page_name` varchar(255) NOT NULL,
  `page_bio` varchar(255) NOT NULL,
  `page_logo` text NOT NULL,
  `category_page` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;

--
-- Déchargement des données de la table `page`
--

INSERT INTO `page` (`id`, `page_name`, `page_bio`, `page_logo`, `category_page`) VALUES
(1, 'Moka Moka Cafe', 'Une étincelle dans votre vie', 'logo_moka_moka.webp', 'Entreprise');

-- --------------------------------------------------------

--
-- Structure de la table `publications`
--

DROP TABLE IF EXISTS `publications`;
CREATE TABLE `publications` (
  `id` int(11) NOT NULL,
  `content` text NOT NULL,
  `creation_date` datetime NOT NULL DEFAULT current_timestamp(),
  `who_typed` varchar(255) NOT NULL,
  `who_typed_id` varchar(255) NOT NULL,
  `title` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;

-- --------------------------------------------------------

--
-- Structure de la table `reactions`
--

DROP TABLE IF EXISTS `reactions`;
CREATE TABLE `reactions` (
  `id` int(11) NOT NULL,
  `user_id` int(10) NOT NULL,
  `publication_id` int(10) DEFAULT NULL,
  `comment_id` int(10) DEFAULT NULL,
  `type` varchar(19) NOT NULL,
  `emoji` varchar(49) NOT NULL,
  `creation_date` datetime NOT NULL DEFAULT current_timestamp(),
  `who_react` varchar(255) NOT NULL,
  `who_react_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;

-- --------------------------------------------------------

--
-- Structure de la table `users`
--

DROP TABLE IF EXISTS `users`;
CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `name` varchar(255) NOT NULL,
  `first_name` varchar(255) NOT NULL,
  `mail` varchar(255) NOT NULL,
  `password` varchar(255) NOT NULL,
  `phone` varchar(255) NOT NULL,
  `gender` varchar(255) NOT NULL,
  `roles` varchar(255) NOT NULL,
  `creation_date` date NOT NULL,
  `avatar` varchar(255) NOT NULL,
  `banner` varchar(255) NOT NULL,
  `birthday` date DEFAULT NULL,
  `status` enum('active','inactive') NOT NULL DEFAULT 'active'
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;

--
-- Déchargement des données de la table `users`
--

INSERT INTO `users` (`id`, `name`, `first_name`, `mail`, `password`, `phone`, `gender`, `roles`, `creation_date`, `avatar`, `banner`, `birthday`, `status`) VALUES
(1, 'Lin', 'Alexis', 'Alexis_L@gmail.com', '$2y$10$DK1ygUOcpNT5f6oqkRWQzuIoHLeQ2E5G7A26M.XJ1AMTEAgMcPFoC', '0616322245', 'Homme', 'Admin', '2023-05-10', '64706c45341948.59851099.jpg', '64706c45351610.37825595.jpg', '2013-05-16', 'active'),
(2, 'Kalloga', 'Sira', 'Sira_K@gmail.com', '$2y$10$DK1ygUOcpNT5f6oqkRWQzuIoHLeQ2E5G7A26M.XJ1AMTEAgMcPFoC', '0723738392', 'Femme', 'Client', '2023-05-11', '64706dc5510ad7.25783368.jpg', '64706c45351610.37825595.jpg', '2013-05-23', 'active'),
(3, 'Mathys', 'Hemmy', 'Hemmy_M@gmail.com', '$2y$10$DK1ygUOcpNT5f6oqkRWQzuIoHLeQ2E5G7A26M.XJ1AMTEAgMcPFoC', '0723738772', 'Femme', 'Client', '2023-05-10', '64706dc1a691c9.29354005.jpg', '64706c45351610.37825595.jpg', '2021-02-09', 'active'),
(4, 'Lu', 'Luc', 'Luc_L@gmail.com', '$2y$10$DK1ygUOcpNT5f6oqkRWQzuIoHLeQ2E5G7A26M.XJ1AMTEAgMcPFoC', '07 66 36 87 25', 'Homme', 'Client', '2023-05-12', '64706e56006723.35485500.jpg', '64706e55f32426.77551326.png', '2014-08-14', 'active'),
(5, 'Carvalho', 'Christelle', 'Christelle_C@gmail.com', '$2y$10$DK1ygUOcpNT5f6oqkRWQzuIoHLeQ2E5G7A26M.XJ1AMTEAgMcPFoC', '07 66 36 87 25', 'Femme', 'Client', '2023-08-21', '64706dc8b93da1.99878953.png', '64706c45351610.37825595.jpg', '2016-01-29', 'active');
-- --------------------------------------------------------

--
-- Structure de la table `user_group`
--

DROP TABLE IF EXISTS `user_group`;
CREATE TABLE `user_group` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `group_id` int(11) NOT NULL,
  `status` varchar(50) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;

--
-- Déchargement des données de la table `user_group`
--

INSERT INTO `user_group` (`id`, `user_id`, `group_id`, `status`) VALUES
(1, 1, 1, 'private'),
(2, 2, 2, 'public');

--
-- Index pour les tables déchargées
--

--
-- Index pour la table `comments`
--
ALTER TABLE `comments`
  ADD PRIMARY KEY (`id`);

--
-- Index pour la table `friend_list`
--
ALTER TABLE `friend_list`
  ADD PRIMARY KEY (`id`);

--
-- Index pour la table `group`
--
ALTER TABLE `group`
  ADD PRIMARY KEY (`id`);

--
-- Index pour la table `images`
--
ALTER TABLE `images`
  ADD PRIMARY KEY (`id`);

--
-- Index pour la table `members_group`
--
ALTER TABLE `members_group`
  ADD PRIMARY KEY (`id`);

--
-- Index pour la table `messages`
--
ALTER TABLE `messages`
  ADD PRIMARY KEY (`id`);

--
-- Index pour la table `message_session`
--
ALTER TABLE `message_session`
  ADD PRIMARY KEY (`id`);

--
-- Index pour la table `notifications`
--
ALTER TABLE `notifications`
  ADD PRIMARY KEY (`id`);

--
-- Index pour la table `page`
--
ALTER TABLE `page`
  ADD PRIMARY KEY (`id`);

--
-- Index pour la table `publications`
--
ALTER TABLE `publications`
  ADD PRIMARY KEY (`id`);

--
-- Index pour la table `reactions`
--
ALTER TABLE `reactions`
  ADD PRIMARY KEY (`id`);

--
-- Index pour la table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`);

--
-- Index pour la table `user_group`
--
ALTER TABLE `user_group`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT pour les tables déchargées
--

--
-- AUTO_INCREMENT pour la table `comments`
--
ALTER TABLE `comments`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=22;

--
-- AUTO_INCREMENT pour la table `friend_list`
--
ALTER TABLE `friend_list`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT pour la table `group`
--
ALTER TABLE `group`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT pour la table `images`
--
ALTER TABLE `images`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT pour la table `members_group`
--
ALTER TABLE `members_group`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;

--
-- AUTO_INCREMENT pour la table `messages`
--
ALTER TABLE `messages`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT pour la table `message_session`
--
ALTER TABLE `message_session`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=18;

--
-- AUTO_INCREMENT pour la table `notifications`
--
ALTER TABLE `notifications`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;

--
-- AUTO_INCREMENT pour la table `page`
--
ALTER TABLE `page`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT pour la table `publications`
--
ALTER TABLE `publications`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=60;

--
-- AUTO_INCREMENT pour la table `reactions`
--
ALTER TABLE `reactions`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=54;

--
-- AUTO_INCREMENT pour la table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT pour la table `user_group`
--
ALTER TABLE `user_group`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
