<?php
/**
 * Created by PhpStorm.
 * User: Mark
 * Date: 4/17/18
 * Time: 10:37 AM
 */

namespace markpthomas\crawler;

use markpthomas\library as Lib;


abstract class ReportBase
    extends Lib\KeyValueMultiLineData
    implements Lib\IMySqlFill
{
    public $externalSiteStub;

    public $pageId;
    public $name;
    public $pageMenuName;
    public $pageURL;
    public $type;

    /**
     * @param bool $keepOldUrl True: The old URL from the crawled source is used. False: A new URL specified for the site is used.
     * @return Report
     */
    public abstract function toReport($keepOldUrl = false);

    /**
     * Inserts the corresponding crawler ID into the record of the provided page ID.
     * @param int $pageId Page ID for which the crawler's ID is to be inserted.
     * @param \PDO $mysqlPdo
     */
    public abstract function insertCrawlerId(\PDO $mysqlPdo, $pageId);

    /**
     * Matches pages by name to any crawled pages and associates the two via the crawler's ID.
     * @param \PDO $mysqlPdo
     */
    public static function associateCrawlerIdsWithPages(\PDO $mysqlPdo){
        // Override this in extended classes where applicable.
    }
} 