<?php
/*
 * Samus Framework - Configuration
 * Configuração automática do documento "global_configuration.ini"
 *
 * @author Vinicius Fiorio Custódio - samus@samus.com.br
 * @package Samus
 */
$GLOBALS['error'] = false;
$errorMsg = '';
$success = false;

$configFile = "system/configs/global_configuration.ini";
$c = parse_ini_file($configFile);


if ($c['disable_init'] == '1') {
    echo 'The init configuration is disabled';
    exit();
}

$baseDir = str_replace('web/init.php', '', $_SERVER['REQUEST_URI']) . 'sf/';


if (isset($_POST['action']) && $_POST['action'] == 'Confirm') {

    // directory validation
    if (!is_dir($_POST['directory'])) {
        displayError("O diretório " . $_POST['directory'] . ' não é um diretório válido');
    }

    // connection validation
    if ($_POST['queryMode'] == 'pdo') {
        try {
            $pdo = new PDO($_POST['adapter'] . ":host=" . $_POST['host'] . ";dbname=" . $_POST['name'], $_POST['user'], $_POST['password']);
        } catch (PDOException $pdoEx) {
            displayError('Database connection fail: ' . $pdoEx->getMessage());
        }
    } elseif ($_POST['queryMode'] == 'mysqli') {

        if (!($con = mysqli_connect($_POST['host'], $_POST['user'], $_POST['password']))) {
            displayError("Database connection fail");
        }

        if (!(mysqli_select_db($con, $_POST['name']))) {
            displayError("Database " . $_POST['name'] . " doesn't exist");
        }
    }

    // url validation
    if (is_link($_POST['url'] . 'index.php')) {
        displayError('Invalid URL');
    }

    // virtual url validation
    if (is_link($_POST['appUrl'] . 'install/test/sayHello')) {
        displayError('Invalid APP URL');
    }

    $iniStr = "[project]
title=" . $_POST['title'] . "
directory=" . $_POST['directory'] . "
url=" . $_POST['url'] . "
applUrl=" . $_POST['applUrl'] . "
sitedir=" . $_POST['sitedir'] . "
adminMail=" . $_POST['adminMail'] . "
is_config=" . $_POST['is_config'] . "

[connection]
queryMode=" . $_POST['queryMode'] . "
adapter=" . $_POST['adapter'] . "
name=" . $_POST['name'] . "
user=" . $_POST['user'] . "
password=" . $_POST['password'] . "
charset=" . $_POST['charset'] . "
engine=" . $_POST['engine'] . "
table_prefix=" . $_POST['table_prefix'] . "
host=" . $_POST['host'] . "

[security]
disable_init=" . $_POST['disable_init'] . "
disable_sf_assistants=" . $_POST['disable_sf_assistants'] . "
";

    $resourse = fopen('system/configs/global_configuration.ini', 'w');
    fwrite($resourse, $iniStr);
    fclose($resourse);

    $success = true;
}

function displayError($msg) {
    $GLOBALS['error'] = true;
    $GLOBALS['errorMsg'] .= $msg . '
<br />
<br />
';
}

if (isset($_POST['action']) && $_POST['action'] == 'Default Ini') {

    $success = true;

    $iniStr = "[project]
title=
directory=
url=
applUrl=
sitedir=
adminMail=
is_config=false

[connection]
queryMode=
adapter=
name=
user=
password=
charset=
engine=
table_prefix=
host=

[security]
disable_init=false
disable_sf_assistants=false
";

    $resourse = fopen('system/configs/global_configuration.ini', 'w');
    fwrite($resourse, $iniStr);
    fclose($resourse);
}


$configFile = "system/configs/global_configuration.ini";
$c = parse_ini_file($configFile);

if (!isset($c['title']) || empty($c['title']))
    $c['title'] = 'Samus Framework Project';

$currentDirectory = str_replace('\\', '/', dirname(__FILE__) . '/');

if (!isset($c['directory']) || empty($c['directory'])) {
    $c['directory'] = $currentDirectory;
}

$uri = str_replace('init.php', '', $_SERVER['REQUEST_URI']);
$host = 'http://' . $_SERVER['HTTP_HOST'] . $uri."web/";

if (!isset($c['url']) || empty($c['url'])) {
    $c['url'] = $host;
}

if (!isset($c['applUrl']) || empty($c['applUrl'])) {
    $c['applUrl'] = str_replace('web/', '', $host);
}

if (!isset($c['sitedir']) || empty($c['sitedir'])) {
    $c['sitedir'] = 'site';
}

if (!isset($c['adminMail']) || empty($c['adminMail'])) {
    $c['adminMail'] = 'samus@samus.com.br';
}

if (!isset($c['name']) || empty($c['name'])) {
    $c['name'] = 'sf';
}

if (!isset($c['user']) || empty($c['user'])) {
    $c['user'] = 'root';
}

if (!isset($c['password']) || empty($c['password'])) {
    $c['password'] = '';
}

if (!isset($c['password']) || empty($c['password'])) {
    $c['password'] = '';
}

if (!isset($c['charset']) || empty($c['charset'])) {
    $c['charset'] = 'latin1';
}

if (!isset($c['engine']) || empty($c['engine'])) {
    $c['engine'] = 'InnoDB';
}

if (!isset($c['table_prefix']) || empty($c['table_prefix'])) {
    $c['table_prefix'] = 'sf_';
}

if (!isset($c['host']) || empty($c['host'])) {
    $c['host'] = 'localhost';
}
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
    <head>
        <link rel="shortcut icon" href="http://framework.samus.com.br/fav.ico" type="image/x-icon"/> 
        <style type="text/css">
            body { font-size: 11px; font-family: Arial;margin: auto; text-align: center; color: #333; background-color: #EBEBEB; margin-bottom: 20px; }
            fieldset { background-color: #FCFCFC; border-width: 1px; padding: 8px; text-align: left; margin: auto; margin-top: 10px; border-color: #A7A7A7; }
            fieldset legend { color: #333; padding: 2px 4px; border: 1px solid #C3C3C3; font-weight: bold; background-color: #fff; border-bottom: 1px solid #7E7E7E; border-right: 1px solid #7E7E7E; text-transform: uppercase; }
            fieldset input { width: 95%; }
            label { display: block; margin-top: 6px; }
            fieldset#config-root { width: 40%; }
            input[type='text'] { padding: 5px; }
            input[type='text']:focus { color:#338000; outline: 1px solid greenyellow }
            .success { background-color: #007C26; color: #fff; text-align: center; padding: 10px; }
            .error { background-color: #C40A0A; color: #fff; padding: 3px 8px; text-align: center; margin: 5px 0px; }
            .error hr { border: none; border-bottom: 1px solid #990B0B; height: 1px;  }
            #config-root { border-right: 2px solid #696C74; border-bottom: 2px solid #696C74; }
            .a-right {text-align: right;}
        </style>

        <meta http-equiv="Content-Type" content="text/html; charset=ISO-8859-1"/>
        <title>Config Application</title>
    </head>
    <body>
        <div>

            <form method="post" action="">
                <div>
                    <fieldset id="config-root">
                        <legend>Config Application</legend>

                        <p class="a-right">
                            <a href="sf/helloWorld" target="_blank">
                                Test: "Hello World" page
                            </a>
                        </p>


                        <?php
                        if ($success) {
                        ?>

                            <div class="success" onclick="this.style.display='none'">
                                global_configuration.ini updated
                            </div>

                        <?php } ?>


                        <?php
                        if ($GLOBALS['error']) {
                        ?>
                            <div class="error" onclick="this.style.display='none'">
                                Error: <?php echo $GLOBALS['errorMsg']; ?>
                            </div>
                        <?php } ?>

                        <fieldset>
                            <legend>Project</legend>


                            <label>Project Name</label>
                            <input type="text" name="title" value="<?php echo $c['title']; ?>" />

                            <label>Directory</label>
                            <input type="text" name="directory" value="<?php echo $c['directory']; ?>" />

                            <label>URL</label>
                            <input type="text" name="url" value="<?php echo $c['url']; ?>" />

                            <label>App Url</label>
                            <input type="text" name="applUrl" value="<?php echo $c['applUrl']; ?>" />

                            <label>Site Base Dir</label>
                            <input type="text" name="sitedir" value="<?php echo $c['sitedir']; ?>" />

                            <label>Admin Mail</label>
                            <input type="text" name="adminMail" value="<?php echo $c['adminMail']; ?>" />
                        </fieldset>

                        <br />

                        <fieldset>
                            <legend>Database Connection</legend>

                            <label>Query Mode</label>
                            <select name="queryMode">
                                <option value="pdo">PDO</option>
                                <option value="mysqli">MySqli</option>
                            </select>

                            <label>Adapter</label>
                            <select name="adapter">
                                <option value="mysql">MySql</option>
                                <option value="postgree">PostGree</option>
                            </select>

                            <label>Database Name</label>
                            <input type="text" name="name" value="<?php echo $c['name']; ?>" />

                            <label>Username</label>
                            <input type="text" name="user" value="<?php echo $c['user']; ?>" />

                            <label>Password</label>
                            <input type="text" name="password" value="<?php echo $c['password']; ?>" />

                            <label>Charset</label>
                            <input type="text" name="charset" value="<?php echo $c['charset']; ?>" />

                            <label>Engine</label>
                            <input type="text" name="engine" value="<?php echo $c['engine']; ?>" />

                            <label>Table Prefix</label>
                            <input type="text" name="table_prefix" value="<?php echo $c['table_prefix']; ?>" />

                            <label>Host</label>
                            <input type="text" name="host" value="<?php echo $c['host']; ?>" />

                        </fieldset>

                        <label>
                            <?php
                            if ($c['disable_init'] == '1') {
                            ?>
                                <input type="checkbox" name="disable_init" value="1" class="left" checked="checked" style="width: 15px;" />
                            <?php } else {
                            ?>
                                <input type="checkbox" name="disable_init" value="1" class="left" style="width: 15px;" />
                            <?php } ?>

                            Disable init.php
                        </label>
                        <label>

                            <?php
                            if ($c['disable_sf_assistants'] == '1') {
                            ?>
                                <input type="checkbox" name="disable_sf_assistants" value="1" class="left" checked="checked" style="width: 15px;" />
                            <?php } else {
 ?>
                                <input type="checkbox" name="disable_sf_assistants" value="1" class="left" style="width: 15px;" />
<?php } ?>

                            Disable SF Assistants ("newPage", "newFilter" ...)
                        </label>


                        <label>

                            <?php
                            if ($c['is_config'] == '1') {
                            ?>
                                <input type="checkbox" name="is_config" value="true" class="left" checked="checked" style="width: 15px;" />
<?php } else { ?>
                                <input type="checkbox" name="is_config" value="true" class="left" style="width: 15px;" />
<?php } ?>

                            Project is currect configurated
                        </label>

                        <br />
                        <hr />

                        <div style="text-align: right;">

                            <input type="submit" value="Confirm" name="action" style="width: 100px; " />
                            <input type="submit" value="Default Ini" name="action" style="width: 90px; font-size: 10px; float: left;" onclick="return confirm('This action will overwrite the current configuration, cannot be undone');" />
                        </div>

                    </fieldset>
                </div>
            </form>


        </div>


    </body>
</html>
