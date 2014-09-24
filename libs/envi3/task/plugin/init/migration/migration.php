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
class ___class_name___ extends EnviMigrationBase
{


    public function __construct()
    {
        $project_dir = dirname(dirname(dirname(__FILE__))).DIRECTORY_SEPARATOR;

        $this->databases_yml = $project_dir.'config'.DIRECTORY_SEPARATOR.'___app_name____databases.yml';

        $this->env = trim(file_get_contents(dirname(dirname(__FILE__)).DIRECTORY_SEPARATOR.'env'));

        $this->instance_name = 'default_master';
    }



    public function change()
    {

    }
    public function up()
    {

    }
    public function down()
    {

    }

}
