<?php
/**
 * フォームの処理
 *
 *
 * PHP versions 5
 *
 *
 * @category   MVC
 * @package    Envi3
 * @subpackage EnviMVCCore
 * @author     Akito <akito-artisan@five-foxes.com>
 * @copyright  2011-2013 Artisan Project
 * @license    http://opensource.org/licenses/BSD-2-Clause The BSD 2-Clause License
 * @version    GIT: $Id$
 * @link       https://github.com/EnviMVC/EnviMVC3PHP
 * @see        http://www.enviphp.net/
 * @since      File available since Release 1.0.0
 */


/**
 * フォームの処理
 *
 *
 * @package    Envi3
 * @category   MVC
 * @subpackage EnviMVCCore
 * @author     Akito <akito-artisan@five-foxes.com>
 * @copyright  2011-2013 Artisan Project
 * @license    http://opensource.org/licenses/BSD-2-Clause The BSD 2-Clause License
 * @version    Release: @package_version@
 * @link       https://github.com/EnviMVC/EnviMVC3PHP
 * @see        http://www.enviphp.net/
 * @since      Class available since Release 1.0.0
 */
class EnviForm
{
    private $system_conf;

    /**
     * コンストラクタ
     *
     * @return void
     */
    public function __construct(array $system_conf)
    {
        $this->system_conf = $system_conf;
    }

    /**
     * +-- モジュール、アクションに応じたコンフィグ情報を取得する
     *
     * @access      public
     * @return      array
     */
    public function getConfiguration()
    {
        if (!(isset($this->system_conf[EnviRequest::getThisModule()]) && isset($this->system_conf[EnviRequest::getThisModule()][EnviRequest::getThisAction()]))) {
            return array();
        }
        return $this->system_conf[EnviRequest::getThisModule()][EnviRequest::getThisAction()];
    }
    /* ----------------------------------------- */

    /**
     * +-- YMLに定義されたバリデーションを行う
     *
     * @access      public
     * @return      array|ValidatorError
     */
    public function validate()
    {
        $validate_conf = $this->getConfiguration();
        if (count($validate_conf) === 0) {
            return array();
        }

        $validate = validator();
        $i18n     = EnviRequest::getI18n();
        foreach ($validate_conf as $form_name => $conf) {
            $i = 0;
            $validate->autoExecute(
                isset($conf['name'][$i18n]) ? array($form_name => $conf['name'][$i18n]) : $form_name,
                $conf['validation'][$i]['type'],
                $conf['validation'][$i]['is_chain'],
                $conf['is_trim'],
                strpos($conf['method'], '|') ? 3 : stripos($conf['method'], 'POST') === 0 ? validator::METHOD_POST : validator::METHOD_GET,
                isset($conf['validation'][$i]['option']) ? $conf['validation'][$i]['option'] : false
            );
            if (isset($conf['validation'][$i]['error_message'][$i18n]) && !empty($conf['validation'][$i]['error_message'][$i18n])) {
                $validate->error()->setErrorList($form_name, $conf['validation'][$i]['type'], $conf['validation'][$i]['error_message'][$i18n]);
            }
            while(isset($conf['validation'][++$i])) {
                $validate->chain(
                    $form_name,
                    $conf['validation'][$i]['type'],
                    $conf['validation'][$i]['is_chain'],
                    isset($conf['validation'][$i]['option']) ? $conf['validation'][$i]['option'] : false
                );
                if (isset($conf['validation'][$i]['error_message'][$i18n]) && !empty($conf['validation'][$i]['error_message'][$i18n])) {
                    $validate->error()->setErrorList($form_name, $conf['validation'][$i]['type'], $conf['validation'][$i]['error_message'][$i18n]);
                }
            }
        }
        $res = $validate->autoExecute();
        $validate->free();
        return $res;
    }
    /* ----------------------------------------- */
}