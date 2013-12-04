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

$TEXT['ice_error'] = 'Erreur fatale ICE:';

// ice errors
$TEXT['ice_module_not_found'] = 'Module php-ICE non trouvé';
$TEXT['ice_connection_refused'] = 'Connexion refusée';
$TEXT['ice_connection_timeout'] = 'Délais de connexion expiré';
$TEXT['ice_slice_profile_not_exists'] = 'Le profile slice n\'existe pas';
$TEXT['ice_no_slice_definition_found'] = 'Aucune définition slice trouvée';
$TEXT['ice_invalid_secret'] = 'Le mot de passe est incorrect';
$TEXT['ice_invalid_slice_file'] = 'Fichier des définitions slice invalide';
$TEXT['ice_icephp_not_found'] = 'Le fichier "Ice.php" est introuvable';
$TEXT['ice_invalid_murmur_version'] = 'Version murmur invalide';
$TEXT['ice_host_not_found'] = 'Hôte distant non trouvé';
$TEXT['ice_unknown_error'] = 'Erreur inconnue';

$TEXT['iceprofiles_admin_none'] = 'Vous n\'avez accès à aucun serveur. Veuillez en référer à votre admin';

$TEXT['ice_help_common'] = 'PhpMumbleAdmin n\'a pas pu se connecter sur l\'interface ICE';

$TEXT['ice_help_no_slice_definition_found'] = 'Cela veut dire que ni Murmur.ice, ni php-Slice n\'ont put être chargé';
$TEXT['ice_help_slice_32'] = 'Pour régler le problème, veuillez lire ce fichier, section icePHP 3.2';
$TEXT['ice_help_ice34'] = 'Veuillez lire README.txt => ICE 3.4';
$TEXT['ice_help_unauth'] = 'PhpMumbleAdmin a rencontré une erreur avec ICE<br>Toute activitée est désactivée durant cette erreur<br>Veuillez en référer à votre admin';
$TEXT['ice_help_upgrade_murmur'] = 'La version du démon murmur n\'est pas compatible avec PMA.<br>Il est necessaire de le mettre à jour vers une version 1.2.0 ou supérieur.';
$TEXT['ice_help_slice_file'] = 'Le fichier Murmur.ice ou slice-php utilisé est incompatible avec le serveur murmur. Le profile a été désactivé.';

// Messages
$TEXT['auth_error'] = 'Echec d\'authentification';
$TEXT['auth_su_disabled'] = 'Connexion SuperUser désactivée';
$TEXT['auth_ru_disabled'] = 'Connexion utilisateur désactivée';
$TEXT['change_pw_error'] = 'Echec - le mot de passe n\'a pas changé';
$TEXT['change_pw_success'] = 'Le mot de passe à été changé avec succès';
$TEXT['invalid_bitmask'] = 'Masque invalide';
$TEXT['invalid_channel_name'] = 'Charactère pour salon invalide';
$TEXT['invalid_username'] = 'Charactère pour utilisateur invalide';
$TEXT['invalid_certificate'] = 'Certificat invalide';
$TEXT['auth_vserver_stopped'] = 'Le serveur est arrêté pour le moment. Veuillez réessayer plus tard.';
$TEXT['user_already_registered'] = 'L\'utilisateur est déja enregistré. Il doit se reconnecter pour que son status passe en mode authentifié';
$TEXT['InvalidSessionException'] = 'Cet utilisateur n\'est pas connecté ou bien a quité le serveur';
$TEXT['InvalidChannelException'] = 'Le salon n\'existe pas ou bien a été supprimé';
$TEXT['InvalidUserException'] = 'Le compte n\'existe pas ou bien a été supprimé';
$TEXT['children_channel'] = 'Vous ne pouvez pas déplacer un salon vers un salon enfant';
$TEXT['ServerBootedException'] = 'Le serveur a été arreté durant la mise à jour. La dernière action a été annulée.';
$TEXT['invalid_secret_write'] = 'Vous n\'avez pas les droits en écriture ICE<br>Vous devez spécifier le mot de passe "icesecretwrite" à PMA';
$TEXT['ServerFailureException'] = 'Le serveur n\'a pas pu démarrer.<br>Veuillez vérifier les logs du serveur';
$TEXT['unknown_murmur_exception'] = 'Une erreur inconnue est survenue avec ICE';
$TEXT['vserver_dont_exists'] = 'Le serveur n\'existe pas ou bien à été supprimé';
$TEXT['username_exists'] = 'Ce nom d\'utilisateur existe déjà';
$TEXT['gen_pw_mail_sent'] = 'Un email de confirmation a été envoyé à votre adresse.<br>Veuillez en suivre les instructions pour générer un nouveau mot de passe.';
$TEXT['web_access_disabled'] = 'L\'accès web au serveur est désactivé';
$TEXT['vserver_dont_allow_HTML'] = 'Le serveur n\'autorise pas les tags HTML';
$TEXT['please_authenticate'] = 'Veuillez vous authentifier';
$TEXT['iceProfile_sessionError'] = 'Une erreur est survenue durant votre session - Pour des raisons de sécurité, veuillez vous ré-authentifier';
$TEXT['gen_pw_authenticated'] = 'Vous ne pouvez pas procéder à une génération de mot de passe en étant authentifié. Veuillez vous déconnecter et réessayer';
$TEXT['certificate_modified_success'] = 'Le certificat a été mis à jour avec succès<br>Vous devez redémarrer le serveur pour le prendre en compte';
$TEXT['host_modified_success'] = 'Le paramètre host a été mise à jour avec succès<br>Vous devez redémarrer le serveur pour le prendre en compte';
$TEXT['port_modified_success'] = 'Le port a été mis à jour avec succès<br>Vous devez redémarrer le serveur pour le prendre en compte';
$TEXT['illegal_operation'] = 'Opération illégale';
$TEXT['vserver_reset_success'] = 'La configuration du serveur a été réinitialisée avec succès';
$TEXT['new_su_pw'] = 'Nouveau mot de passe pour le SuperUser: %s'; // %s = new SuperUser password
$TEXT['registration_deleted_success'] = 'Le compte a été supprimé avec succès';
$TEXT['gen_pw_error'] = 'Un erreur est survenue et PMA ne peut pas traiter votre demande de génération de mot de passe';
$TEXT['gen_pw_invalid_server_id'] = 'Le serveur n\'existe pas et PMA ne peut pas traiter votre demande de génération de mot de passe';
$TEXT['gen_pw_invalid_username'] = 'Le pseudo n\'existe pas et PMA ne peut pas traiter votre demande de génération de mot de passe';
$TEXT['gen_pw_su_denied'] = 'Vous ne pouvez pas faire de demande de génération de mot de passe pour le compte SuperUser';
$TEXT['gen_pw_empty_email'] = 'Aucune adresse email trouvée pour le compte et PMA ne peut pas traiter votre requete de génération de mot de passe';
$TEXT['new_pma_version'] = 'PhpMumbleAdmin %s est disponible'; // %s = new PMA version
$TEXT['no_update_found'] = 'Aucune mise à jour disponible';
$TEXT['registration_created_success'] = 'Le compte a été créé avec succès';
$TEXT['iceMemoryLimitException_logs'] = 'Les logs du serveur sont trop volumineux.<br>Veuillez signaler à votre admin de diminuer les requêtes de logs.<br>En attendant, PMA à forcé une requête de 100 lignes.';
$TEXT['vserver_created_success'] = 'Le serveur virtuel a été créé avec succès';
$TEXT['vserver_deleted_success'] = 'Le serveur virtuel a été supprimé avec succès';
$TEXT['refuse_cookies'] = 'Les cookies sont désactivés.<br>Il est nécessaire de les accepter pour pouvoir vous authentifier.';
$TEXT['parameters_updated_success'] = 'Tous les serveurs ont été configurés avec succès';
$TEXT['NestingLimitException'] = 'Limite d\'imbrication des salons atteinte';



?>