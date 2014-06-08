<?php
/**
 * メールの送信
 *
 *
 * PHP versions 5
 *
 *
 * @category   MVC
 * @package    Envi3
 * @subpackage Mail
 * @author     Akito <akito-artisan@five-foxes.com>
 * @copyright  2011-2013 Artisan Project
 * @license    http://opensource.org/licenses/BSD-2-Clause The BSD 2-Clause License
 * @version    GIT: $Id$
 * @link       https://github.com/EnviMVC/EnviMVC3PHP
 * @see        http://www.enviphp.net/
 * @since      File available since Release 1.0.0
 */


/**
 * メールの送信
 *
 *
 * @package    Envi3
 * @category   MVC
 * @subpackage Mail
 * @author     Akito <akito-artisan@five-foxes.com>
 * @copyright  2011-2013 Artisan Project
 * @license    http://opensource.org/licenses/BSD-2-Clause The BSD 2-Clause License
 * @version    Release: @package_version@
 * @link       https://github.com/EnviMVC/EnviMVC3PHP
 * @see        http://www.enviphp.net/
 * @since      Class available since Release 1.0.0
 */
class EnviSendMail
{
    /**
     * 送信先の配列を格納
     *
     * @var string
     */
    public $to_array = array();

    /**
     * エラーメールの送信先
     *
     * @var string
     */
    public $error_to = '';

    /**
     * CC送信先の配列を格納
     *
     * @see pandaSendMail(),$_cc
     * @var array
     */
    public $cc_array = array();

    /**
     * BCC送信先の配列を格納
     *
     * @see pandaSendMail(),$_bcc
     * @var array
     */
    public $bcc_array = array();


    /**
     * キーワードヘッダーを格納
     *
     * @see $header
     * @var string
     */
    public $keyword = 'Envi MVC Sendmail';


    /**
     * 返信先 Reply-To:
     *
     * @var string
     */
    public $reply = '';

    /**
     * 送信元 from:
     *
     * @var string
     */
    public $_from = '';


    /**
     * 開発環境の場合の送信先
     *
     * false を指定すると本番と同じ動作（dev_sendに送らない）になります。
     *
     * @var string
     */
    public $dev_send = false;

    /**
     * ステージ環境の場合の送信先
     *
     * false を指定すると本番と同じ動作（stg_sendに送らない）になります。
     *
     * @var string
     */
    public $stg_send = false;

    /**
     * ステージ環境の場合の許可送信先
     *
     * @var string
     */
    public $stg_allow_send = array(
    );

    /**
     * 件名 subject:
     *
     * @var string
     */
    public $subject = '';

    /**
     * 本文 body
     *
     * setTextMsg()コールで生成されます。
     *
     * @see setBody(),$footerpath,$filepath
     * @var string
     */
    public $body = '';

    /**
     * 添付ファイル
     *
     * @see addAttachment(),getAttachment(),
     * @var array
     */
    public $attachment = array();

    /**
     * 添付ファイルがあるかどうか
     *
     * バイナリの配列を設定してください。
     *
     * @see sendMailSubmit(),pandaSendMail()
     * @var array
     */
    public $is_attachment = false;

    /**
     * メールの正規化を行うか
     *
     * ヘッダの不要な改行や、半角カナの全角化を行う
     *
     * @see sendMailSubmit(),pandaSendMail()
     * @var bool
     */
    public $auto_clean = true;

    /**
     * 区切り線
     *
     * 添付ファイルの区切り線
     *
     * @var string
     */
    public $boundary = '--------SendMimeMailClass';

    /**
     * ServerInfo()オブジェクトを格納
     *
     * @var object
     */
    private $_ServerInfo;


    /**
     * 送信先 TOを格納
     *
     * $to_arrayから、クラス内で自動生成されます。
     *
     * @see $to_array,sendMailSubmit()
     * @var string
     */
    private $_to;



    /**
     * 送信先 CCを格納
     *
     * $cc_arrayから、クラス内で自動生成されます。
     *
     * @see $cc_array,sendMailSubmit()
     * @var string
     */
    private $_cc;


    /**
     * 送信先 BCCを格納
     *
     * $bcc_arrayから、クラス内で自動生成されます。
     *
     * @see $bcc_array,sendMailSubmit()
     * @var string
     */
    private $_bcc = false;


    /**
     * headerを格納します
     *
     * クラス内で自動生成されます。
     *
     * @see sendMailSubmit()
     * @var string
     */
    private $_header;


    public $snap_shot;

    public $renderer;

    /**
     * コンストラクタ
     *
     * @return void
     */
    public function __construct($system_conf)
    {
        require_once $system_conf['SETTING']['renderer']['resource'];
        $this->renderer = new $system_conf['SETTING']['renderer']['class_name'];
        $this->dev_send = isset($system_conf['SETTING']['dev_send']) ? $system_conf['SETTING']['dev_send'] : false;
        $this->stg_send = isset($system_conf['SETTING']['stg_send']) ? $system_conf['SETTING']['stg_send'] : false;
        $this->stg_allow_send  = isset($system_conf['SETTING']['stg_allow_send']) ? $system_conf['SETTING']['stg_allow_send'] : NULL;
        $this->auto_clean = $system_conf['SETTING']['auto_clean'];
        if (isset($system_conf['SETTING']['from'])) {
            $this->setFrom(
                $system_conf['SETTING']['from']['email'],
                isset($system_conf['SETTING']['from']['name']) && $system_conf['SETTING']['from']['name'] ? $system_conf['SETTING']['from']['name'] : NULL
            );
        }
        $this->boundary = uniqid('--------EnviSendMail_1.0');
        $this->_ServerInfo = EnviServerStatus();
    }


    public function setSnapShot()
    {
        $this->_snap_shot = clone $this;
    }

    public function getSnapShot(){
        return $this->_snap_shot;
    }

    /**
     * メールの送信実行
     *
     * @return boolean
     */
    public function sendMailSubmit()
    {
        if (!$this->_makeHeader()) {
            return false;
        }

        if ($this->is_attachment) {
            //添付ありの場合
            $body="--".$this->boundary."\n"
                ."Content-Type: text/plain; charset=\"ISO-2022-JP\"\n"
                ."Content-Transfer-Encoding: 7bit\n"
                ."\n"
                .mb_convert_encoding($this->body, "JIS", "AUTO")
                ."\n";

            //添付する
            foreach ($this->attachment as $key => $value) {
                $body .= "--".$this->boundary."\n"
                    ."Content-Type: ".$value["type"]."; name=\""
                    .$value["file"]."\"\n"
                    ."Content-Transfer-Encoding: base64\n"
                    ."Content-Disposition: attachment; filename=\"".$value["file"]."\"\n\n"
                    .chunk_split(base64_encode($value["data"]))
                    ."\n";
                    $body.="--".$this->boundary."--\n";
            }

            $result = mail(
                $this->_to,
                mb_encode_mimeheader($this->subject),
                $body,
                $this->_header
            );
        } else {
            //添付無しの場合は、シンプルに
            $result = mb_send_mail(
                $this->_to,
                $this->subject,
                $this->body,
                $this->_header
            );
        }
        return $result;
    }


    /**
     * ヘッダを作成
     *
     * @return boolean
     */
    private function _makeHeader()
    {
        $is_dev = (ENVI_ENV === EnviServerStatus::DEVELOPER);
        $is_stg = (ENVI_ENV === EnviServerStatus::STAGE);
        if (!count((array)$this->to_array)) {
            return false;
        } elseif ($is_dev && $this->dev_send !== false) {
            //開発なら、開発用アドレスに
            $this->_to = $this->dev_send;
        } elseif ($is_stg && $this->stg_send !== false) {
            $loop = 1;
            foreach ($this->to_array as $value) {
                if (in_array($value, $this->stg_allow_send)) {
                    $to_array_tmp[] = $value;
                } else if ($loop == 1) {
                    $to_array_tmp[] = $this->stg_send;
                    $loop++;
                }
            }
            $this->to_array = $to_array_tmp;
            $this->_to = join(',', (array)$this->to_array);
        } else {
            $this->_to = join(',', (array)$this->to_array);
        }

        if (count((array)$this->cc_array)) {
            if ($is_dev && $this->dev_send !== false) {
                //開発なら、開発用アドレスに
                $this->_cc = $this->dev_send;
            } elseif ($is_stg && $this->stg_send !== false) {
                $cc_array_tmp[] = $this->stg_send;
                foreach ($this->cc_array as $value) {
                    if (in_array($value, $this->stg_allow_send)) {
                        $cc_array_tmp[] = $value;
                    }
                }
                $this->cc_array = $cc_array_tmp;
                $this->_cc = join(',', (array)$this->cc_array);

            } else {
                $this->_cc = join(',', (array)$this->cc_array);
            }
        } else {
            if ($is_stg && $this->stg_send !== false) {
                $this->_cc = $this->stg_send;
            }
        }

        if (count((array)$this->bcc_array)) {
            if ($is_dev && $this->dev_send !== false) {
                //開発なら、開発用アドレスに
                $this->_bcc = $this->dev_send;
            } elseif ($is_stg && $this->stg_send !== false) {
                $loop = 1;
                foreach ($this->bcc_array as $value) {
                    if (in_array($value, $this->stg_allow_send)) {
                        $bcc_array_tmp[] = $value;
                    } else if ($loop == 1) {
                        $bcc_array_tmp[] = $this->stg_send;
                        $loop++;
                    }
                }
                $this->bcc_array = $bcc_array_tmp;
                $this->_bcc = join(',', (array)$this->bcc_array);
            } else {
                $this->_bcc = join(',', (array)$this->bcc_array);
            }
        }

        //開発なら自動で件名に【DEV】をつける。
        if ($is_dev) {
            $this->subject = '【DEV】'.$this->subject;
        } elseif ($is_stg) {
            $this->subject = '【STG】'.$this->subject;
        }

        //メールをクリーンな内容にする
        if ($this->auto_clean) {
            $this->setString($this->_to);
            $this->setString($this->_cc);
            $this->setString($this->_bcc);
            $this->setString($this->reply);
            $this->setString($this->subject, true);
            $this->setString($this->keyword);
            $this->setString($this->_from);
            $this->setString($this->body, true, true);
        }
        $this->_header = "From: ".$this->_from
                        .($this->_cc == false ? '' : "\r\nCc: ".$this->_cc)
                        .($this->_bcc == false ? '' : "\r\nBcc: ".$this->_bcc)
                        .($this->reply == false ? '' : "\r\nReply-To: ".$this->reply)
                        .($this->keyword == false ? '' : "\r\nKeyword: ".$this->keyword);

        // 添付ファイル用のヘッダ
        if($this->is_attachment){
            $this->_header .="\r\nMIME-Version: 1.0";
            $this->_header .="\r\nContent-Type: multipart/mixed; boundary=\"".$this->boundary.'"';
        }
        return $this->_header;
    }


    /**
     * 置き換え機能付きbodyの作成
     *
     * 本文テンプレートを読み込む
     *
     * @param string $template
     * @return boolean
     */
    public function setBody($template)
    {
        $this->body = $this->renderer->displayRef($template, 'EnviSendMailClass', 'EnviSendMailClass');
    }

    /**
     * templateにtemplate変数を格納
     *
     * @param string $name
     * @param string $value
     */
    public function setAttribute($name, $value)
    {
        $this->renderer->setAttribute($name, $value);
    }

    /**
     * +-- templateにtemplate変数を配列を展開する形で追加
     *
     * @access      public
     * @param       array $values
     * @return      void
     */
    public function setAttributes(array $values)
    {
        foreach ($values as $name => $value) {
            $this->renderer->setAttribute($name, $value);
        }
    }
    /* ----------------------------------------- */

    /**
     * 送信者設定
     *
     * @param string $address アドレス
     * @param string $name 名前
     */
    public function setFrom($address, $name = NULL)
    {
        if ($name !== NULL) {
            $this->_from = mb_encode_mimeheader($name)."<{$address}>";
        } else {
            $this->_from = $address;
        }
    }

    /**
     * キーワード設定
     *
     * @param string $keyword キーワード
     */
    public function setKeyword($keyword)
    {
        $this->keyword = $keyword;
    }

    /**
     * 件名設定
     *
     * @param string $subject 件名
     */
    public function setSubject($subject)
    {
        $this->subject = $subject;
    }

    /**
     * 宛先設定
     *
     * @param string $address アドレス
     * @param string $name 名前
     */
    public function setTo($address, $name = NULL)
    {
        if (is_array($address)) {
            $this->to_array = (array)$this->to_array + $address;
        } elseif ($name !== NULL) {
            $this->to_array[] = mb_encode_mimeheader($name)."<{$address}>";
        } else {
            $this->to_array[] = $address;
        }
    }

    /**
     * Cc設定
     *
     * @param string $address アドレス
     * @param string $name 名前
     */
    public function setCc($address, $name=NULL)
    {
        if (is_array($address)) {
            $this->cc_array = (array)$this->cc_array + $address;
        } elseif ($name !== NULL) {
            $this->cc_array[] = mb_encode_mimeheader($name)."<{$address}>";
        } else {
            $this->cc_array[] = $address;
        }
    }

    /**
     * Bcc設定
     *
     * @param string $address アドレス
     * @param string $name 名前
     */
    public function setBcc($address, $name = NULL)
    {
        if (is_array($address)) {
            $this->bcc_array = (array)$this->bcc_array + $address;
        } elseif ($name !== NULL) {
            $this->bcc_array[] = mb_encode_mimeheader($name)."<{$address}>";
        } else {
            $this->bcc_array[] = $address;
        }
    }


    /**
     * +-- メールの内容を整える
     *
     * メールの内容を整える
     *
     * @access public
     * @param string $msg
     * @param boolean $is_kana OPTIONAL:false
     * @param boolean $isPre OPTIONAL:false
     * @return string
     */
    public function setString(&$msg, $is_kana = false, $isPre = false)
    {
        if (!$isPre) {
            $msg = str_replace(array("\n", "\r"), '', $msg);
        } elseif($is_kana) {
            $msg = mb_convert_kana($msg, 'KV');
        }
        return $msg;
    }


    /**
     * 添付する
     *
     * @param string $data 添付データ
     * @param string $Filename 添付ファイルの名前
     * @param string $Type mimeヘッダ
     * @return void
     */
    public function addAttachment(&$data, $Filename = '', $Type = 'application/octet-stream')
    {
        $this->is_attachment = TRUE;
        if($Filename === ''){
            $Filename = uniqid('at');
        }
        $s = count($this->attachment);
        $this->attachment[$s]['data'] = $data;
        $this->attachment[$s]['file'] = $Filename;
        $this->attachment[$s]['type'] = $Type;
    }


    /**
     * ファイルを読み込んで添付する
     *
     * @param string $file 添付データ
     * @param string $rename 添付ファイルの名前
     * @param string $Type mimeヘッダ
     */
    public function setAttachment($file, $rename = false, $Type = 'application/octet-stream')
    {
        $data = file_get_contents($file);
        $this->addAttachment(
            $data,
            ($rename !== false ? $rename : basename($file)),
            $Type
        );
    }


    /**
     * エラーメール送信処理
     *
     * エラーメールを送信します。
     *
     * @param bool $isError trueの場合にのみメールを送信する
     * @return void
     */
    public function sendmailError($isError = true)
    {
        if ($isError) {
            $body = "以下の内容のメールを送信しようとしましたが、エラーの為送信出来ませんでした。\r\n"
                        ."宛先:".$this->_to."\r\n"
                        ."送信者:".$this->_from."\r\n"
                        ."CC:".$this->_cc."\r\n"
                        ."BCC:".$this->_bcc."\r\n"
                        ."件名:".$this->subject."\r\n\r\n"
                        ."ヘッダ一覧\r\n\r\n".$this->_header."\r\n\r\n"
                        .mb_strimwidth($this->body, 0, 12000, "・・・・長いメールの為本文の一部を省略しました。")
                        ."\r\n\r\n--------\r\n添付ファイル:".($this->is_attachment ? "あり" : "なし");
            $is_dev = ($this->_ServerInfo->getServerStatus() === EnviServerStatus::DEVELOPER);
            $is_stg = ($this->_ServerInfo->getServerStatus() === EnviServerStatus::STAGE);

            $subject = "EnviSendMail Error Report";

            if ($is_dev) {
                $subject = "【DEV】".$subject;
            } elseif ($is_stg) {
                $this->subject = "【STG】".$this->subject;
            }

            mb_send_mail($this->error_to, $subject, $body, 'from: '.$this->_from);
        }
    }
}
