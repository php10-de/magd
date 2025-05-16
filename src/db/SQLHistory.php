<?php

class SQLHistory {
    protected $_con;

    protected $_currentSQLFile;

    public function __construct() {
        global $con;
        $this->_con = $con;
        $this->setSQLFile();
    }

    /**
     *
     */
    protected function setSQLFile() {
        $currentTicketFromBranch = $this->readBranch();
        if ($currentTicketFromBranch) {
            $this->_currentSQLFile = SQL_ROOT . $currentTicketFromBranch . '.sql.php';
            return;
        }
        $this->_currentSQLFile = SQL_ROOT . 'v' . HROSE_VERSION . '.sql.php';
    }

    /**
     * @return false|string
     */
    public function readBranch() {
        $cmd = "cd " . escapeshellarg(DOC_ROOT) ." && git rev-parse --abbrev-ref HEAD 2>&1";
        $output = _exec($cmd);
        preg_match('/' . JIRA_PROJECT_NAME . '-[0-9]+/', $output, $matches);
        if (count($matches)) {
            $this->_currentTicket = $matches[0];
            return $matches[0];
        }
        return false;
    }

    public function getSQLFile() {
        return $this->_currentSQLFile;
    }

    /**
     * @param string $addSql
     */
    public function writeHistory($addSql) {
        if (defined('HROSE_PHPUNIT_TEST_MODE')) {
            return;
        }
        $sql = [];
        $currentFile = $this->_currentSQLFile;
        if (file_exists($currentFile)) {
            require_once $currentFile;
        }
        if (!in_array($addSql, $sql)) {
            $sql[] = $addSql;
        }
        $fContent = '<?php
$hroseVersion = \'' . HROSE_VERSION . '\';

';
        foreach ($sql as $statement) {
            $fContent .= '$sql[] = \'' . addslashes($statement) . '\';
';
        }
        file_put_contents($currentFile, $fContent);
    }
}