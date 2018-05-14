<?php
/**
 * Created by PhpStorm.
 * User: Mark
 * Date: 4/16/18
 * Time: 10:18 PM
 */

namespace markpthomas\library;

/**
 * Class MyPDOStatement <br />
 * Example output the query and the query with values inserted: <br />
 * var_dump( $query->queryString, $query->_debugQuery() ); <br />
 * @package markpthomas\library
 * @see https://stackoverflow.com/questions/7716785/get-last-executed-query-in-php-pdo
 */
class MyPDOStatement extends \PDOStatement
{
    // have a look at http://www.php.net/manual/en/pdo.constants.php
    public static function getOptions(){
        $className = get_called_class();
        return array(
            \PDO::ATTR_DEFAULT_FETCH_MODE => \PDO::FETCH_OBJ,
        \PDO::ATTR_ERRMODE => \PDO::ERRMODE_EXCEPTION,
//            \PDO::ATTR_ERRMODE => \PDO::ERRMODE_WARNING,
            \PDO::ATTR_STATEMENT_CLASS => array($className, array()),
        );
    }

    protected $_debugValues = null;

    protected function __construct()
    {
        // need this empty construct()!
    }

    public function execute($values=array())
    {
        $this->_debugValues = $values;
        try {
            $t = parent::execute($values);
            // maybe do some logging here?
        } catch (\PDOException $e) {
            // maybe do some logging here?
            MyLogger::log('Error code: ' . $e->getCode() . '<br />');
            MyLogger::log('Error description: ' . $e->getMessage() . '<br />');
            MyLogger::log(nl2br($e->getTraceAsString()) . '<br />');
            throw $e;
        }

        return $t;
    }

    public function _debugQuery($replaced=true)
    {
        $q = $this->queryString;

        if (!$replaced) {
            return $q;
        }

        // preg_replace_callback() parses the query for :placeholders and passes them to the callback function _debugReplace(),
        // which then returns the value bound to :placeholder.
        return preg_replace_callback('/:([0-9a-z_]+)/i', array($this, '_debugReplace'), $q);
    }

    protected function _debugReplace($m)
    {
        $v = $this->_debugValues[$m[0]];
        if ($v === null) {
            return "NULL";
        }
        if (!is_numeric($v)) {
            $v = str_replace("'", "''", $v);
        }

        return "'". $v ."'";
    }
}