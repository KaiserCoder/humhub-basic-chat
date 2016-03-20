<?php

namespace humhub\modules\ponychat\libs;

use Yii;

class PonyCode
{
    private static $patterns = [
        'rainbowToHTML' => [
            'pattern' => '#\[rainbow\](.*)\[\/rainbow\]#is',
            'child'   => false
        ],
        'videoToHTML' => [
            'pattern' => '#\[video\][\s]*(http(.*)youtube\.com(.*)?v=([\w\_\-]+)(.*?))[\s]*\[\/video\]#is',
            'child'   => false
        ],
        'imageToHTML' => [
            'pattern' => '#\[img\][\s]*(.*)[\s]*\[\/img\]#is',
            'child'   => false
        ],
        'dictatorToHTML' => [
            'pattern' => '#(hitler|fuhrer|castro|musolini|staline?|mao|kim([\s\-]+)(jong|jung|ill?)|valls)#is',
            'child'   => true
        ],
        'spoilerToHTML' => [
            'pattern' => '#\[spoiler\](.*)\[\/spoiler\]#is',
            'child'   => true
        ],
        'baseToHTML' => [
            'pattern' => '#\[(b|i|u|pre)\](.*)\[/\1\]#is',
            'child'   => true
        ],
        'colorToHTML' => [
            'pattern' => '#\[color=([\w\#]+)\](.*)\[\/color\]#is',
            'child'   => true
        ],
        'mirrorToHTML' => [
            'pattern' => '#\[mirror\](.*)\[\/mirror\]#is',
            'child'   => true
        ],
        'urlToHTML' => [
            'pattern' => '#\[url(=(https?\:\/\/[\H]+))?\](.*)\[\/url\]#is',
            'child'   => false
        ],
        'smileyToHTML' => [
            'pattern' => '#:([\w^]+):#',
            'child'   => true
        ],
        'shiraToHTML' => [
            'pattern' => '#shiracat#is',
            'child'   => true
        ],
        'hapToHTML' => [
            'pattern' => '#haptwi#is',
            'child'   => true
        ]
    ];

    private static $colors = [
        'ff0000', 'ff8400',
        'ffea00', '00ff06',
        '0078ff', 'a800ff'
    ];

    private static $string;

    public static function clean($input)
    {
        self::$string = $input;

        foreach (self::$patterns as $method => $regex)
        {
            self::parse($regex, [static::class, $method]);
        }

        return self::$string;
    }

    private static function parse($regex, array $callback)
    {
        while (preg_match($regex['pattern'], self::$string))
        {
            self::$string = preg_replace_callback($regex['pattern'], function($matches) use($regex, $callback)
            {
                $return = call_user_func_array($callback, [$matches]);

                if (!$regex['child']) {
                    $return = preg_replace('#\[(.*)\]#', null, $return);
                }

                return $return;
            }, self::$string);
        }
    }

    private static function shortenURL($input)
    {
        $output = strtolower($input);
        $output = preg_replace('#^(http|ftp)s?://#', null, $output);

        if (strlen($output) > 50) {
            $output = substr($output, 0, strpos($output, '/') + 5) . '...';
        }

        return $output;
    }

    private static function stringSplit($string, $length = 0)
    {
        if ($length > 0) {
            $result = [];
            $stringLength = mb_strlen($string, 'UTF-8');

            for ($index = 0; $index < $stringLength; $index += $length)
            {
                $result[] = mb_substr($string, $index, $length, 'UTF-8');
            }

            return $result;
        }

        return preg_split('//u', $string, -1, PREG_SPLIT_NO_EMPTY);
    }

    private static function urlExists($url)
    {
        $headers = get_headers($url);
        return stripos($headers[0], '200 OK') ? true : false;
    }

    private static function isImage($url)
    {
        $parameters = [
            'http' => [
                'method' => 'HEAD'
            ]
        ];

        $context = stream_context_create($parameters);

        if (self::urlExists($url)) {
            $fileOpen = fopen($url, 'rb', false, $context);
        } else {
            return false;
        }

        if (!($meta = stream_get_meta_data($fileOpen))) {
            fclose($fileOpen);
            return false;
        }

        $wrapperData = $meta['wrapper_data'];

        if (is_array($wrapperData)) {
            foreach(array_keys($wrapperData) as $key)
            {
                if (substr($wrapperData[$key], 0, 19) === 'Content-Type: image') {
                    fclose($fileOpen);
                    return true;
                }
            }
        }

        fclose($fileOpen);

        return false;
    }

    private static function spoilerToHTML($match)
    {
        $id = uniqid();
        return '<a href="javascript:spoiler(\'' . $id . '\')" class="spoiler-button"><i id="l' . $id . '" class="fa fa-angle-right"></i> Spoiler</a><div id="' . $id . '" class="spoiler">' . $match[1] . '</div>';
    }

    private static function hapToHTML($match)
    {
        return '<img src="' . Yii::getAlias('@web') . '/img/hap3.png" class="smiley"/>';
    }

    private static function shiraToHTML($match)
    {
        return '<img src="' . Yii::getAlias('@web') . '/img/shira.jpg" class="smiley"/>';
    }

    private static function mirrorToHTML($match)
    {
        return '<span class="mirror">' . $match[1] . '</span>';
    }

    private static function dictatorToHTML($match)
    {
        return 'Sombra';
    }

    private static function smileyToHTML($match)
    {
        return '<img src="' . Yii::getAlias('@web') . '/img/smiley/' . $match[1] . '.png' . '" alt="' . $match[1] . '" class="smiley"/>';
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
        return '<iframe width="560" height="315" src="https://www.youtube.com/embed/' . $match[4] . '" frameborder="0" allowfullscreen></iframe>';
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
        if (empty($match[2])) {
            return '<a href="' . preg_replace('#javascript([\s]*):#', 'ponyscript:', $match[3]) . '" target="_blank">' . self::shortenURL($match[3]) . '</a>';
        } else {
            return '<a href="' . preg_replace('#javascript([\s]*):#', 'ponyscript:', $match[2]) . '" target="_blank">' . $match[3] . '</a>';
        }
    }

    private static function imageToHTML($match)
    {
        if (!self::isImage($match[1])) {
            $match[1] = Yii::getAlias('@web') . '/img/csrf.jpg';
        }

        return '<img src="' . $match[1] . '" alt="' . $match[1] . '" title="' . $match[1] . '"/>';
    }

}
