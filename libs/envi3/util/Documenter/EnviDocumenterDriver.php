<?php
/**
 * EnviDocumenterで使用されるドライバ
 *
 *
 *
 * PHP versions 5
 *
 *
 * @category   MVC
 * @package    Envi3
 * @subpackage EnviMVCCore
 * @author     Akito <akito-artisan@five-foxes.com>
 * @copyright  2011-2014 Artisan Project
 * @license    http://opensource.org/licenses/BSD-2-Clause The BSD 2-Clause License
 * @version    GIT: $Id$
 * @link       https://github.com/EnviMVC/EnviMVC3PHP
 * @see        http://www.enviphp.net/
 * @since      File available since Release 3.3.3.5
 * @doc_ignore
 */

/**
 * EnviDocumenterで使用されるドライバ
 *
 * 各Markdownファイルへの書き出しドライバです
 *
 * @category   MVC
 * @package    Envi3
 * @subpackage EnviMVCCore
 * @author     Akito <akito-artisan@five-foxes.com>
 * @copyright  2011-2014 Artisan Project
 * @license    http://opensource.org/licenses/BSD-2-Clause The BSD 2-Clause License
 * @version    Release: @package_version@
 * @link       https://github.com/EnviMVC/EnviMVC3PHP
 * @see        http://www.enviphp.net/
 * @since      File available since Release 3.3.3.5
 * @doc_ignore
 */
class EnviDocumenterDriver
{
    private $template_dir;
    private $docs_dir;
    public function __construct($docs_dir = NULL, $template_dir = NULL)
    {
        $this->docs_dir     = dirname(dirname(dirname(dirname(dirname(__FILE__))))).'/document/man/';
        $this->template_dir = dirname(__FILE__).'/template/md_tpl/';
    }

    public function fileWriter($class_list, $token_list, EnviParserResult $cpr)
    {
        $write_path = $this->makeWritePath($cpr->getFileName()).'.md';

        $this->write(
            $write_path,
            $this->template_dir.'file.tpl',
            array(
                'file_name' => mb_ereg_replace('\.php$', '', basename($cpr->getFileName())),
                'class_list' => $class_list,
            )
        );

        $doc_comment = $this->getFileDocComment($token_list);
        $this->introWrite($doc_comment, $cpr);
    }
    /* ----------------------------------------- */


    public function classWriter($class_name, $token, $methods, $cpr)
    {
        $write_path = $this->makeWritePath($cpr->getFileName()).'/'.$class_name.'.md';
        $this->write(
            $write_path,
            $this->template_dir.'class.tpl',
            array(
                'file_name' => mb_ereg_replace('\.php$', '', basename($cpr->getFileName())),
                'class_name' => $class_name,
                'token' => $token,
                'methods' => $methods
            )
        );
    }


    public function methodWriter(&$method_list, $method_name)
    {
        $method = $method_list[$method_name];
        $class_name = $method['class_name'];
        $write_path = $this->makeWritePath($method['file_path']).'/'.$class_name.'/'.$method['token']->getName().'.md';
        $this->write(
            $write_path,
            $this->template_dir.'method.tpl',
            array(
                'file_name'   => $method['file_name'],
                'class_name'  => $class_name,
                'method_name'   => $method_name,
                'token'       => $method['token'],
                'method_list' => $method_list,
                'class_token'   => $method['class_token'],
                'file_doc_token'   => $method['file_doc_token'],
            )
        );
    }


    public function constantWriter($class_list, $cpr)
    {
        $write_path = $this->makeWritePath($cpr->getFileName()).'/constants.md';
        $this->write($write_path, $this->template_dir.'constants.tpl', array('class_list' => $class_list));
    }

    /**
     * +-- イントロを書き込み
     *
     * @access      protected
     * @param       var_text $values
     * @param       EnviParserResult $cpr
     * @return      void
     */
    protected function introWrite($values, EnviParserResult $cpr)
    {
        $write_path = $this->makeWritePath($cpr->getFileName()).'/intro.md';
        $this->write($write_path, $this->template_dir.'intro.tpl', $values);
    }
    /* ----------------------------------------- */

    /**
     * +-- 書き込みファイルパスを取得
     *
     * @access      public
     * @param       var_text $file_name
     * @return      void
     */
    public function makeWritePath($file_name)
    {
        return $this->docs_dir.mb_ereg_replace('^.*envi3/(.*)\.php$', '\1', $file_name);
    }
    /* ----------------------------------------- */


    protected function write($write_path, $tpl_path, $values)
    {
        ob_start();
        extract($values);
        include $tpl_path;
        $res = ob_get_contents();
        ob_end_clean();
        @mkdir(dirname($write_path), 0777, true);
        file_put_contents($write_path, $res);
    }



    private function getFileDocComment(&$token_list)
    {
        $res = array('doc_subject' => '', 'doc_detail' => '', );
        foreach ($token_list as $token) {
            if ($token->getTokenName() === 'DOC_COMMENT') {
                $res['doc_subject'] = $token->getDocBlockSubject();
                $res['doc_detail'] = str_replace('PHP versions 5', '', $token->getDocBlockDetail());
                return $res;
            }
        }
        return $res;
    }



}

