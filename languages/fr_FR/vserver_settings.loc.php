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

$TEXT['registername_info'] = 'Nom du serveur<br>Ce paramètre est aussi requis pour enregistrer votre serveur dans la liste globale des serveurs publiques.';
$TEXT['host_info'] = 'Adresse du serveur virtuel.<br>Redémarage necessaire';
$TEXT['port_info'] = 'Port du serveur virtuel.<br>Redémarage necessaire';
$TEXT['password_info'] = 'Mot de passe pour les utilisateurs non-enregistrés. Laissez le paramètre vide pour un serveur publique.';
$TEXT['timeout_info'] = 'Delais en secondes avant de fermer les connexions mortes.';
$TEXT['bandwidth_info'] = 'Limite de la bande passante (en bits par secondes) que les utilisateurs peuvent utiliser pour la parole.';
$TEXT['users_info'] = 'Maximum d\'utilisateurs acceptés par le serveur virtuel en même temps.';
$TEXT['defaultchannel_info'] = 'ID du salon par défaut';
$TEXT['registerpassword_info'] =  'Mot de passe pour la liste globale des serveurs publiques';
$TEXT['registerhostname_info'] = 'DNS du serveur pour la la liste globale des serveurs publiques. Ce paramètre permet d\'atteindre votre serveur avec son DNS plutôt qu\'avec son adresse IP. Le DNS doit être valide ou sinon, l\'enregistrement du serveur échoura.';
$TEXT['registerurl_info'] = 'URL de votre site web pour la la liste globale des serveurs publiques';
$TEXT['username_info'] = 'Expression régulière validant les caractères autorisés des noms d\'utilisateur';
$TEXT['channelname_info'] = 'Expression régulière validant les caractères autorisés des noms de salon.';
$TEXT['textmessagelength_info'] = 'Maximum de caractères autorisés dans un message texte.<br>0 pour aucune limite.';
$TEXT['imagemessagelength_info'] = 'Maximum de caractères autorisés dans un message texte avec image.<br>0 pour aucune limite.';
$TEXT['allowhtml_info'] = 'Utilisation du HTML pour les messages, les commentaires utilisateurs et les descriptions de salon';
$TEXT['bonjour_info'] = 'Service de découverte bonjour';
$TEXT['certrequired_info'] = 'Rejeter les clients ne présentant pas de certificat.';
$TEXT['usersperchannel_info'] = 'Maximum de client par salon.';
$TEXT['rememberchannel_info'] = 'Reconnexion des utilisateurs enregistrés au dernier salon qu\'ils occupaient.';
$TEXT['opusthreshold_info'] = 'Nombre d\'utilisateurs supportant Opus requis pour forcer son usage, en pourcentage.<br>0 = Toujours activer Opus.<br>100 = activer Opus si supporté par tous les utilisateurs.';
$TEXT['channelnestinglimit_info'] = 'Profondeur maximum d\'imbrication des salons. Notez que certaines base de données comme MySQL utilisant InnoDB échouent avec des imbrications de salons trop profondes.';
$TEXT['suggestpositional_info'] = 'Activer ce paramètre permet d\'alerter les utilisateurs qui n\'ont pas activés la position audio que vous suggerez de le faire.<br>Le désactiver alertera les utilisateurs qui ont activés la position audio, que vous suggerez de ne pas le faire.<br>Laissez le paramètre vide si vous ne voulez pas lancer de notification de suggestion.';
$TEXT['suggestpushtotalk_info'] = 'Setting this to "enabled" will alert any user who does not have Push-To-Talk enabled that the server administrators recommend enabling it.<br>Setting it to "disabled" will have the opposite effect<br>If you do not care whether the user enables PTT or not, set it to blank. The message will appear in the log window upon connection, but only if the user\'s settings do not match what the server requests.';
$TEXT['suggestpushtotalk_info'] = 'Activer ce paramètre permet d\'alerter les utilisateurs qui n\'ont pas activés la fonction appuyez-pour-parler que vous suggerez de le faire.<br>Le désactiver alertera les utilisateurs qui ont activés appuyez-pour-parler, que vous suggerez de ne pas le faire.<br>Laissez le paramètre vide si vous ne voulez pas lancer de notification de suggestion.';

$TEXT['reset_param'] = 'Réinitialiser le paramètre %s'; // %s = setting key
$TEXT['enable'] = 'Activer';
$TEXT['disable'] = 'Désactiver';
$TEXT['enabled'] = 'Activé';
$TEXT['disabled'] = 'Désactivé';
$TEXT['invalid_cert'] = 'Erreur: certificat invalide';
$TEXT['confirm_reset_cert'] = 'Confirmez-vous la réinitialisation du certificat?';
$TEXT['welcometext'] = 'Message d\'acceuil';
$TEXT['certificate'] = 'Certificat';
$TEXT['add_certificate'] = 'Ajouter un certificat et sa clé privée';


?>