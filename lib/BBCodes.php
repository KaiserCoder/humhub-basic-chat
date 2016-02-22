<?php

namespace humhub\modules\ponychat\lib;

class BBCodes
{
    const PATTERN_PHP = '#\[PHP\](.*?)\[\/PHP\]#is';
    const PATTERN_CODE = '#\[CODE(=([a-z]+))?\](.*?)\[\/CODE\]#is';
    const PATTERN_URL_BLOCK = '#\[URLS\](.*?)\[\/URLS\]#is';
    const PATTERN_URL = '#\[URL(=(.*?))?\](.*?)\[\/URL\]#i';
    const PATTERN_IMAGE = '#\[IMG(=(.*?))?\](.*?)\[\/IMG\]#i';
    const PATTERN_QUOTE = '#\[QUOTE(=(.*?))?\](.*?)\[\/QUOTE\]#i';
    const PATTERN_NO_PARSE = '#\[NOPARSE\](.*?)\[\/NOPARSE\]#is';
    const PATTERN_TITLE = '@\s*\[((SUB)?TITLE)\](.*)\[/\1\]\s*@i';
    const PATTERN_BASE = '@\[(B|I|U|PRE|STRIKE)\](.*)\[/\1\]@i';
    const PATTERN_ITEM = '@\s*\[ITEM\](.*)\[/ITEM\]\s*@i';
    const PATTERN_LIST = '@\s*\[LIST\](.*)\[/LIST\]\s*@is';
    const PATTERN_LINK = '#((http|ftp)s?://)?(([a-z][a-z0-9-]*\.)+)?[a-z][a-z0-9-]*\.([a-z]{2,6})(/[^\s]*)?#is';
    const OPT_URLS = 1;
    const OPT_BASE = 2;
    const OPT_CODE = 4;

    // Clean Options
    const OPT_LIST = 8;
    const OPT_QUOTE = 16;
    const OPT_BASIC = 31;
    const OPT_TITLE = 32;
    const OPT_IMG = 64;
    const OPT_NOPARSE = 128; // 31 = 1|2|4|8|16
    const OPT_FULL = 255;
    private $CodeReplace = [];
    private $NoParseReplace = [];
    private $str; // 255 = 1|2|4|8|16|32|64|128

    /**
     * Same as calling clean($input, BBCodes::OPT_FULL)
     *
     * @param string $input
     *
     * @return string
     */
    public function fullClean($input)
    {
        return $this->clean($input, self::OPT_FULL);
    }

    /**
     * @param     $input
     * @param int $opt
     *
     * @return string
     */
    public function clean($input, $opt = self::OPT_BASIC)
    {
        $this->str = $input;

        if (self::OPT_NOPARSE & $opt) {
            $this->findNoParse();
        }
        if (self::OPT_CODE & $opt) {
            $this->findCode();
        }
        if (self::OPT_LIST & $opt) {
            $this->parseList();
        }
        if (self::OPT_BASE & $opt) {
            $this->parseBase();
        }
        if (self::OPT_TITLE & $opt) {
            $this->parseTitle();
        }
        if (self::OPT_URLS & $opt) {
            $this->parseURL();
        }
        if (self::OPT_IMG & $opt) {
            $this->parseImage();
        }
        if (self::OPT_QUOTE & $opt) {
            $this->parseQuote();
        }
        if (self::OPT_URLS & $opt) {
            $this->parseURLBlock();
        }

        $this->str = nl2br($this->str);
        $this->str = preg_replace('#<br( /)?>\s*<br( /)?>#', '</p><p>', $this->str);

        if (self::OPT_CODE & $opt) {
            $this->parseCode();
        }
        if (self::OPT_NOPARSE & $opt) {
            $this->parseNoParse();
        }

        return preg_replace('#javascript([\s]*):#', 'ponyscript:', $this->str);
    }

    private function findNoParse()
    {
        //*/ Set up replace array for NOPARSE tags
        if (preg_match_all(self::PATTERN_NO_PARSE, $this->str, $matches)) {
            foreach ($matches[1] as $k => $v) {
                $this->NoParseReplace[$k] = $v;
            }
        }
    }

    private function findCode()
    {
        //*/ Set up replace array for code tags
        if (preg_match_all(self::PATTERN_CODE, $this->str, $matches)) {
            foreach ($matches[3] as $k => $v) {
                if (!empty($matches[2][$k]) && class_exists(GenSynth::class)) {
                    $v = GenSynth::highlight_string($v, strtolower($matches[2][$k]), true, GenSynth::OPT_HEADER_DIV & GenSynth::OPT_LINE_NUMBERS_FANCY & GenSynth::OPT_CAPS_NO_CHANGE);
                    $this->CodeReplace[$k] = '<div class="bbcodes_code_wrapper"><span class="bbcodes_code_language">' . $matches[2][$k] . ' Code:</span>' . "\n"
                                             . '<div class="bbcodes_code">' . $v . '</div></div>';
                }
                else {
                    $v = "<pre>$v</pre>";
                    $this->CodeReplace[$k] = '<div class="bbcodes_code_wrapper"><span class="bbcodes_code_language">Code:</span>' . "\n"
                                             . '<div class="bbcodes_code">' . $v . '</div></div>';
                }
            }
        }
    }

    private static function listToHTML($v)
    {
        if (preg_match(BBCodes::PATTERN_ITEM, $v[1])) {
            return '<ul>' . preg_replace(BBCodes::PATTERN_ITEM, '<li>\\1</li>', $v[1]) . '</ul>';
        }
        else {
            return $v[0];
        }
    }

    private function parseList()
    {
        while (preg_match(self::PATTERN_LIST, $this->str)) {
            $this->str = preg_replace_callback(self::PATTERN_LIST, [static::class, 'listToHTML'], $this->str);
        }
    }

    private static function baseToHTML($v)
    {
        $t = strtolower($v[1]);
        switch ($t) {
            case 'b':
                return "<strong>$v[2]</strong>";
            default:
                return "<$v[1]>$v[2]</$v[1]>";
        }
    }

    private function parseBase()
    {
        while (preg_match(self::PATTERN_BASE, $this->str)) {
            $this->str = preg_replace_callback(self::PATTERN_BASE, [static::class, 'baseToHTML'], $this->str);
        }
    }

    private static function titleToHTML($v)
    {
        $t = strtolower($v[1]);
        switch ($t) {
            case 'title':
                return "<h2>$v[3]</h2>";
            case 'subtitle':
                return "<h3>$v[3]</h3>";
            default:
                return $v[3];
        }
    }

    private function parseTitle()
    {
        while (preg_match(self::PATTERN_TITLE, $this->str)) {
            $this->str = preg_replace_callback(self::PATTERN_TITLE, [static::class, 'titleToHTML'], $this->str);
        }
    }

    private function parseURL()
    {
        if (preg_match_all(self::PATTERN_URL, $this->str, $matches)) {
            $URLReplace = [];
            foreach ($matches[0] as $k => $v) {
                if (empty($matches[2][$k])) {
                    $display = self::shortenURL($matches[3][$k]);
                    $url = $matches[3][$k];
                }
                else {
                    $display = $matches[3][$k];
                    $url = $matches[2][$k];
                }
                $URLReplace[$v] = '<a href="' . $url . '" target="_blank">' . $display . '</a>';
            }
            $this->str = strtr($this->str, $URLReplace);
        }
    }

    public static function shortenURL($input)
    {
        $output = strtolower($input);
        $output = preg_replace("#^(http|ftp)s?://#", "", $output);
        if (strlen($output) > 50) {
            $output = substr($output, 0, strpos($output, "/") + 5) . '...';
        }

        return $output;
    }

    private function parseImage()
    {
        if (preg_match_all(self::PATTERN_IMAGE, $this->str, $matches)) {
            $IMGReplace = [];
            foreach ($matches[0] as $k => $v) {
                if (empty($matches[2][$k])) {
                    $url = $matches[3][$k];
                }
                else {
                    $url = $matches[2][$k];
                }
                $IMGReplace[$v] = '<img src="' . $url . '" alt="' . ($url != $matches[3][$k] ? $matches[3][$k] : '') . '" title="' . ($url != $matches[3][$k] ? $matches[3][$k] : '') . '" />';
            }
            $this->str = strtr($this->str, $IMGReplace);
        }
    }

    private function parseQuote()
    {
        if (preg_match_all(self::PATTERN_QUOTE, $this->str, $matches)) {
            $QUOTEReplace = [];
            foreach ($matches[0] as $k => $v) {
                $QUOTEFind[$k] = $v;
                if (empty($matches[2][$k])) {
                    $by = '';
                    $body = $matches[3][$k];
                }
                else {
                    $by = '<span class="bbcodes_quote_author">Quote by: <strong>' . $matches[2][$k] . '</strong></span>' . "\n";
                    $body = $matches[3][$k];
                }
                $QUOTEReplace[$v] = '<div class="bbcodes_quote">' . $by . $body . '</div>';
            }
            $this->str = strtr($this->str, $QUOTEReplace);
        }
    }

    private function parseURLBlock()
    {
        if (preg_match_all(self::PATTERN_URL_BLOCK, $this->str, $matches)) {
            $replace = [];
            foreach ($matches[1] as $k => $v) {
                $replace[$matches[0][$k]] = $this->findURLs($v);
            }
            $this->str = strtr($this->str, $replace);
        }
    }

    private function urlToLink($v)
    {
        return '<a href="' . $v[0] . '">' . BBCodes::shortenURL($v[0]) . '</a>';
    }

    private function findURLs($str)
    {
        return preg_replace_callback(self::PATTERN_LINK, [static::class, 'urlToLink'], $str);
    }

    private function parseCode()
    {
        //*/ Set up replace array for code tags (post BB filtering, so the BB tags don't get changed when IN code)
        if (preg_match_all(self::PATTERN_CODE, $this->str, $matches)) {
            $CodeFind = [];
            foreach ($matches[0] as $k => $v) {
                $CodeFind[$k] = $v;
            }
            $this->str = str_replace($CodeFind, $this->CodeReplace, $this->str);
            foreach ($this->CodeReplace as $k => $v) {
                unset($this->CodeReplace[$k]);
            }
        }
    }

    private function parseNoParse()
    {
        //*/ Set up replace array for NOPARSE tags (post BB filtering)
        if (preg_match_all(self::PATTERN_NO_PARSE, $this->str, $matches)) {
            $NOPARSEfind = [];
            foreach ($matches[0] as $k => $v) {
                $NOPARSEfind[$k] = $v;
            }
            $this->str = str_replace($NOPARSEfind, $this->NoParseReplace, $this->str);
            foreach ($this->NoParseReplace as $k => $v) {
                unset($this->NoParseReplace[$k]);
            }
        }
    }
}
