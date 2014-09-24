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
        $validator->free();

        // エラーメッセージの変更
        $validator->error()->setUserErrorList ('noblank', _('{form}を正しく入力して下さい。'));
        $validator->error()->setUserErrorList ('maxwidth', _('{form}を正しく入力して下さい。'));
        $validator->error()->setUserErrorList ('integer', _('{form}を正しく入力して下さい。'));

        // バリデーション
        $validator->autoPrepare(array('id' => _('URL')), 'noblank', false, false, validator::METHOD_GET|validator::METHOD_POST);
        $validator->chain('id', 'maxwidth', false, 20);
        $validator->chain('id', 'integer', false);

        $res = $validator->executeAll();
        if ($validator->isError($res)) {
            return Envi::ERROR;
        }

        EnviRequest::setAttribute('id', $res['id']);
        $_____model_pascal_case_name_____ = _____model_pascal_case_name_____Peer::retrieveByPK($res['id']);


        if (!$_____model_pascal_case_name_____ instanceof _____model_pascal_case_name_____) {
            EnviRequest::setError('is_sign_on', 'id', 0, _('URLを正しく入力して下さい。'));
            return Envi::ERROR;
        }

        EnviRequest::setAttribute('_____model_pascal_case_name_____', $_____model_pascal_case_name_____->toArray());
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
/*%%attribute_text%%*/
        $this->Renderer()->setAttribute('_____module_name_____', EnviRequest::getAttribute('_____model_pascal_case_name_____'));
        $this->Renderer()->setAttribute('error', EnviRequest::getErrors());
        $this->Renderer()->display('edit.tpl');
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
        $this->execute();
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
        $this->Renderer()->setAttribute('error', EnviRequest::getErrors());
        $this->Renderer()->display('common_error.tpl');
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