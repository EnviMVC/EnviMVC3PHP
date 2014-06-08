<?php
/**
 * @package
 * @subpackage
 * @sinse 0.1
 * @author     akito<akito-artisan@five-foxes.com>
 */
/**
 * @package
 * @subpackage
 * @sinse 0.1
 * @author     akito<akito-artisan@five-foxes.com>
 */
class EnviParseSample
{

    /**
     * +-- And‚Ì‚Ä‚·‚Æ
     *
     * @access      public
     * @param       boolean $aaa
     * @param       boolean $bbb
     * @return      integer
     */
    public function andTest($aaa, $bbb)
    {
        if ($aaa === false && $bbb === false) {
            return 1;
        }
        return 2;
    }
    /* ----------------------------------------- */

    /**
     * +-- Or‚Ì‚Ä‚·‚Æ
     *
     * @access      public
     * @param       boolean $aaa
     * @param       boolean $bbb
     * @return      integer
     */
    public function orTest($aaa, $bbb)
    {
        if ($aaa === false || $bbb === false) {
            return 1;
        }
        return 2;
    }
    /* ----------------------------------------- */

    /**
     * +-- if‚Ì‚Ä‚·‚Æ
     *
     * @access      public
     * @param       boolean $aaa
     * @param       boolean $bbb
     * @return      integer
     */
    public function ifTest($aaa, $bbb)
    {
        if ($aaa === false) {
            return 1;
        } elseif ($bbb === false) {
            return 3;
        }
        return 2;
    }
    /* ----------------------------------------- */
}
