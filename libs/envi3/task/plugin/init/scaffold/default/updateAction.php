<?php
/*%%dao_use%%*/
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
 * @doc_ignore
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
 * @doc_ignore
 */
class _____action_name_____Action extends _____module_name_____Actions
{


    /**
     * +-- 一番初めに呼ばれる、メソッド
     *
     *
     * @return bool falseを返すと、そこで処理が止まります。
     */
    public function initialize()
    {
        return true;
    }
    /* ----------------------------------------- */


    /**
     * +--Viewに移る前に実行される処理。Killされない限りは、NONEやfalseを返しても実行される
     *
     *
     * @return boolean
     */
    public function shutdown()
    {
        return true;
    }
    /* ----------------------------------------- */


    /**
     * +-- バリデートする
     *
     * バリデータを通して、処理を分岐させる。
     *
     * @return Envi::DEFAULT_ACCESS | Envi::ERROR | Envi::SUCCESS | boolean
     */
    public function validate()
    {
        $validator = validator();
        if($_SERVER['REQUEST_METHOD'] !== 'POST') {
            return Envi::DEFAULT_ACCESS;
        }
        if (!EnviRequest::hasParameter('commit') && !EnviRequest::hasParameter('confirm')) {
            return Envi::DEFAULT_ACCESS;
        }

        // バリデーション
        $validator->autoPrepare(array('id' => _('URL')), 'noblank', false, false, validator::METHOD_GET|validator::METHOD_POST);
        $validator->chain('id', 'maxwidth', false, 20);
        $validator->chain('id', 'integer', false);
        $res = $validator->executeAll();
        if ($validator->isError($res)) {
            EnviRequest::setAttribute('id_error', true);
            return Envi::ERROR;
        }
        $id = $res['id'];
        EnviRequest::setAttribute('id', $id);
        $validator->free();

/*%%validate_text%%*/


        $input_data = $validator->executeAll();
        if ($validator->isError($input_data)) {
            return Envi::ERROR;
        }

/*%%add_input_data_text%%*/


        EnviRequest::setAttribute('input_data', $input_data);

/*%%validate_unique_check_update_text%%*/


        if (EnviRequest::hasParameter('confirm')) {
            return Envi::DEFAULT_ACCESS;
        }
        return Envi::SUCCESS;
    }
    /* ----------------------------------------- */


    /**
     * +-- validate()でEnvi::SUCCESSもしくはtrueが返った場合の処理。
     *
     * @see validate()
     * @return Envi::DEFAULT_ACCESS | Envi::ERROR | Envi::SUCCESS  | Envi::NONE | boolean
     */
    public function execute()
    {
        $input_data = EnviRequest::getAttribute('input_data');
        $_____model_pascal_case_name_____  = _____model_pascal_case_name_____Peer::retrieveByPK(EnviRequest::getAttribute('id'));
        if (!$_____model_pascal_case_name_____ instanceof $_____model_pascal_case_name_____) {
            EnviController::killBy404Error();
        }


/*%%setter_text%%*/

        $_____model_pascal_case_name_____->save();

        EnviController::redirect('./show.php?commit=t&id='.$_____model_pascal_case_name_____->getId());
    }
    /* ----------------------------------------- */


    /**
     * +--validate()でEnvi::DEFAULT_ACCESSが返った場合の処理。
     *
     *
     * @see validate()
     * @return Envi::DEFAULT_ACCESS | Envi::ERROR | Envi::SUCCESS | boolean
     */
    public function defaultAccess()
    {
        $input_data = EnviRequest::getAttribute('input_data');
        $this->Renderer()->setAttribute('input_data', $input_data);
        if (EnviRequest::hasParameter('confirm')) {
            $this->Renderer()->display('edit_confirm.tpl');
            return Envi::NONE;
        }

        if (!EnviRequest::hasParameter('commit')) {
            EnviRequest::setAttribute('commit', 'update');
            EnviController::forward('show');
            return Envi::NONE;
        }
        EnviController::forward('edit');
    }
    /* ----------------------------------------- */

    /**
     * +--validate()でEnvi::ERRORもしくはfalseが返った場合の処理。
     *
     *
     * @see validate()
     * @return Envi::DEFAULT_ACCESS | Envi::ERROR | Envi::SUCCESS | boolean
     */
    public function handleError()
    {
        EnviController::forward('edit');
    }
    /* ----------------------------------------- */

    /**
     * +--セキュアなページかどうか。
     *
     *
     * @return boolean
     */
    public function isSecure()
    {
        return false;
    }
    /* ----------------------------------------- */

    /**
     * +--Controllerから直接呼ばれるアクションかどうか？
     *
     *
     * @return boolean
     */
    public function isPrivate()
    {
        return false;
    }
    /* ----------------------------------------- */

    /**
     * +--SSLでのみアクセスされるページかどうか？
     *
     *
     * @return boolean
     */
    public function isSSL()
    {
        return false;
    }
    /* ----------------------------------------- */

}