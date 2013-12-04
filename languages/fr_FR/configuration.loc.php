<?php

 /*
 *    phpMumbleAdmin (PMA), web php administration tool for murmur ( mumble server daemon ).
 *    Copyright (C) 2010 - 2013  Dadon David. PMA@ipnoz.net
 *
 *    This program is free software: you can redistribute it and/or modify
 *    it under the terms of the GNU General Public License as published by
 *    the Free Software Foundation, either version 3 of the License, or
 *    (at your option) any later version.
 *
 *    This program is distributed in the hope that it will be useful,
 *    but WITHOUT ANY WARRANTY; without even the implied warranty of
 *    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 *    GNU General Public License for more details.
 *
 *    You should have received a copy of the GNU General Public License
 *    along with this program.  If not, see <http://www.gnu.org/licenses/>.
 */

$TEXT['tab_options'] = 'Options';
$TEXT['tab_ICE'] = 'ICE';
$TEXT['tab_settings'] = 'Paramètres';
$TEXT['tab_debug'] = 'Débogueur';

// Tab options
$TEXT['select_lang'] = 'Selectionnez une langue';
$TEXT['select_style'] = 'Selectionnez un style';
$TEXT['select_time'] = 'Selectionnez l\'heure locale';
$TEXT['time_format'] = 'Format de l\'heure';
$TEXT['date_format'] = 'Format de la date';
$TEXT['select_locales_profile'] = 'Selectionnez un profile de localisation';
$TEXT['uptime_format'] = 'Format l\'uptime';
$TEXT['conn_login'] = 'Pseudo de connection';
$TEXT['conn_login_info'] = 'Cette option vous permet de choisir le pseudo avec lequel vous voulez vous connecter aux serveurs';

$TEXT['default_options'] =  'Valeurs des options par défaut';
$TEXT['default_lang'] = 'Language par défaut';
$TEXT['default_style'] = 'Style par défaut';
$TEXT['default_time'] = 'Heure locale par défaut';
$TEXT['default_time_format'] = 'Format de l\'heure par défaut';
$TEXT['default_date_format'] = 'Format de la date par défaut';
$TEXT['default_locales'] = 'Informations de localisation par défaut';
$TEXT['add_locales_profile'] = 'Ajouter un profile d\'informations de localisation';
$TEXT['del_locales_profile'] = 'Supprimer un profile d\'informations de localisation';

$TEXT['sa_login'] = 'Pseudo SuperAdmin';
$TEXT['change_your_pw'] = 'Changez votre mot de passe';
$TEXT['enter_your_pw'] = 'Entrez votre mot de passe';

// Tab ICE
$TEXT['profile_name'] = 'Nom du profile';
$TEXT['ICE_host'] = 'Hôte de l\'interface ICE';
$TEXT['ICE_port'] = 'Port de l\'interface ICE';
$TEXT['ICE_timeout'] = 'Timeout en secondes';
$TEXT['ICE_secret'] = 'Mot de passe ICE';
$TEXT['slice_profile'] = 'Profile slice';
$TEXT['slice_php_file'] = 'Fichier slice-php';
$TEXT['conn_url'] = 'URL de connexion';
$TEXT['conn_url_info'] = 'PMA permet de se connecter à un serveur virtuel grâce à l\'IP renseigné avec le paramètre "host". Ce paramètre-ci permet de le remplacer par un nom de domaine ou une IP.';
$TEXT['public_profile'] = 'Profile publique';
$TEXT['default_ICE_profile'] = 'Définir en tant que profile ICE par défaut';
$TEXT['add_ICE_profile'] = 'Ajouter un profile ICE';
$TEXT['del_profile'] = 'Supprimer le profile';
$TEXT['confirm_del_ICE_profile'] = 'Confirmez-vous la suppression du profile ICE ?';
$TEXT['enable_profile'] = 'Cliquez pour activer le profile';

// Tab settings
$TEXT['mumble_accounts'] = 'Comptes mumble';

$TEXT['disable_function'] = '0 désactive cette fonction';

$TEXT['site_title'] = 'Titre du site web';
$TEXT['site_desc'] = 'Description du site web';
$TEXT['autologout'] = 'Auto-déconnexion en minutes ( 5 - 30 )';
$TEXT['autocheck_update'] = 'Vérification automatique de mise à jour';
$TEXT['autocheck_update_info'] = 'en jours: 0 - 31<br>0 désactive cette fonction';
$TEXT['check_update'] = 'Rechercher des mises à jours';
$TEXT['inc_murmur_vers'] = 'URL de connexion avec la version du démon murmur';
$TEXT['inc_murmur_vers_info'] = 'Un client mumble de version inferieur ne pourra pas se connecter aux serveurs via l\'url de connexion';

$TEXT['show_avatar'] = 'Afficher les avatars seulement aux SuperAdmins';

$TEXT['activate_su_login'] = 'Autoriser les SuperUsers à se connecter à PMA';
$TEXT['activate_su_modify_pw'] = 'Autoriser les SuperUsers à changer le mot de passe des utilisateurs enregistrés';
$TEXT['activate_su_vserver_start'] = 'Autoriser les SuperUsers à démarrer / arrêter le serveur virtuel';
$TEXT['activate_su_ru'] = 'Activer la classe SuperUser_ru';
$TEXT['activate_su_ru_info'] = 'Donner les droits SuperUser à certains utilisateurs enregistrés et sous certaines conditions ( Voir le README.txt )';
$TEXT['reg_users'] = 'Utilisateurs enregistrés';
$TEXT['activate_ru_login'] = 'Autoriser les utilisateurs enregistrés à se connecter à PMA';
$TEXT['activate_ru_del_account'] = 'Autoriser les utilisateurs enregistrés à supprimer leur compte';
$TEXT['activate_ru_modify_login'] = 'Autoriser les utilisateurs enregistrés à modifier leur pseudo';

$TEXT['vservers_logs'] = 'Logs des serveurs virtuels';
$TEXT['srv_logs_amount'] = 'Nombre de logs que PMA doit obtenir des serveurs';
$TEXT['activate_vservers_logs_for_adm'] = 'Activer l\'onglet des logs pour les admins et les SuperUsers';
$TEXT['activate_adm_highlight_logs'] = 'Autoriser les admins et les SuperUsers à surligner les logs';

$TEXT['pma_logs'] = 'Logs PMA';
$TEXT['pma_logs_infos'] = 'Options disponible au SuperAdmin seulement';
$TEXT['logs_sa_actions'] = 'Logguer les actions du SuperAdmin ( RootAdmin exclus )';
$TEXT['pma_logs_clean'] = 'Nettoyer les logs anciens ( en jours )';

$TEXT['tables'] = 'Tables';
$TEXT['overview_table_lines'] = 'Nombre de lignes pour la table des serveurs';
$TEXT['users_table_lines'] = 'Nombre de lignes pour la table des utilisateurs enrigistrés';
$TEXT['ban_table_lines'] = 'Nombre de lignes pour la table des bans';
$TEXT['tables_infos'] = '10 - 1000 ( 0 désactive la pagination )';

$TEXT['overview_table'] = 'Table des serveurs';
$TEXT['enable_users_total'] = 'Afficher le total des utilisateurs';
$TEXT['enable_connected_users'] = 'Afficher les utilisateurs connectés';
$TEXT['enable_vserver_uptime'] = 'Afficher l\'uptime des serveurs';
$TEXT['sa_only'] = 'SuperAdmins seulement';

$TEXT['srv_dropdown_list'] =  'Liste déroulante des serveurs';
$TEXT['activate_auth_dropdown'] = 'Activer la liste déroulante des serveurs pour la page d\'authentification';
$TEXT['activate_auth_dropdown_info'] = 'Cela implique que les IDs des serveurs accessibles soient inscrit dans le code source de la page d\'authentification.';
$TEXT['refresh_ddl_cache'] = 'Temps en heure du rafraichissement automatique du cache';
$TEXT['ddl_show_cache_uptime'] =  'Afficher l\'uptime du cache';

$TEXT['autoban'] = 'Autoban';
$TEXT['autoban_attemps'] = 'Limite des tentatives';
$TEXT['autoban_frame'] = 'Période des tentatives ( en secondes )';
$TEXT['autoban_duration'] = 'Durée du ban ( en secondes )';

$TEXT['smtp_srv'] = 'Serveur smtp';
$TEXT['host'] = 'Hôte';
$TEXT['port'] = 'port';
$TEXT['default_sender_email'] = 'Email de l\'expéditeur par défaut';

$TEXT['external_viewer'] = 'Viewer externe';
$TEXT['see_external_viewer'] = 'Voire le viewer externe';
$TEXT['external_viewer_enable'] = 'Activer le viewer externe';
$TEXT['external_viewer_width'] = 'Largeur des viewers';
$TEXT['external_viewer_height'] = 'Hauteur des viewers';
$TEXT['external_viewer_vertical'] = 'Aligner verticalement les viewers';
$TEXT['external_viewer_scroll'] = 'Activer les barres de défilement des viewers';

// Generate password options
$TEXT['activate_pwgen'] = 'Activer la génération de mot de passe par email';
$TEXT['activate_explicite_msg'] = 'Activer les messages d\'erreurs explicites';
$TEXT['sender_email'] = 'Email de l\'expéditeur';
$TEXT['pwgen_max_pending'] = 'Delais d\'attente maximum pour une requête de génération de mot de passe ( en heure: 1 - 744 )';

 ?>