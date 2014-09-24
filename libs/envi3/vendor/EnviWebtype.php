<?php
/**
 * リクエストユーザーの情報を解析
 *
 * リクエスト元のブラウザ情報や、ホスト情報を解析して、使用端末情報を取得します。
 *
 *
 *
 * PHP versions 5
 *
 *
 * @category   EnviMVC拡張
 * @package    EnviPHPが用意するエクステンション
 * @subpackage WebType
 * @author     Akito <akito-artisan@five-foxes.com>
 * @copyright  2011-2013 Artisan Project
 * @license    http://opensource.org/licenses/BSD-2-Clause The BSD 2-Clause License
 * @version    GIT: $Id$
 * @link       https://github.com/EnviMVC/EnviMVC3PHP
 * @see        http://www.enviphp.net/
 * @since      Class available since Release 1.0.0
 */


/**
 * ユーザーのブラウザ情報を解析
 *
 * リクエスト元のブラウザ情報や、ホスト情報を解析して、使用端末情報を取得します。
 *
 * @category   EnviMVC拡張
 * @package    EnviPHPが用意するエクステンション
 * @subpackage WebType
 * @author     Akito <akito-artisan@five-foxes.com>
 * @copyright  2011-2013 Artisan Project
 * @license    http://opensource.org/licenses/BSD-2-Clause The BSD 2-Clause License
 * @version    Release: @package_version@
 * @link       https://github.com/EnviMVC/EnviMVC3PHP
 * @see        http://www.enviphp.net/
 * @since      Class available since Release 1.0.0
 */
class EnviWebType
{
    const PC =      'pc';
    const AU =      'au';
    const TUKA =     't';
    const IDO =      'ido';
    const VODAFONE = 'v';
    const DOCOMO =   'd';
    const PHS =      'phs';
    const LMODE =    'l';
    const PSP =      'psp';
    const IPHONE =   'iPhone';
    const ANDROID =  'android';
    const OHTER =    'other';
    const DEBUG =    'debug';

    public $Envi_hdml_smaf_map;
    public $Envi_hdml_aud_map;
    public $Envi_hdml_grf_map;
    public $Envi_lmode_smaf_map;
    public $Envi_lmode_grf_map;
    public $_cash = array();
    public $remote_host;

    /**
     * +-- コンストラクタ
     *
     * @access public
     * @return void
     */
    public function __construct($system_conf)
    {
        //TUKAデバイスマップ
        $this->Envi_hdml_smaf_map = array('HI11' => 4, 'KC11' => 4, 'HI12' => 4, 'KC12' => 4, 'KCT4' => 4, 'KCT5' => 4, 'KCT6' => 4, 'KCT7' => 4,
                        'SN12' => 16, 'SN14' => 16, 'SN13' => 16, 'HI13' => 16, 'SN15' => 16, 'SN16' => 16, 'KC13' => 16,
                        'HI14' => 16, 'CA14' => 16, 'SN17' => 16, 'HI21' => 16, 'TST3' => 16, 'KCT8' => 16, 'TST4' => 16,
                        'MIT1' => 16, 'SYT3' => 16, 'MAT3' => 16, 'CN17' => 16, 'ST13' => 16, 'KC14' => 40, 'TST5' => 40,
                        'KCT9' => 40, 'TST6' => 40, 'KC15' => 40, 'ST14' => 40, 'KCTA' => 40, 'KCTB' => 16, 'KCTC' => 40,
                        'SYT4' => 40, 'TST7' => 40, 'TST8' => 40, 'KCTD' => 40, 'SYT5' => 40, 'KCU1' => 40
        );
        $this->Envi_hdml_aud_map = array('SN12' => 1, 'SN14' => 1, 'SN13' => 1, 'SN15' => 1, 'SN16' => 1, 'SN17' => 1, 'SN21' => 1, 'SN22' => 1,
                        'CA14' => 1, 'TS23' => 1, 'TST3' => 1, 'TST4' => 1, 'TST5' => 1, 'TST6' => 1, 'SA22' => 1,
                        'ST13' => 1, 'ST14' => 1, 'MIT1' => 1, 'SYT3' => 1, 'SN22' => 1, 'SYT4' => 1, 'TST7' => 1,
                        'TST8' => 1, 'KCTD' => 1, 'SYT5' => 1, 'KCU1' => 1, 'test' => 1
        );
        $this->Envi_hdml_grf_map = array('HI21' => 1, 'HI14' => 1, 'TST6' => 1, 'ST13' => 1,
                        'ST14' => 1, 'KC15' => 1, 'SYT3' => 1, 'TST5' => 1,
                        'KCTA' => 1, 'KCTC' => 1, 'SYT4' => 1, 'TST7' => 1,
                        'TST8' => 1, 'KCTD' => 1, 'SYT5' => 1, 'KCU1' => 1
        );

        //L-modeデバイスマップ
        $this->Envi_lmode_smaf_map = array('NTT4955595094544-000' => 16, 'NTT4955595098269-000' => 16, 'NTT4955595091253-000' => 16,
            'NTT4955595091314-000' => 16, 'NTT4955595095749-000' => 16, 'NTT4955595094513-000' => 16, 'NTT4955595094476-000' => 16,
            'NTT4955595094391-000' => 16, 'NTT4955595097675-000' => 16, 'NTT4955595097255-000' => 16, 'CANSL70' => 16, 'CANSL50' => 16,
            'CANVL20' => 16, 'CANVL10' => 16, 'CANVL1' => 16, 'SHAUXWB10' => 16, 'SHAUX-W71' => 16, 'SHAUXW-70' => 16, 'PCCTF-LS710' => 16,
            'PCCTF-LS700' => 16, 'PCCTF-LS500' => 16, 'PCCTF-LP900F' => 16, 'PCCTF-LP800F' => 16, 'MGCUFL3' => 16, 'MGCUFL1' => 16,
            'MGCKXL6' => 16, 'KMEKXL5' => 16
        );

        $this->Envi_lmode_grf_map = array('NTT4955595094544-000' => 1, 'NTT4955595098269-000' => 1, 'NTT4955595095749-000' => 1,
            'NTT4955595094513-000' => 1, 'NTT4955595094476-000' => 1, 'NTT4955595094391-000' => 1, 'NTT4955595097675-000' => 1,
            'NTT4955595097255-000' => 1, 'CANSL70' => 1, 'CANSL50' => 1, 'CANVL20' => 1, 'CANVL10' => 1, 'CANVL1' => 1, 'SHAUXWB10' => 1, 'SHAUX-W71' => 1,
            'SHAUX-W70' => 1, 'SHAUXW51' => 1, 'MGCUFL3' => 1, 'MGCUFL1' => 1, 'MGCKXL6' => 1, 'KMEKXL5' => 1
        );
    }

    /**
     * +-- リクエストユーザーのホスト名を取得する
     *
     * @access      public
     * @return      string
     */
    public function getUserRemoteHost()
    {
        if (!$this->remote_host) {
            $this->remote_host = @gethostbyaddr($_SERVER['REMOTE_ADDR']);
        }
        return $this->remote_host;
    }
    /* ----------------------------------------- */

    /**
     * +-- UserAgentから、OSやブラウザ情報を取得します
     *
     * UserAgentから、OSやブラウザ情報を取得します。
     * 引数が省略された場合は、リクエストされたユーザーのUserAgentが自動的に参照されます。
     *
     * @access      public
     * @param       string $user_agent OPTIONAL:NULL
     * @return      array
     */
    public function getUserInfo($user_agent = NULL)
    {
        $user_agent = $user_agent ? $user_agent : $_SERVER['HTTP_USER_AGENT'];
        if (isset($this->_cash['getUserInfo'][$user_agent])) {
            return $this->_cash['getUserInfo'][$user_agent];
        }
        if (preg_match('/DreamPassport/', $user_agent)) {
            $blo['os'] = 'DreamFlyer';
        } elseif (preg_match("/Android *(\d)/i", $user_agent, $matches)) {
            $blo['os'] = "Android $matches[1]";
        } elseif (preg_match("/iPod touch/", $user_agent)) {
            $blo['os'] = 'iPod touch';
        } elseif (preg_match("/iPhone/", $user_agent)) {
            $blo['os'] = 'iPhone';
        } elseif (preg_match("/iPad/", $user_agent)) {
            $blo['os'] = 'iPad';
        } elseif (preg_match("/Win 9x 4.90/", $user_agent)) {
            $blo['os'] = 'Windows Me';
        } elseif (preg_match("/Windows 98/", $user_agent)) {
            $blo['os'] = 'Windows 98';
        } elseif (preg_match("/Win98/", $user_agent)) {
            $blo['os'] = 'Windows 98';
        } elseif (preg_match("/Windows 95/", $user_agent)) {
            $blo['os'] = 'Windows 95';
        } elseif (preg_match("/Win95/", $user_agent)) {
            $blo['os'] = 'Windows 95';
        } elseif (preg_match("/Windows XP/", $user_agent)) {
            $blo['os'] = 'Windows XP';
        } elseif (preg_match("/Windows NT 5.1/", $user_agent)) {
            $blo['os'] = 'Windows XP';
        } elseif (preg_match("/Windows 2000/", $user_agent)) {
            $blo['os'] = 'Windows 2000';
        } elseif (preg_match("/Windows NT 5/", $user_agent)) {
            $blo['os'] = 'Windows 2000';
        } elseif (preg_match("/Windows NT 6.0/", $user_agent)) {
            $blo['os'] = 'Windows Vista';
        } elseif (preg_match("/Windows NT 6.1/", $user_agent)) {
            $blo['os'] = 'Windows 7';
        } elseif (preg_match("/Windows NT 6.2; ARM; Trident/6.0/", $user_agent)) {
            $blo['os'] = 'Windows RT';
        } elseif (preg_match("/Windows NT 6.2/", $user_agent)) {
            $blo['os'] = 'Windows 8';
        } elseif (preg_match("/Windows NT/", $user_agent)) {
            $blo['os'] = 'Windows NT';
        } elseif (preg_match("/WinNT/", $user_agent)) {
            $blo['os'] = 'Windows NT';
        } elseif (preg_match("/Windows CE/", $user_agent)) {
            $blo['os'] = 'Windows CE';
        } elseif (preg_match("/Borg/", $user_agent)) {
            $blo['os'] = 'Windows (Win32)';
        } elseif (preg_match("/Win32/", $user_agent)) {
            $blo['os'] = 'Windows (Win32)';
        } elseif (preg_match("/Mac OS X/", $user_agent)) {
            $blo['os'] = 'Mac OS X';
        } elseif (preg_match("/Mac_PowerPC/", $user_agent)) {
            $blo['os'] = 'Macintosh(PowerPC)';
        } elseif (preg_match("/Macintosh/", $user_agent)) {
            $blo['os'] = 'Macintosh';
        } elseif (preg_match("/SunOS/", $user_agent)) {
            $blo['os'] = 'SunOS';
        } elseif (preg_match("/FreeBSD/", $user_agent)) {
            $blo['os'] = 'FreeBSD';
        } elseif (preg_match("/Kondara/", $user_agent)) {
            $blo['os'] = 'Linux (Kondara)';
        } elseif (preg_match("/Vine/", $user_agent)) {
            $blo['os'] = 'Linux (Vine)';
        } elseif (preg_match("/Debian/", $user_agent)) {
            $blo['os'] = 'Linux (Debian)';
        } elseif (preg_match("/Laser5/", $user_agent)) {
            $blo['os'] = 'Linux (Laser5)';
        } elseif (preg_match("/Linux/", $user_agent)) {
            $blo['os'] = 'Linux (Others)';
        } elseif (preg_match("/X11/", $user_agent)) {
            $blo['os'] = 'Others (X Window)';
        } elseif (preg_match("/OS\/2/", $user_agent)) {
            $blo['os'] = 'OS/2';
        } elseif (preg_match("/sharp pda browser/", $user_agent)) {
            $blo['os'] = 'Zaurus';
        } elseif (preg_match("/CASSIOPEIA/", $user_agent)) {
            $blo['os'] = 'CASSIOPEIA';
        } elseif (preg_match("/DoCoMo/", $user_agent)) {
            $blo['os'] = 'DoCoMo';
        } elseif (preg_match("/KDDI-CA21/", $user_agent)) {
            $blo['os'] = 'KDDI';
        } elseif (preg_match("/J-PHONE/", $user_agent)) {
            $blo['os'] = 'Vodafone';
        } elseif (preg_match("/Softbank/", $user_agent)) {
            $blo['os'] = 'Softbank';
        } elseif (isset($_SERVER['HTTP_X_UP_SUBNO'])) {
            $blo['os'] = "AU";
        } elseif (preg_match("/UP.Browser/", $user_agent) && !isset($_SERVER['HTTP_X_UP_SUBNO'])) {
            $blo['os'] = 'Tu-Ka';
        } elseif (preg_match("/W3C_Validator/", $user_agent)) {
            $blo['os'] = 'W3C Validator';
        } elseif (preg_match("/W3C_CSS_Validator_JFouffa/", $user_agent)) {
            $blo['os'] = 'W3C CSS Validator JFouffa';
        } elseif (preg_match("/Another_HTML-lint/", $user_agent)) {
            $blo['os'] = 'Another HTML-lint';
        } else {
            if (preg_match("/Mozilla\/(\d)/i", $user_agent, $matches)) {
                $mv = $matches[1];
            if  (preg_match("/NetCaptor *(\d)/i", $user_agent, $matches)) {
                $blo['os'] = 'Unknown (NetCaptor)';
            } elseif (preg_match("/MSIE *(\d)/i", $user_agent, $matches)) {
                $blo['os'] = 'Unknown (IE)';
            } elseif (preg_match("/Konqueror\/(\d)/i", $user_agent, $matches)) {
                $blo['os'] = 'Others (KDE : Konqueror)';
            } else {
                $blo['os'] = 'Unknown (NN)';
            }
            } else {
                if (preg_match("/InternetNinja *(\d)/i", $user_agent, $matches)) {
                    $blo['os'] = 'Unknown (InternetNinja User)';
                } elseif (preg_match("/Sleipnir Version *(\d)/i", $user_agent, $matches)) {
                    $blo['os'] = 'Unknown (Sleipnir)';
                } elseif (preg_match("/Borg\/(\d)/i", $user_agent, $matches)) {
                    $blo['os'] = 'Unknown (AirWeb)';
                } elseif (preg_match("/WebAuto\/(\d)/i", $user_agent, $matches)) {
                    $blo['os'] = 'Unknown (WebAuto)';
                } elseif (preg_match("/Lynx\/(\d)/i", $user_agent, $matches)) {
                    $blo['os'] = 'Unknown (Lynx)';
                } elseif (preg_match("/InternetLinkAgent\/(\d)/i", $user_agent, $matches)) {
                    $blo['os'] = 'Unknown (InternetLinkAgent)';
                } elseif (preg_match("/W3CRobot\/(\d)/i", $user_agent, $matches)) {
                    $blo['os'] = 'Unknown (W3CRobot)';
                } elseif (preg_match("/Openbot\/(\d)/i", $user_agent, $matches)) {
                    $blo['os'] = 'Unknown (Openbot)';
                } elseif (preg_match("/Lunascape/i", $user_agent, $matches)) {
                    $blo['os'] = 'Unknown (Lunascape)';
                } elseif (preg_match("/CuserAgentm/i", $user_agent, $matches)) {
                    $blo['os'] = 'Unknown (CuserAgentm)';
                } elseif (preg_match("/Donut/", $user_agent, $matches)) {
                    $blo['os'] = 'Unknown (Donut)';
                } elseif (preg_match("/Moon Browser/", $user_agent, $matches)) {
                    $blo['os'] = 'Unknown (Moon Browser)';
                } elseif (preg_match("/w3m/", $user_agent, $matches)) {
                    $blo['os'] = 'Unknown (w3m)';
                } elseif (preg_match("/WWWC/", $user_agent, $matches)) {
                    $blo['os'] = 'Unknown (WWWC)';
                } elseif (preg_match("/Getweb!/", $user_agent, $matches)) {
                    $blo['os'] = 'Unknown (Getweb!)';
                } elseif (preg_match("/WorldTALK/", $user_agent, $matches)) {
                    $blo['os'] = 'Unknown (WorldTALK)';
                } elseif (preg_match("/NexTools WebAgent/", $user_agent, $matches)) {
                    $blo['os'] = 'Unknown (NexTools WebAgent)';
                } else {
                    $blo['os'] = 'Others';
                }
            }
        }
        if (preg_match("/Mozilla\/(\d)/i", $user_agent, $matches)) {
            $mv = $matches[1];
            if (preg_match("/DreamPassport\/(\d)/i", $user_agent, $matches)) {
                $blo['browser'] = "DreamPassport $matches[1].x";
            } elseif (preg_match("/NetCaptor *(\d)/i", $user_agent, $matches)) {
                $blo['browser'] = "NetCaptor $matches[1].x";
            } elseif (preg_match("/Opera *(\d)/i", $user_agent, $matches)) {
                $blo['browser'] = "Opera $matches[1].x";
            } elseif (preg_match("/Lunascape *(\d)/i", $user_agent, $matches)) {
                $blo['browser'] = "Lunascape (IE Component) $matches[1].x";
            } elseif (preg_match("/MSIE 5.01/i", $user_agent, $matches)) {
                $blo['browser'] = 'IE 5.01';
            } elseif (preg_match("/MSIE 5.0/i", $user_agent, $matches)) {
                $blo['browser'] = 'IE 5.0';
            } elseif (preg_match("/MSIE 5.14/i", $user_agent, $matches)) {
                $blo['browser'] = 'IE 5.14';
            } elseif (preg_match("/MSIE 5.16/i", $user_agent, $matches)) {
                $blo['browser'] = 'IE 5.16';
            } elseif (preg_match("/MSIE 5.5/i", $user_agent, $matches)) {
                $blo['browser'] = 'IE 5.5';
            } elseif (preg_match("/MSIE 6.0/i", $user_agent, $matches)) {
                $blo['browser'] = 'IE 6.0';
            } elseif (preg_match("/MSIE *(\d)/i", $user_agent, $matches)) {
                $blo['browser'] = "IE $matches[1].x";
            } elseif (preg_match("/Netscape6\/(\d)/i", $user_agent, $matches)) {
                $blo['browser'] = "Netscape $matches[1].x";
            } elseif (preg_match("/Netscape\/(\d)/i", $user_agent, $matches)) {
                $blo['browser'] = "Netscape $matches[1].x";
            } elseif (preg_match("/Phoenix\/(\d)/i", $user_agent, $matches)) {
                $blo['browser'] = "Phoenix $matches[1].x";
            } elseif (preg_match("/Firebird\/(\d)/i", $user_agent, $matches)) {
                $blo['browser'] = "Firebird $matches[1].x";
            } elseif (preg_match("/Firefox\/(\d)/i", $user_agent, $matches)) {
                $blo['browser'] = "Firefox $matches[1].x";
            } elseif (preg_match("/Camino\/(\d)/i", $user_agent, $matches)) {
                $blo['browser'] = "Camino $matches[1].x";
            } elseif (preg_match("/Galeon\/(\d)/i", $user_agent, $matches)) {
                $blo['browser'] = "Galeon $matches[1].x";
            } elseif (preg_match("/Chrome *(\d)/i", $user_agent, $matches)) {
                $blo['browser'] = "Chrome $matches[1].x";
            } elseif (preg_match("/Safari/", $user_agent, $matches)) {
                $blo['browser'] = 'Safari';
            } elseif (preg_match("/Konqueror\/(\d)/i", $user_agent, $matches)) {
                $blo['browser'] = "Konqueror $matches[1].x";
            } elseif (preg_match("/Gecko/", $user_agent, $matches)) {
                $blo['browser'] = 'Others (Gecko)';
            } else {
                $blo['browser'] = "NN $mv.x";
            }
        } else {
            if (preg_match("/InternetNinja *(\d)/i", $user_agent, $matches)) {
                $blo['browser'] = "Internet Ninja $matches[1].x";
            } elseif (preg_match("/Sleipnir Version *(\d)/i", $user_agent, $matches)) {
                $blo['browser'] = "Sleipnir $matches[1].x";
            } elseif (preg_match("/Borg\/(\d)/i", $user_agent, $matches)) {
                $blo['browser'] = "AirWeb $matches[1].x";
            } elseif (preg_match("/WebAuto\/(\d)/i", $user_agent, $matches)) {
                $blo['browser'] = "WebAuto $matches[1].x";
            } elseif (preg_match("/Lynx\/(\d)/i", $user_agent, $matches)) {
                $blo['browser'] = "Lynx $matches[1].x";
            } elseif (preg_match("/InternetLinkAgent\/(\d)/i", $user_agent, $matches)) {
                $blo['browser'] = "InternetLinkAgent $matches[1].x";
            } elseif (preg_match("/W3CRobot\/(\d)/i", $user_agent, $matches)) {
                $blo['browser'] = "W3CRobot $matches[1].x";
            } elseif (preg_match("/Openbot\/(\d)/i", $user_agent, $matches)) {
                $blo['browser'] = "Openbot $matches[1].x";
            } elseif (preg_match("/sharp pda browser\/(\d)/i", $user_agent, $matches)) {
                $blo['browser'] = "sharp pda browser $matches[1].x";
            } elseif (preg_match("/Cuam Ver *(\d)/i", $user_agent, $matches)) {
                $blo['browser'] = "Cuam(IE Component) $matches[1].x";
            } elseif (preg_match("/CuserAgentm Ver *(\d)/i", $user_agent, $matches)) {
                $blo['browser'] = "CuserAgentm (IE Component) $matches[1].x";
            } elseif (preg_match("/Donut R\/*(\d)/i", $user_agent, $matches)) {
                $blo['browser'] = "Donut $matches[1].x";
            } elseif (preg_match("/Moon Browser ver.*(\d)/i", $user_agent, $matches)) {
                $blo['browser'] = "Moon Browser ver. $matches[1].x";
            } elseif (preg_match("/w3m\/*(\d)/i", $user_agent, $matches)) {
                $blo['browser'] = "w3m $matches[1].x";
            } elseif (preg_match("/WWWC\/*(\d)/i", $user_agent, $matches)) {
                $blo['browser'] = "WWWC $matches[1].x";
            } elseif (preg_match("/PageDown\/*(\d)/i", $user_agent, $matches)) {
                $blo['browser'] = "PageDown $matches[1].x";
            } elseif (preg_match("/NexTools WebAgent \/*(\d)/i", $user_agent, $matches)) {
                $blo['browser'] = "NexTools WebAgent $matches[1].x";
            } elseif (preg_match("/UP.Browser\/*(\d)/i", $user_agent, $matches)) {
                $blo['browser'] = "UP.Browser $matches[1].x";
            } elseif (preg_match("/J-PHONE\/*(\d)/i", $user_agent, $matches)) {
                $blo['browser'] = "J-PHONE $matches[1].x";
            } elseif (preg_match("/DoCoMo\/*(\d)/i", $user_agent, $matches)) {
                $blo['browser'] = "DoCoMo $matches[1].x";
            } elseif (preg_match("/W3C_Validator\/*(\d)/i", $user_agent, $matches)) {
                $blo['browser'] = "W3C Validator $matches[1].x";
            } elseif (preg_match("/W3C_CSS_Validator_JFouffa\/*(\d)/i", $user_agent, $matches)) {
                $blo['browser'] = "W3C CSS Validator JFouffa $matches[1].x";
            } elseif (preg_match("/Another_HTML-lint\/*(\d)/i", $user_agent, $matches)) {
                $blo['browser'] = "Another HTML-lint $matches[1].x";
            } else {
                $blo['browser'] = "Others";
            }
        }
        $this->_cash["getUserInfo"][$user_agent] = $blo;
        return $blo;
    }
    /* ----------------------------------------- */

    /**
     * +-- リモートホストを加味したユーザー端末判定
     *
     * リモートホストやユーザーエージェントから、OSやブラウザ情報を取得します。
     * 引数が省略された場合は、リクエストされたユーザーのUserAgentが自動的に参照されます。
     *
     * @access      public
     * @param       string $user_agent OPTIONAL:NULL
     * @param       string $remote_host OPTIONAL:NULL
     * @return      string
     */
    public function getWeb($user_agent = NULL, $remote_host = NULL)
    {
        $user_agent = $user_agent === NULL ? $_SERVER['HTTP_USER_AGENT'] : $user_agent;
        if (isset($this->_cash['getWeb'][$user_agent])) {
            return $this->_cash['getWeb'][$user_agent];
        }
        $remote_host = $remote_host === NULL ? $this->getUserRemoteHost() : $remote_host;
        if (strstr($user_agent, 'KDDI') && strstr($user_agent, 'Opera')) {
            $web = self::OHTER;
        } elseif (strstr($user_agent, 'iPhone')) {
            $web = self::IPHONE;
            // $web = self::PC;
        } elseif (strstr($user_agent, 'Android')) {
            $web = self::ANDROID;
            // $web = self::PC;
        } elseif (preg_match('/\.(ido|ezweb)\.ne\.jp$/', $remote_host)) {
            if (isset($_SERVER['HTTP_X_UP_SUBNO'])) {
                // EZweb WAP2.0 端末用の処理
                if (isset($_SERVER['HTTP_X_UP_DEVCAP_MULTIMEDIA'])) {
                    $web = self::AU;
                } else {
                    // $web = self::TUKA;
                    $web = self::PC;
                }
            } else {
                // EZweb 旧端末用の処理
                // $web = self::IDO;
                $web = self::PC;
            }
        } elseif (strstr($user_agent, 'Googlebot-Mobile')) {
            $web = self::VODAFONE;
        } elseif ($remote_host  === 'pdxcgw.pdx.ne.jp') {
            // H' 用の処理
            $web = self::PHS;
        } elseif (mb_ereg('\.docomo\.ne\.jp$', $remote_host)) {
            // i-mode 用の処理
            $web = self::DOCOMO;
        } elseif (mb_ereg('\.jp-[ckqt]\.ne\.jp$', $remote_host) || mb_ereg('\.softbank\.ne\.jp', $remote_host)) {
            // J-SKY 用の処理
            $web = self::VODAFONE;
        } elseif (mb_ereg('\.pipopa\.ne\.jp$', $remote_host)) {
            // L-mode 用の処理
            $web = self::LMODE;
        } elseif ($_SERVER['REMOTE_ADDR'] === '') {
            //特定IPはデバッグに
            $web = self::DEBUG;
        } elseif (strstr($user_agent, 'PlayStation Portable')) {
            $web = self::PSP;
        } else {
            // それ以外はPCよん
            $web = self::PC;
        }
        $this->_cash['getWeb'][$user_agent] = $web;
        return $web;
    }
    /* ----------------------------------------- */

    /**
     * +-- リクエストされたユーザーの端末固有情報を取得します
     *
     * リクエストされたユーザーの端末固有情報を取得します。
     * 主にガラケーが出している、端末情報を配列で取得します。
     *
     * @access      public
     * @return      array
     */
    public function getCharacter()
    {
        if (isset($this->_cash['getCharacter'])) {
            return $this->_cash['getCharacter'];
        }
        $info['remote_host'] = $this->getUserRemoteHost();
        $info['web'] = $this->getWeb();
        switch ($info['web']) {
            case self::DEBUG:
            //デバッグの処理
                $get_user        = $this->getUserInfo();
                $info['browser'] = $get_user['browser'];
                $info['b_type']  = 'xhtml';
                $info['size']    = '10000';
                $info['device']  = 'test';
                $info['uid']     = 'test';
                $info['smaf']    = 124;
                $info['pcm']     = true;
                $info['grf']     = true;
                break;
            case self::PC:
            //PCの処理
                $get_user = $this->getUserInfo();
                $info['b_type']  = 'html';
                $info['browser'] = $get_user['browser'];
                $info['device']  = $get_user['os'];
                $info['uid']     = $_SERVER['REMOTE_ADDR'];
                $info['size']    = '9999999999';
                $info['smaf']    = 124;
                $info['pcm']     = true;
                $info['grf']     = true;
                break;
            case self::VODAFONE:
            //Vodafoneの処理
                $user_agent = explode('/', strtr($_SERVER['HTTP_USER_AGENT'], array(' ' => '/')));
                $info['b_type'] = 'html';
                if (isset($_SERVER['HTTP_X_JPHONE_MSNAME'])) {
                    $info['browser'] = $_SERVER['HTTP_X_JPHONE_MSNAME'];
                } else {
                    $info['browser'] = $user_agent[1];
                }
                if (isset($user_agent[3])) {
                    if (isset($_SERVER['HTTP_X_JPHONE_UID'])) {
                        $info['uid'] = $_SERVER['HTTP_X_JPHONE_UID'];
                    } else {
                        $info['uid'] = $user_agent[3];
                    }
                } else {
                    $info['uid'] = $_SERVER['REMOTE_ADDR'];
                }
                if (isset($_SERVER['HTTP_X_JPHONE_SMAF'])) {
                    $info_SMAF = explode('/', $_SERVER['HTTP_X_JPHONE_SMAF']);
                    while (list($key, $val) = each($info_SMAF)) {
                        if ($val === 'pcm') {
                            $info['pcm'] = TRUE;
                        } elseif ($val === 'grf') {
                            $info['grf'] = TRUE;
                        } else {
                            $info['smaf'] = $val;
                        }
                    }
                } else {
                    $info['smaf'] = FALSE;
                }
                if (!isset($info['pcm'])) {
                    $info['pcm'] = FALSE;
                }
                if (!isset($info['grf'])) {
                    $info['grf'] = FALSE;
                }
                list($info['width'], $info['higth']) = explode('*', $_SERVER['HTTP_X_JPHONE_DISPLAY']);
                $info['device']                      = $_SERVER['HTTP_X_JPHONE_MSNAME'];
                break;
            case self::DOCOMO:
            //DOCOMOの場合
                $info['uid']     = isset($_SERVER['HTTP_X_DCMGUID']) ? $_SERVER['HTTP_X_DCMGUID'] : false;
                $info['b_type']  = 'html';

                $user_agent = explode('/', strtr($_SERVER['HTTP_USER_AGENT'], array(
                    ' ' => '/',
                    '（' => '/',
                    ')' => '/',
                    ';' => '/'
                    )
                ));
                $info['browser'] = $user_agent[1];
                $info['device']  = $user_agent[2];
                $info['cash']    = $user_agent[3];
                if ($info['cash']) {
                    $info['size'] = substr($info[cash], 1) * 1000;
                } else {
                    $info['size'] = 12000;
                }
                break;
            case self::AU:
            case self::TUKA:
            //EZWEBの場合
                $info['uid']    = $_SERVER['HTTP_X_UP_SUBNO'];
                $info['size']   = $_SERVER['HTTP_X_UP_DEVCAP_MAX_PDU'];
                $user_agent = explode('-', strtr($_SERVER['HTTP_USER_AGENT'], array(
                    ' ' => '-',)
                ));
                $info['device'] = $user_agent[1];
                if (isset($_SERVER['HTTP_X_UP_DEVCAP_MULTIMEDIA'])) {
                    //XHTML端末
                    $info['b_type'] = 'xhtml';
                    $info['aud'] = TRUE;
                    $smaf_type = (int)substr($_SERVER['HTTP_X_UP_DEVCAP_MULTIMEDIA'], 4, 1);
                    if ($smaf_type === 1) {
                        $info['smaf'] = 4;
                    } elseif ($smaf_type === 2) {
                        $info['smaf'] = 16;
                        $info['grf'] = FALSE;
                    } elseif ($smaf_type === 3) {
                        $info['smaf'] = 16;
                        $info['grf'] = TRUE;
                    } elseif ($smaf_type === 4) {
                        $info['smaf'] = 40;
                        $info['grf'] = FALSE;
                    } elseif ($smaf_type === 5) {
                        $info['smaf'] = 40;
                        $info['grf'] = TRUE;
                    } elseif ($smaf_type === 6) {
                        $info['smaf'] = 40;
                        $info['grf'] = FALSE;
                    } elseif ($smaf_type === 7) {
                        $info['smaf'] = 40;
                        $info['grf'] = TRUE;
                    }
                } else {
                    //HDML端末
                    ini_set('default_mimetype', 'text/x-hdml');    // HDML mime-types is text/x-hdml
                    ini_set('default_charset', 'Shift_JIS');    // HDML charset is Shift_JIS
                    $info['b_type'] = 'hdml';
                    $device = $info['device'];
                    if (isset($this->Envi_hdml_aud_map['$device'])) {$info['aud'] = TRUE;}
                    else {$info['aud'] = FALSE;}
                    if (isset($this->Envi_hdml_grf_map['$device'])) {$info['grf'] = TRUE;}
                    $info['smaf'] = $this->Envi_hdml_smaf_map['$device'];
                }
                break;
            case self::IDO:
                //旧KDDI端末
                ini_set('default_mimetype', 'text/x-hdml');    // HDML mime-types is text/x-hdml
                ini_set('default_charset', 'Shift_JIS');    // HDML charset is Shift_JIS
                $info['b_type'] = 'hdml';
                $device         = $info['device'];
                if (isset($this->Envi_hdml_aud_map[$dev])) {
                    $info['aud'] = TRUE;
                } else {
                    $info['aud'] = FALSE;
                }
                if (isset($this->Envi_hdml_grf_map[$dev])) {
                    $info['grf'] = TRUE;
                }
                $info['smaf'] = isset($this->Envi_hdml_smaf_map[$dev]) ? $this->Envi_hdml_smaf_map[$dev] : 16;

                break;
            case self::LMODE:
            //L-modeの場合
                $user_agent      =  explode('/', $_SERVER['HTTP_USER_AGENT']);
                $info['size']    = $user_agent[5];
                $info['b_type']  = 'html';
                $info['browser'] = $user_agent[0];
                if (isset($this->Envi_lmode_smaf_map[$browser])) {
                    $info['smaf'] = $this->Envi_lmode_smaf_map[$browser];
                } else {
                    $info['smaf'] = FALSE;
                }
                $info['device'] = $user_agent[4];
                if (2 >= (substr($user_agent[2], 0, 1))) {
                    $info['b_type'] = 2;
                }
                if (2 >= (substr($user_agent[2], 0, 1))) {
                    $info['b_type'] = 1;
                }
                if ($info['b_type'] == 2) {
                    if ($user_agent[8] == 1) {
                        $info['ssl'] = TRUE;
                    } else {
                        $info['ssl'] = FALSE;
                    }
                    $info['p_type'] = $user_agent[9];
                    $info['width']  = substr($user_agent[9], 1, 4);
                    $info['higth']  = substr($user_agent[9], 5, 4);

                } else {
                    $info['ssl'] = FALSE;
                }

                break;
            default:
                $get_user = $this->getUserInfo();
                $info['b_type']  = 'html';
                $info['browser'] = $get_user['browser'];
                $info['device']  = $get_user['os'];
                $info['uid']     = $_SERVER['REMOTE_ADDR'];
                $info['size']    = '9999999999';
                $info['smaf']    = false;
                $info['pcm']     = false;
                $info['grf']     = false;
                break;
        }
        $this->_cash['getCharacter'] = $info;
        return $info;
    }
    /* ----------------------------------------- */
}