<?php

#################################################################
#  Copyright notice
#
#  (c) 2013 Jérôme Schneider <mail@jeromeschneider.fr>
#  All rights reserved
#
#  http://sabre.io/baikal
#
#  This script is part of the Baïkal Server project. The Baïkal
#  Server project is free software; you can redistribute it
#  and/or modify it under the terms of the GNU General Public
#  License as published by the Free Software Foundation; either
#  version 2 of the License, or (at your option) any later version.
#
#  The GNU General Public License can be found at
#  http://www.gnu.org/copyleft/gpl.html.
#
#  This script is distributed in the hope that it will be useful,
#  but WITHOUT ANY WARRANTY; without even the implied warranty of
#  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
#  GNU General Public License for more details.
#
#  This copyright notice MUST APPEAR in all copies of the script!
#################################################################

namespace BaikalAdmin\Controller\Install;

class Database extends \Flake\Core\Controller {
    protected $aMessages = [];
    protected $oModel;
    protected $oForm;    # \Formal\Form

    function execute() {
        $this->oModel = new \Baikal\Model\Config\Database();

        if (file_exists(PROJECT_PATH_SPECIFIC . "config.system.php")) {
            require_once PROJECT_PATH_SPECIFIC . "config.system.php";
            $this->oModel->set('sqlite_file', PROJECT_SQLITE_FILE);
            $this->oModel->set('mysql', PROJECT_DB_MYSQL);
            $this->oModel->set('mysql_host', PROJECT_DB_MYSQL_HOST);
            $this->oModel->set('mysql_dbname', PROJECT_DB_MYSQL_DBNAME);
            $this->oModel->set('mysql_username', PROJECT_DB_MYSQL_USERNAME);
            $this->oModel->set('mysql_password', PROJECT_DB_MYSQL_PASSWORD);
            $this->oModel->set('encryption_key', BAIKAL_ENCRYPTION_KEY);
        }

        $this->oForm = $this->oModel->formForThisModelInstance([
            "close"           => false,
            "hook.validation" => [$this, "validateConnection"],
            "hook.morphology" => [$this, "hideMySQLFieldWhenNeeded"],
        ]);

        if ($this->oForm->submitted()) {
            $this->oForm->execute();

            if ($this->oForm->persisted()) {
                if (file_exists(PROJECT_PATH_SPECIFIC . "config.system.php")) {
                    @unlink(PROJECT_PATH_SPECIFIC . "config.system.php");
                }
                touch(PROJECT_PATH_SPECIFIC . '/INSTALL_DISABLED');

                if (defined("BAIKAL_CONFIGURED_VERSION")) {
                    $oStandardConfig = new \Baikal\Model\Config\Standard();
                    $oStandardConfig->set("configured_version", BAIKAL_CONFIGURED_VERSION);
                    $oStandardConfig->persist();

                    # We've just rolled back the configured version, so reload so that we get to the
                    # version upgrade page rather than the database is configured message in render below
                    $sLink = PROJECT_URI . "admin/install/?/database";
                    \Flake\Util\Tools::redirect($sLink);
                    exit(0);
                }
            }
        }
    }

    function render() {
        $sBigIcon = "glyph2x-magic";
        $sBaikalVersion = BAIKAL_VERSION;

        $oView = new \BaikalAdmin\View\Install\Database();
        $oView->setData("baikalversion", BAIKAL_VERSION);

        if ($this->oForm->persisted()) {
            $sMessage = "<p>Baïkal is now installed, and its database properly configured. <strong>For security reasons, this installation wizard is now disabled.</strong></p>";
            $sMessage . "<p>&nbsp;</p>";
            $sMessage .= "<p><a class='btn btn-success' href='" . PROJECT_URI . "admin/'>Start using Baïkal</a></p>";
            $sForm = "";
        } else {
            $sMessage = "";
            $sForm = $this->oForm->render();
        }

        $oView->setData("message", $sMessage);
        $oView->setData("form", $sForm);

        return $oView->render();
    }

    function validateConnection($oForm, $oMorpho) {
        if ($oForm->refreshed()) {
            return true;
        }
        $bMySQLEnabled = $oMorpho->element("mysql")->value();

        if ($bMySQLEnabled) {
            $sHost = $oMorpho->element("mysql_host")->value();
            $sDbname = $oMorpho->element("mysql_dbname")->value();
            $sUsername = $oMorpho->element("mysql_username")->value();
            $sPassword = $oMorpho->element("mysql_password")->value();

            try {
                $oDb = new \Flake\Core\Database\Mysql(
                    $sHost,
                    $sDbname,
                    $sUsername,
                    $sPassword
                );

                if (($aMissingTables = \Baikal\Core\Tools::isDBStructurallyComplete($oDb)) !== true) {
                    # Checking if all tables are missing
                    $aRequiredTables = \Baikal\Core\Tools::getRequiredTablesList();
                    if (count($aRequiredTables) !== count($aMissingTables)) {
                        $sMessage = "<br /><p><strong>Database is not structurally complete.</strong></p>";
                        $sMessage .= "<p>Missing tables are: <strong>" . implode("</strong>, <strong>", $aMissingTables) . "</strong></p>";
                        $sMessage .= "<p>You will find the SQL definition of Baïkal tables in this file: <strong>Core/Resources/Db/MySQL/db.sql</strong></p>";
                        $sMessage .= "<br /><p>Nothing has been saved. <strong>Please, add these tables to the database before pursuing Baïkal initialization.</strong></p>";

                        $oForm->declareError(
                            $oMorpho->element("mysql"),
                            $sMessage
                        );
                    } else {
                        # All tables are missing
                        # We add these tables ourselves to the database, to initialize Baïkal
                        $sSqlDefinition = file_get_contents(PROJECT_PATH_CORERESOURCES . "Db/MySQL/db.sql");
                        $oDb->query($sSqlDefinition);
                    }
                }

                return true;
            } catch (\Exception $e) {
                $oForm->declareError($oMorpho->element("mysql"),
                    "Baïkal was not able to establish a connexion to the MySQL database as configured.<br />MySQL says: " . $e->getMessage());
                $oForm->declareError($oMorpho->element("mysql_host"));
                $oForm->declareError($oMorpho->element("mysql_dbname"));
                $oForm->declareError($oMorpho->element("mysql_username"));
                $oForm->declareError($oMorpho->element("mysql_password"));
            }
        } else {
            $sFile = $oMorpho->element("sqlite_file")->value();

            try {
                # Asserting DB file is writable
                if (file_exists($sFile) && !is_writable($sFile)) {
                    $sMessage = "DB file is not writable. Please give write permissions on file <span style='font-family: monospace'>" . $sFile . "</span>";
                    $oForm->declareError($oMorpho->element("sqlite_file"), $sMessage);

                    return false;
                }
                # Asserting DB directory is writable
                if (!is_writable(dirname($sFile))) {
                    $sMessage = "The <em>FOLDER</em> containing the DB file is not writable, and it has to.<br />Please give write permissions on folder <span style='font-family: monospace'>" . dirname($sFile) . "</span>";
                    $oForm->declareError($oMorpho->element("sqlite_file"), $sMessage);

                    return false;
                }

                $oDb = new \Flake\Core\Database\Sqlite(
                    $sFile
                );

                if (($aMissingTables = \Baikal\Core\Tools::isDBStructurallyComplete($oDb)) !== true) {
                    # Checking if all tables are missing
                    $aRequiredTables = \Baikal\Core\Tools::getRequiredTablesList();
                    if (count($aRequiredTables) !== count($aMissingTables)) {
                        $sMessage = "<br /><p><strong>Database is not structurally complete.</strong></p>";
                        $sMessage .= "<p>Missing tables are: <strong>" . implode("</strong>, <strong>", $aMissingTables) . "</strong></p>";
                        $sMessage .= "<p>You will find the SQL definition of Baïkal tables in this file: <strong>Core/Resources/Db/SQLite/db.sql</strong></p>";
                        $sMessage .= "<br /><p>Nothing has been saved. <strong>Please, add these tables to the database before pursuing Baïkal initialization.</strong></p>";

                        $oForm->declareError(
                            $oMorpho->element("sqlite_file"),
                            $sMessage
                        );
                    } else {
                        # All tables are missing
                        # We add these tables ourselves to the database, to initialize Baïkal
                        $sSqlDefinition = file_get_contents(PROJECT_PATH_CORERESOURCES . "Db/SQLite/db.sql");
                        foreach (explode(';', $sSqlDefinition) as $query) {
                            if (!trim($query)) {
                                continue;
                            }
                            $oDb->query($query);
                        }
                    }
                }

                return true;
            } catch (\Exception $e) {
                $oForm->declareError(
                    $oMorpho->element("sqlite_file"),
                    "Baïkal was not able to establish a connexion to the SQLite database as configured.<br />SQLite says: " . $e->getMessage() . (string) $e
                );
            }
            // SQLite
        }
    }

    function hideMySQLFieldWhenNeeded(\Formal\Form $oForm, \Formal\Form\Morphology $oMorpho) {
        if ($oForm->submitted()) {
            $bMySQL = (intval($oForm->postValue("mysql")) === 1);
        } else {
            // oMorpho won't have the values from the model set on it yet
            $bMySQL = $this->oModel->get("mysql");
        }

        if ($bMySQL === true) {
            $oMorpho->remove("sqlite_file");
        } else {
            $oMorpho->remove("mysql_host");
            $oMorpho->remove("mysql_dbname");
            $oMorpho->remove("mysql_username");
            $oMorpho->remove("mysql_password");
        }
    }
}
