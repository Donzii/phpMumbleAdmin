<?php

 /**
 * phpMumbleAdmin (PMA), web php administration tool for murmur (mumble server daemon).
 * Copyright (C) 2010 - 2015  Dadon David. PMA@ipnoz.net
 *
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 *    along with this program.  If not, see <http://www.gnu.org/licenses/> .
 */

define('PMA_OLD_ERROR_REPORTING', error_reporting(-1));

define('PMA_STARTED', microTime());

define('PMA_PATH_ROOT', __DIR__ .'/');
define('PMA_DIR_PROG', PMA_PATH_ROOT.'program/');

require PMA_DIR_PROG.'includes/def.files.inc';
require PMA_DIR_INCLUDES.'init.inc';
/**
* Load functions and PMA exceptions.
*/
require PMA_DIR_FUNCTIONS.'misc.php';
require PMA_DIR_FUNCTIONS.'sort.php';
require PMA_DIR_FUNCTIONS.'PMA.php';
require PMA_DIR_FUNCTIONS.'debug.php';
require PMA_DIR_LIB.'PMA_exceptions.php';
/**
* Initialize core object.
*/
$PMA = PMA_core::getInstance();
/**
* Reset php ERROR_REPORTING level if PMA debug is off.
*/
if ($PMA->config->get('debug') === 0) {
    error_reporting(PMA_OLD_ERROR_REPORTING);
}
/**
* Register shutdown method.
*/
register_shutdown_function(array($PMA, 'shutdown'));
/**
* Check bans
*/
$PMA->debug('Checking your IP: '.$_SERVER['REMOTE_ADDR'], 3);
if ($PMA->bans->checkIP($_SERVER['REMOTE_ADDR'])) {
    $PMA->bans->killPma();
}
$PMA->debug('You can read this message, good for you ! :-D', 3);
/**
* Init cookie
*/
$PMA->debug('Setup cookie', 3);
// Set default options.
$PMA->cookie->set('profile_id', $PMA->config->get('default_profile'));
$PMA->cookie->set('lang', $PMA->config->get('default_lang'));
$PMA->cookie->set('skin', $PMA->config->get('default_skin'));
$PMA->cookie->set('timezone', $PMA->config->get('default_timezone'));
$PMA->cookie->set('time', $PMA->config->get('default_time'));
$PMA->cookie->set('date', $PMA->config->get('default_date'));
$PMA->cookie->set('installed_localeFormat', $PMA->config->get('defaultSystemLocales'));
$PMA->cookie->set('uptime', $PMA->config->get('default_uptime'));
// No conf cookie found,
// check if user really accept cookie or if it is the first connection to PMA.
if (! $PMA->cookie->loadCookie()) {
    // Redirect user to check that he accepted the config cookie with the "check cookie url"
    if (! isset($_GET[PMA_cookie::CHECK_URL])) {
        $PMA->cookie->requestUpdate();
        $PMA->redirection('?'.PMA_cookie::CHECK_URL);
    } else {
        // Still no cookie: user don't accept cookies
        $PMA->messageError('refuse_cookies');
        $PMA->messageError('Check again'); // href="./"
    }
}
/**
* INCLUDE PATH
* Add the path for "Ice.php" to include_path.
*/
if (PMA_ICE_INT >= 30400 && $PMA->config->get('IcePhpIncludePath') !== '') {
    set_include_path(get_include_path().PATH_SEPARATOR .$PMA->config->get('IcePhpIncludePath'));
}
/**
* TIMEZONE
*/
$PMA->debug('Setup timezone', 3);
$PMA->dateTimeFormat = $PMA->cookie->get('date').' - '.$PMA->cookie->get('time');
setTimezone($PMA->cookie->get('timezone'));
/**
* LOCALES
*/
$localesProfiles = $PMA->config->get('systemLocalesProfiles');
$userLocale = $PMA->cookie->get('installed_localeFormat');
if (isset($localesProfiles[$userLocale])) {
    setLocale(LC_ALL, $userLocale);
} else {
    setLocale(LC_ALL, $PMA->config->get('defaultSystemLocales'));
}
/**
* SESSION
*/
$PMA->debug('Setup session', 3);
$PMA->session->setDirectory(PMA_DIR_SESSIONS);
$PMA->session->setCookiePath(PMA_HTTP_PATH);
$PMA->session->setAutoLogout($PMA->config->get('auto_logout') * 60);
if (! $PMA->session->isWritableDir()) {
    $PMA->fatalError('Session directory is not writeable.');
}
if ($PMA->session->isSsanityRequired($PMA->app->get('lastSessionsCheck'))) {
    $PMA->debug('session sanity...', 1);
    $PMA->session->removeOutdatedSessions();
    $PMA->app->set('lastSessionsCheck', time()); // Update last check timestamp.
}
if ($PMA->cookie->userAcceptCookies()) {
    $PMA->session->start();
}
$PMA->session->initialize();
$PMA->messages = $PMA->session->mergeMessages($PMA->messages);
/**
* ROUTES CONTROLERS
*/
$PMA->debug('Setup routes', 3);
$PMA->router->initialize();
$PMA->router->newController('profile', false, true);
$PMA->router->newController('page');
$PMA->router->newController('tab');
$PMA->router->newController('subtab');
$PMA->router->loadHistoryNoDeep();
$PMA->router->loadHistoryDeep();
/**
* Setup user profile
*/
$PMA->userProfile = $PMA->profiles->get($PMA->router->getRoute('profile'));
/**
* USER
*/
$PMA->debug('Setup user', 3);
$PMA->user->setProfileID($PMA->router->getRoute('profile'));
$PMA->user->setup();
if ($PMA->user->isPmaAdmin()) {
    $PMA->admins = new PMA_datas_admins();
    $registration = $PMA->admins->get($PMA->user->adminID);
    if (is_null($registration)) {
        $PMA->logout();
        $PMA->debugError('Admin do not exist. Logout.');
        $PMA->redirection();
    }
    // Update admin login & class, it may have changed during two requests.
    $PMA->user->setLogin($registration['login']);
    $PMA->user->setClass($registration['class']);
    if ($PMA->user->is(PMA_USER_ADMIN)) {
        // Update admins access only.
        $PMA->user->setAdminAccess($registration['access']);
    }
}
/**
* Setup widgets.
*/
$PMA->widgets = new PMA_widgets();
$PMA->widgets->setPath('widget', PMA_DIR_WIDGETS);
$PMA->widgets->setPath('popup', PMA_DIR_POPUPS);
/**
* External viewer.
*/
if (isset($_GET['ext_viewer'])) {
    require PMA_DIR_INCLUDES.'externalViewer.inc';
    die();
}
/**
* Execute commands.
*/
if (isset($_GET['cmd']) OR isset($_POST['cmd'])) {
    /**
    * Check for Ice-PHP 3.4 workaround.
    */
    require PMA_FILE_ICE34_WORKAROUND;
    $params = isset($_GET['cmd']) ? $_GET : $_POST;
    $cmd = PMA_cmd::factory($params);

    if (is_object($cmd)) {
        try {
            $cmd->setParameters($params);
            $cmd->process();
        } catch (PMA_cmdException $e) {
            // Do nothing.
        } catch (Exception $e) {
            pmaExceptionsOperations($e);
        }
        $cmd->cmdShutdown();
        /**
        * Always redirect after a command.
        */
        $PMA->redirection($cmd->getRedirection());
    }
}
/**
* Load images definitions.
*/
require PMA_DIR_INCLUDES.'def.images.inc';
/**
* Setup routes.
*/
$PMA->modules = new PMA_modules();
$PMA->modules->setPath(PMA_DIR_MODULES);
require PMA_DIR_ROUTES.'profiles.php';
require PMA_DIR_ROUTES.'pages.php';
require PMA_DIR_ROUTES.$PMA->router->getRoute('page').'.php';
/**
* Check for Ice-PHP 3.4 workaround.
*/
require PMA_FILE_ICE34_WORKAROUND;
/**
* Setup current user who online widget.
*/
if ($PMA->user->isMinimum(PMA_USER_UNAUTH)) {
    $PMA->whosOnline = new PMA_datas_whosOnline();
    $PMA->whosOnline->setAutoLogout($PMA->config->get('auto_logout') * 60);
    $PMA->whosOnline->updateUser($PMA->user);
    $PMA->whosOnline->removeOldActivity();
}
/**
* Load languages for the current page.
*/
pmaLoadLanguage('common');
pmaLoadLanguage($PMA->router->getRoute('page'));
/**
* Add common CSS files.
*/
$PMA->skeleton->addCssFile('classes.css');
$PMA->skeleton->addCssFile('main.css');
$PMA->skeleton->addCssFile('themes/'.$PMA->cookie->get('skin'));
/**
* Add common JS text.
*/
$PMA->skeleton->addJsText('pw_check_failed', $TEXT['password_check_failed']);
$PMA->skeleton->addJsText('invalid_ip', $TEXT['invalid_IP_address']);
$PMA->skeleton->addJsText('invalid_port', $TEXT['invalid_port']);
$PMA->skeleton->addJsText('invalid_number', $TEXT['invalid_numerical']);
/**
* Setup main widgets.
*/
$PMA->widgets->newWidget('main_languagesFlags');
$PMA->widgets->newWidget('main_userLogout');
$PMA->widgets->newWidget('route_pages');
$PMA->widgets->newWidget('route_profiles');
$PMA->widgets->newWidget('main_messages');
$PMA->widgets->newWidget('main_serversPanel');
$PMA->widgets->newWidget('main_infoPanel');
$PMA->widgets->newWidget('route_tabs');
$PMA->widgets->newWidget('route_subTabs');
$PMA->widgets->newWidget('main_whosOnline');
$PMA->widgets->newWidget('main_debug');
$PMA->widgets->newWidget('main_captions');
/**
* Setup misc variables.
*/
$PMA->skeleton->siteTitleEnc = htEnc($PMA->config->get('siteTitle'));
$PMA->skeleton->siteCommentEnc = htEnc($PMA->config->get('siteComment'));
$PMA->skeleton->footerDate = strftime('%A '.$PMA->cookie->get('date').' - '.$PMA->cookie->get('time'));
/**
* Setup module.
*/
$PMA->mainViewPath = null;
$module = new PMA_output();
try {
    foreach ($PMA->modules->getPaths() as $file) {
        $PMA->debug('Module : loading '.$file, 3);
        require PMA_DIR_MODULES.$file;
    }
    if (is_file($path = $PMA->modules->getView())) {
        $PMA->mainViewPath = $path;
    }
} catch (PMA_moduleException $e) {
    // Do nothing
} catch (Exception $e) {
    pmaExceptionsOperations($e);
}
/**
* Setup widgets.
*/
foreach ($PMA->widgets->getList() as $obj) {
    $PMA->debug('Loading '.$obj->type.' '.$obj->id, 3);
    if (is_readable($obj->classPath)) {
        require $obj->classPath;
    }
    try {
        $PMA->widgets->setCurrentID($obj->id);
        $widget = new PMA_output();
        if (is_readable($obj->controllerPath)) {
            require $obj->controllerPath;
        }
        $PMA->widgets->saveDatas($widget);
    } catch (PMA_widgetException $e) {
        //$PMA->debugError($obj->type.' '.$obj->id);
        $PMA->widgets->disableWidget();
    } catch (Exception $e) {
        //$PMA->debugError($obj->type.' '.$obj->id);
        $PMA->widgets->disableWidget();
        pmaExceptionsOperations($e);
    }
}
/**
* Enable unhidden popup as main view if no module view are available.
*/
if (is_null($PMA->mainViewPath)) {
    foreach ($PMA->widgets->getList() as $obj) {
        if ($obj->type === 'popup' && ! $obj->hidden) {
            $PMA->mainViewPath = $obj->viewPath;
            break;
        }
    }
}
/**
* Update referer only during output.
*/
$PMA->session->updateReferer();
/**
* Shutdown PMA.
*/
$PMA->shutdown();
/**
* Display output.
*/
?>
<!DOCTYPE html>

<html>
<!-- <html manifest="cache.manifest.txt"> -->

    <head>

        <meta charset="utf-8" />
        <title><?php echo $PMA->skeleton->siteTitleEnc; ?></title>
        <meta name="description" content="<?php echo $PMA->skeleton->siteCommentEnc; ?>" />
        <meta name="generator" content="phpMumbleAdmin" />
        <link href="images/mumble/mumble.png" rel="shortcut icon" />
<?php foreach ($PMA->skeleton->getCssFiles() as $file): ?>
        <link rel="stylesheet" type="text/css" href="css/<?php echo htEnc($file); ?>" />
<?php endforeach; ?>
        <script src="js/common.js" type="text/javascript"></script>
        <script src="js/drag.js" type="text/javascript"></script>
        <script src="js/expand.js" type="text/javascript"></script>
        <script src="js/helpers.js" type="text/javascript"></script>
        <script src="js/popup.js" type="text/javascript"></script>
        <script src="js/validators.js" type="text/javascript"></script>
        <script type="text/javascript">
            TEXT = new Object();
<?php foreach ($PMA->skeleton->getJsTexts() as $js): ?>
            TEXT.<?php echo $js->id; ?> = "<?php echo $js->text; ?>";
<?php endforeach; ?>
        </script>

    </head>

    <body>

<?php if ($PMA->widgets->hiddensExists()): ?>
        <div id="jsPopups">
<?php foreach ($PMA->widgets->getHiddens() as $obj): ?>
            <div hidden="hidden">
<?php require $obj->viewPath; ?>
            </div>
<?php endforeach; ?>
        </div>
<?php endif; ?>

        <header id="PMA_header">

<?php require $PMA->widgets->getView('main_languagesFlags'); ?>

            <h1><?php echo $PMA->skeleton->siteTitleEnc; ?></h1>
            <h3><?php echo $PMA->skeleton->siteCommentEnc; ?></h3>

            <div id="PMA_user">
<?php require $PMA->widgets->getView('main_userLogout'); ?>
            </div>

<?php require $PMA->widgets->getView('route_pages'); ?>

        </header>

<?php require $PMA->widgets->getView('route_profiles'); ?>

<?php require $PMA->widgets->getView('main_messages'); ?>

        <div id="PMA_body">
            <div id="PMA_bodyPanel">
<?php require $PMA->widgets->getView('main_serversPanel'); ?>
            </div>

<?php require $PMA->widgets->getView('main_infoPanel'); ?>

<?php require $PMA->widgets->getView('route_tabs'); ?>

            <main>
                <div class="inside">

<?php if (is_readable($PMA->mainViewPath)) {
    require $PMA->mainViewPath;
} ?>

                </div><!-- inside - END -->

<?php require $PMA->widgets->getView('main_captions'); ?>

            </main>
        </div><!-- PMA_body - END -->

<?php require $PMA->widgets->getView('main_whosOnline'); ?>

        <footer id="PMA_footer">
            <p><?php echo $PMA->skeleton->footerDate; ?></p>
            <p>
                <span>Powered by</span>
                <a href="http://sourceforge.net/projects/phpmumbleadmin/"><?php echo PMA_NAME; ?></a>
<?php if ($PMA->user->isMinimum(PMA_USER_ROOTADMIN)): ?>
                <span><?php echo PMA_VERS_STR.PMA_VERS_DESC; ?></span>
<?php endif; ?>
            </p>
        </footer>

<?php require $PMA->widgets->getView('main_debug'); ?>

    </body>

</html>
