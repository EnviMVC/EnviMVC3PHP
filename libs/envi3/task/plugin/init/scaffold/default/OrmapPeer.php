<?php
/**
 *
 *
 * PHP versions 5
 *
 *
 * @category   %%project_category%%
 * @package    %%project_name%%
 * @subpackage
 * @author     %%your_name%% <%%your_email%%>
 * @copyright  %%your_project%%
 * @license    %%your_license%%
 * @version    GIT: $Id$
 * @link       %%your_link%%
 * @see        %%your_see%%
 * @since      File available since Release 1.0.0
 */

/**
 *
 *
 * @category   %%project_category%%
 * @package    %%project_name%%
 * @subpackage
 * @author     %%your_name%% <%%your_email%%>
 * @copyright  %%your_project%%
 * @license    %%your_license%%
 * @version    Release: @package_version@
 * @link       %%your_link%%
 * @see        %%your_see%%
 * @since      Class available since Release 1.0.0
 */
class _____modle_pascal_case_name_____Peer extends Base_____modle_pascal_case_name_____Peer
{
    protected static $queries = array(
        'get_all'    => 'SELECT * FROM __TABLE__',
    );

    /**
     * +-- データの一覧を配列で返す
     *
     * @access      public
     * @static
     * @param       var_text $con OPTIONAL:NULL
     * @return      array
     */
    public static function getAllByArray($con = NULL)
    {
        $bind_array = array();
        $suffix     = '';
        $query_key = 'get_all';

        $dbi = $con ? $con : extension()->DBI()->getInstance('default_master');
        $sql = self::getReplacedSQL(self::$queries[$query_key], $bind_array, $suffix);
        $res = $dbi->getAll($sql, $bind_array);
        return $res;
    }
    /* ----------------------------------------- */


}