<?php

namespace humhub\modules\ponychat\parser;

use Yii;
use humhub\compat\CHtml;

class PonyCode
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
    const PATTERN_COLOR = '@\[COLOR=([\w#]+)\](.*)\[/COLOR\]@is';
    const PATTERN_VIDEO = '@\[VIDEO\](https\:\/\/www\.youtube\.com\/watch\?v=([\w\_\-]+))\[/VIDEO\]@is';
    const PATTERN_RAINBOW = '@\[RAINBOW\](.*)\[/RAINBOW\]@is';
    const PATTERN_SMILEY = '@:([\w^]+):@';

    private static $colors = [
        'ff0000', 'ff8400',
        'ffea00', '00ff06',
        '0078ff', 'a800ff'
    ];

    private $str;

    public function clean($input)
    {
        $this->str = $input;

        $this->parseBase();
        $this->parseURL();
        $this->parseQuote();
        $this->parseImage();
        $this->parseColor();
        $this->parseVideo();
        $this->parseRainbow();
        $this->parseSmiley();

        return preg_replace('#javascript([\s]*):#', 'ponyscript:', $this->str);
    }

    public static function isImage($url)
    {
        $params = ['http' => [
            'method' => 'HEAD'
        ]];
        $ctx = stream_context_create($params);
        $fp = @fopen($url, 'rb', false, $ctx);
        if (!$fp) return false;

        $meta = stream_get_meta_data($fp);
        if (!$meta) {
            fclose($fp);
            return false;
        }

        $wrapper_data = $meta["wrapper_data"];
        if (is_array($wrapper_data)) {
            foreach(array_keys($wrapper_data) as $hh)
            {
                if (substr($wrapper_data[$hh], 0, 19) == "Content-Type: image") {
                    fclose($fp);
                    return true;
                }
            }
        }

        fclose($fp);
        return false;
    }

    private function parseSmiley()
    {
        while (preg_match(self::PATTERN_SMILEY, $this->str))
        {
            $this->str = preg_replace_callback(self::PATTERN_SMILEY, [static::class, 'smileyToHTML'], $this->str);
        }
    }

    private static function smileyToHTML($v)
    {
        return CHtml::img(Yii::getAlias('@web') . '/img/smiley/' . $v[1] . '.png', ['alt' => $v[1], 'class' => 'smiley']);
    }

    private function parseRainbow()
    {
        while (preg_match(self::PATTERN_RAINBOW, $this->str))
        {
            $this->str = preg_replace_callback(self::PATTERN_RAINBOW, [static::class, 'rainbowToHTML'], $this->str);
        }
    }

    private static function rainbowToHTML($v)
    {
        $result = '';
        $length = strlen($v[1]);

        for ($index = 0; $index < $length; ++$index)
        {
            $result .= '<span style="color:#' . self::$colors[$index % count(self::$colors)] . '">' . $v[1][$index] . '</span>';
        }

        return $result;
    }

    private function parseVideo()
    {
        while (preg_match(self::PATTERN_VIDEO, $this->str))
        {
            $this->str = preg_replace_callback(self::PATTERN_VIDEO, [static::class, 'videoToHTML'], $this->str);
        }
    }

    private static function videoToHTML($v)
    {
        return '<iframe width="560" height="315" src="https://www.youtube.com/embed/' . $v[2] . '" frameborder="0" allowfullscreen></iframe>';
    }

    private function parseColor()
    {
        while (preg_match(self::PATTERN_COLOR, $this->str))
        {
            $this->str = preg_replace_callback(self::PATTERN_COLOR, [static::class, 'colorToHTML'], $this->str);
        }
    }

    private static function colorToHTML($v)
    {
        return '<span style="color:' . $v[1] . '">' . $v[2] . '</span>';
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
        while (preg_match(self::PATTERN_BASE, $this->str))
        {
            $this->str = preg_replace_callback(self::PATTERN_BASE, [static::class, 'baseToHTML'], $this->str);
        }
    }

    private function parseURL()
    {
        if (preg_match_all(self::PATTERN_URL, $this->str, $matches)) {
            $URLReplace = [];
            foreach ($matches[0] as $k => $v)
            {
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
            foreach ($matches[0] as $k => $v)
            {
                if (empty($matches[2][$k])) {
                    $url = $matches[3][$k];
                }
                else {
                    $url = $matches[2][$k];
                }

                if (self::isImage($url)) {
                    $IMGReplace[$v] = '<img src="' . $url . '" alt="' . ($url != $matches[3][$k] ? $matches[3][$k] : '') . '" title="' . ($url != $matches[3][$k] ? $matches[3][$k] : '') . '" />';
                } else {
                    $IMGReplace[$v] = '<img src="ponyknowscsrf" alt="' . ($url != $matches[3][$k] ? $matches[3][$k] : '') . '" title="' . ($url != $matches[3][$k] ? $matches[3][$k] : '') . '" />';
                }
            }
            $this->str = strtr($this->str, $IMGReplace);
        }
    }

    private function parseQuote()
    {
        if (preg_match_all(self::PATTERN_QUOTE, $this->str, $matches)) {
            $QUOTEReplace = [];
            foreach ($matches[0] as $k => $v)
            {
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

}
