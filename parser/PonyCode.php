<?php

namespace humhub\modules\ponychat\parser;

class PonyCode
{
    private static $patterns = [
        'dictatorToHTML' => '@(HITLER|FUHRER|MUSOLINI|STALIN|MAO|KIM([\s\-]+)JONG([\s\-]+)UN|KIM([\s\-]+)IL([\s\-]+)SUNG|VALLS)@is',
        'videoToHTML'    => '@\[VIDEO\](https\:\/\/www\.youtube\.com\/watch\?v=([\w\_\-]+))\[/VIDEO\]@is',
        'baseToHTML'     => '@\[(B|I|U|PRE|STRIKE)\](.*)\[/\1\]@is',
        'colorToHTML'    => '@\[COLOR=([\w#]+)\](.*)\[/COLOR\]@is',
        'rainbowToHTML'  => '@\[RAINBOW\](.*)\[/RAINBOW\]@is',
        'imageToHTML'    => '@\[IMG\](.*?)\[\/IMG\]@is',
        'urlToHTML'      => '@\[URL\](.*?)\[\/URL\]@is',
        'smileyToHTML'   => '@:([\w^]+):@'
    ];

    private static $colors = [
        'ff0000', 'ff8400',
        'ffea00', '00ff06',
        '0078ff', 'a800ff'
    ];

    private $string;

    public function clean($input)
    {
        $this->string = $input;

        foreach (self::$patterns as $method => $regex)
        {
            $this->parse($regex, [static::class, $method]);
        }

        return preg_replace('#javascript([\s]*):#', 'ponyscript:', $this->string);
    }

    private function parse($pattern, array $callback)
    {
        while (preg_match($pattern, $this->string))
        {
            $this->string = preg_replace_callback($pattern, $callback, $this->string);
        }
    }

    private static function shortenURL($input)
    {
        $output = strtolower($input);
        $output = preg_replace('@^(http|ftp)s?://@', null, $output);
        if (strlen($output) > 50) {
            $output = substr($output, 0, strpos($output, '/') + 5) . '...';
        }

        return $output;
    }

    private static function stringSplit($str, $l = 0)
    {
        if ($l > 0) {
            $ret = [];
            $len = mb_strlen($str, 'UTF-8');
            for ($i = 0; $i < $len; $i += $l)
            {
                $ret[] = mb_substr($str, $i, $l, 'UTF-8');
            }
            return $ret;
        }
        return preg_split('//u', $str, -1, PREG_SPLIT_NO_EMPTY);
    }

    private static function isImage($url)
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

        $wrapper_data = $meta['wrapper_data'];
        if (is_array($wrapper_data)) {
            foreach(array_keys($wrapper_data) as $hh)
            {
                if (substr($wrapper_data[$hh], 0, 19) == 'Content-Type: image') {
                    fclose($fp);
                    return true;
                }
            }
        }

        fclose($fp);
        return false;
    }

    private static function dictatorToHTML($match)
    {
        unset($match);
        return 'Sombra';
    }

    private static function smileyToHTML($match)
    {
        return '<img src="' . \Yii::getAlias('@web') . '/img/smiley/' . $match[1] . '.png' . '" alt="' . $match[1] . '" class="smiley"/>';
    }

    private static function rainbowToHTML($match)
    {
	    $match[1] = html_entity_decode($match[1], ENT_QUOTES);
        $string = self::stringSplit($match[1]);
	    $length = count($string);
        $result = '';

        for ($index = 0; $index < $length; ++$index)
        {
            $result .= '<span style="color:#' . self::$colors[$index % count(self::$colors)] . '">' . $string[$index] . '</span>';
        }

        return $result;
    }

    private static function videoToHTML($match)
    {
        return '<iframe width="560" height="315" src="https://www.youtube.com/embed/' . $match[2] . '" frameborder="0" allowfullscreen></iframe>';
    }

    private static function colorToHTML($match)
    {
        return '<span style="color:' . $match[1] . '">' . $match[2] . '</span>';
    }

    private static function baseToHTML($match)
    {
        $char = strtolower($match[1]);
        switch ($char) {
            case 'b':
                return '<strong>' . $match[2] . '</strong>';
            default:
                return '<' . $char . '>' . $match[2] . '</' . $char . '>';
        }
    }

    private static function urlToHTML($match)
    {
        return '<a href="' . $match[1] . '" target="_blank">' . self::shortenURL($match[1]) . '</a>';
    }

    private static function imageToHTML($match)
    {
        if (!self::isImage($match[1])) {
            $match[1] = \Yii::getAlias('@web') . '/img/csrf.jpg';
        }

        return '<img src="' . $match[1] . '" alt="' . $match[1] . '" title="' . $match[1] . '"/>';
    }

}
