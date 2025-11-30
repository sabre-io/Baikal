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

namespace BaikalAdmin\Controller\Settings;

use Symfony\Component\Yaml\Yaml;

class Database extends \Flake\Core\Controller {
    /**
     * @var \Baikal\Model\Config\Database
     */
    private $oModel;

    /**
     * @var \Formal\Form
     */
    private $oForm;

    function execute() {
        $this->oModel = new \Baikal\Model\Config\Database();

        # Assert that config file is writable
        if (!$this->oModel->writable()) {
            throw new \Exception("Config file is not writable;" . __FILE__ . " > " . __LINE__);
        }

        $this->oForm = $this->oModel->formForThisModelInstance([
            "close"           => false,
            "hook.morphology" => [$this, "morphologyHook"],
            "hook.validation" => [$this, "validationHook"],
        ]);

        if ($this->oForm->submitted()) {
            $this->oForm->execute();
        }
    }

    function render() {
        $oView = new \BaikalAdmin\View\Settings\Database();
        $oView->setData("message", \Formal\Core\Message::notice(
            "Do not change anything on this page unless you really know what you are doing.<br />You might break Baïkal if you misconfigure something here.",
            "Warning !",
            false
        ));

        $oView->setData("form", $this->oForm->render());

        return $oView->render();
    }

    function morphologyHook(\Formal\Form $oForm, \Formal\Form\Morphology $oMorpho) {
        if ($oForm->submitted()) {
            $bMySQL = ($oForm->postValue("backend") == 'mysql');
            $bPgSQL = ($oForm->postValue("backend") == 'pgsql');
        } else {
            try {
                $config = Yaml::parseFile(PROJECT_PATH_CONFIG . "baikal.yaml");
            } catch (\Exception $e) {
                error_log('Error reading baikal.yaml file : ' . $e->getMessage());
            }

            # Config key 'mysql' kept for backwards compatibility
            if (key_exists('mysql', $config['database'])) {
                $bMySQL = $config['database']['mysql'];
                $bPgSQL = false;

                $this->oModel->set('backend', $bMySQL ? 'mysql' : 'sqlite');
            } else {
                $bMySQL = $config['database']['backend'] == 'mysql';
                $bPgSQL = $config['database']['backend'] == 'pgsql';
            }
        }

        if ($bMySQL === true || $bPgSQL === true) {
            $oMorpho->remove("sqlite_file");
        }

        if (!$bMySQL) {
            $oMorpho->remove("mysql_host");
            $oMorpho->remove("mysql_dbname");
            $oMorpho->remove("mysql_username");
            $oMorpho->remove("mysql_password");
            $oMorpho->remove("mysql_ca_cert");
        }

        if (!$bPgSQL) {
            $oMorpho->remove("pgsql_host");
            $oMorpho->remove("pgsql_dbname");
            $oMorpho->remove("pgsql_username");
            $oMorpho->remove("pgsql_password");
        }
    }

    function validationHook(\Formal\Form $oForm, \Formal\Form\Morphology $oMorpho) {
        if ($oForm->refreshed()) {
            return true;
        }
        if ($oForm->modelInstance()->get("backend") == 'mysql' || $oForm->modelInstance()->get("backend") == 'pgsql') {
            $dbBackendName = $oForm->modelInstance()->get("backend") == 'pgsql' ? 'PostgreSQL' : 'MySQL';
            $dbBackendPrefix = $oForm->modelInstance()->get("backend");

            # We have to check the MySQL or PostgreSQL connection
            $sHost = $oForm->modelInstance()->get("{$dbBackendPrefix}_host");
            $sDbName = $oForm->modelInstance()->get("{$dbBackendPrefix}_dbname");
            $sUsername = $oForm->modelInstance()->get("{$dbBackendPrefix}_username");
            $sPassword = $oForm->modelInstance()->get("{$dbBackendPrefix}_password");
            $sCaCert = $oForm->modelInstance()->get("{$dbBackendPrefix}_ca_cert");

            try {
                $oDB = (($oForm->modelInstance()->get("backend")) == 'pgsql'
                ) ? new \Flake\Core\Database\Pgsql(
                    $sHost,
                    $sDbName,
                    $sUsername,
                    $sPassword
                ) : new \Flake\Core\Database\Mysql(
                    $sHost,
                    $sDbName,
                    $sUsername,
                    $sPassword,
                    $sCaCert
                );
            } catch (\Exception $e) {
                $sMessage = "<strong>{$dbBackendName} error:</strong> " . $e->getMessage();
                $sMessage .= "<br /><strong>Nothing has been saved</strong>";
                $oForm->declareError($oMorpho->element("{$dbBackendPrefix}_host"), $sMessage);
                $oForm->declareError($oMorpho->element("{$dbBackendPrefix}_dbname"));
                $oForm->declareError($oMorpho->element("{$dbBackendPrefix}_username"));
                $oForm->declareError($oMorpho->element("{$dbBackendPrefix}_password"));
                $oForm->declareError($oMorpho->element("{$dbBackendPrefix}_ca_cert"));

                return;
            }

            if (($aMissingTables = \Baikal\Core\Tools::isDBStructurallyComplete($oDB)) !== true) {
                $sMessage = "<strong>{$dbBackendName} error:</strong> These tables, required by Baïkal, are missing: <strong>" . implode(", ", $aMissingTables) . "</strong><br />";
                $sMessage .= "You may want create these tables using the file <strong>Core/Resources/Db/{$dbBackendName}/db.sql</strong>";
                $sMessage .= "<br /><br /><strong>Nothing has been saved</strong>";

                $oForm->declareError($oMorpho->element("backend"), $sMessage);

                return;
            }
        } else {
            $sFile = $oMorpho->element("sqlite_file")->value();

            try {
                # Asserting DB file is writable
                if (file_exists($sFile) && !is_writable($sFile)) {
                    $sMessage = "DB file is not writable. Please give write permissions on file <span style='font-family: monospace'>" . $sFile . "</span>";
                    $oForm->declareError($oMorpho->element("sqlite_file"), $sMessage);

                    return;
                }
                # Asserting DB directory is writable
                if (!is_writable(dirname($sFile))) {
                    $sMessage = "The <em>FOLDER</em> containing the DB file is not writable, and it has to.<br />Please give write permissions on folder <span style='font-family: monospace'>" . dirname($sFile) . "</span>";
                    $oForm->declareError($oMorpho->element("sqlite_file"), $sMessage);

                    return;
                }

                $oDb = new \Flake\Core\Database\Sqlite(
                    $sFile
                );

                if (($aMissingTables = \Baikal\Core\Tools::isDBStructurallyComplete($oDb)) !== true) {
                    $sMessage = "<br /><p><strong>Database is not structurally complete.</strong></p>";
                    $sMessage .= "<p>Missing tables are: <strong>" . implode("</strong>, <strong>", $aMissingTables) . "</strong></p>";
                    $sMessage .= "<p>You will find the SQL definition of Baïkal tables in this file: <strong>Core/Resources/Db/SQLite/db.sql</strong></p>";
                    $sMessage .= "<br /><p>Nothing has been saved. <strong>Please, add these tables to the database before pursuing Baïkal initialization.</strong></p>";

                    $oForm->declareError(
                        $oMorpho->element("sqlite_file"),
                        $sMessage
                    );
                }

                return;
            } catch (\Exception $e) {
                $oForm->declareError(
                    $oMorpho->element("sqlite_file"),
                    "Baïkal was not able to establish a connection to the SQLite database as configured.<br />SQLite says: " . $e->getMessage() . (string) $e
                );
            }
        }
    }
}
