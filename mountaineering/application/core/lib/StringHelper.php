<?php
/**
 * Created by PhpStorm.
 * User: Mark
 * Date: 3/29/18
 * Time: 9:49 AM
 */

namespace markpthomas\library;

/**
 * Class StringHelper Contains methods to help with working with strings.
 * @package markpthomas\library
 */
class StringHelper {
    /**
     * Determines if the string contains the provided query substring.
     * @param string $string String to check.
     * @param string $query Substring to check for.
     * @return bool True if the string contains the query substring.
     */
    public static function stringContains($string, $query){
        return (strpos($string, $query) !== false);
    }

    /**
     * Returns the index to jump to the end of the next occurrence of the provided string.
     * If the string isn't found, the offset provided is to the end of the content.
     * @param int $i Character index to begin check at.
     * @param string $stringToCheck
     * @param $content
     * @return int The index to jump to the end of the next occurrence of the provided string, or the end of the content.
     */
    public static function offsetByNextString($i, $stringToCheck, $content){
        $stringToCheckLength = strlen($stringToCheck);
        $j = $i;

        while(!self::isMatching($j, $stringToCheck, $content))
        {
            $j++;
            if ($j + $stringToCheckLength >= strlen($content))
                return $j - 1;
        }

        $j += $stringToCheckLength;
        return $j;
    }


    /**
     * Determines if the provided text starts at the current index.
     * @param int $i Character index to begin check at.
     * @param string $stringToCheck String to check for a match.
     * @param $content
     * @return bool True: The provided text starts at the current index.
     */
    public static function isMatching($i, $stringToCheck, $content){
        $tagLength = strlen($stringToCheck);

        return ($i + $tagLength <= strlen($content) &&
            substr($content, $i, $tagLength) === $stringToCheck);
    }

    /**
     * Trims all leading and trailing line breaks in the text current content.
     * @param $content
     * @return string
     */
    public static function trimTextBodies($content){
        $tempCurrentContent = trim($content);

        // Remove all preceding line breaks.
        $lineBreaks = ['<br>' , '<br/>', '<br />'];
        foreach ($lineBreaks as $lineBreak){
            $lineBreakLength = strlen($lineBreak);
            while (substr($tempCurrentContent, 0, $lineBreakLength) === $lineBreak){
                $tempCurrentContent = substr($tempCurrentContent, $lineBreakLength);
            }
        }

        // Remove all trailing line breaks.
        foreach ($lineBreaks as $lineBreak){
            $lineBreakLength = strlen($lineBreak);
            while (substr($tempCurrentContent, strlen($tempCurrentContent)-$lineBreakLength) === $lineBreak){
                $tempCurrentContent = substr($tempCurrentContent, 0, -$lineBreakLength);
            }
        }

        // If text is only line breaks, spaces, and dashes, ignore it entirely.
        $tempCurrentContentBlank = $tempCurrentContent;
        foreach ($lineBreaks as $lineBreak){
            $tempCurrentContentBlank = str_replace($lineBreak, '', $tempCurrentContentBlank);
        }
        $tempCurrentContentBlank = str_replace('<p>', '', $tempCurrentContentBlank);
        $tempCurrentContentBlank = str_replace('</p>', '', $tempCurrentContentBlank);
        $tempCurrentContentBlank = str_replace('-', '', $tempCurrentContentBlank);
        $tempCurrentContentBlank = str_replace(' ', '', $tempCurrentContentBlank);

        if ($tempCurrentContentBlank === ''){
            return '';
        } else {
            return trim($tempCurrentContent);
        }
    }

    /**
     * Gets the filename from the provided URL.
     * @param string $url URL to extract the filename from.
     * @see http://krypted.com/utilities/html-encoding-reference/
     * @return string The filename, or empty if no filename is found (indicated by lack of extension).
     */
    public static function getFileNameFromUrl($url){
        // Get last item of URL
        $urlPieces = explode('/', $url);
        $fileName = $urlPieces[count($urlPieces) - 1];
        if (!strpos($fileName, '.')) return '';

        // Decode URL Encoding
        $urlEncodings = [
            '2C' => ',',
            '20' => ' ',
            '23' => '#',
            '26' => '&',
            '27' => '\'',    // technically is ‘ but that is not readily available on keyboards. Simplifying.
            '28' => '(',
            '29' => ')',
            '40' => '@',
        ];

        foreach ($urlEncodings as $key => $AsciiCharacter){
            $urlEncoding = $key;
            for ($i = 0; $i < 3; $i++){ // Iterations 2 & 3 handle double encoding, which is common for Picasa URLs.
                $fileName = str_replace('%'. $urlEncoding, $AsciiCharacter, $fileName);
                $urlEncoding = '25' . $urlEncoding;
            }
        }
        return $fileName;
    }


    /**
     * Parses string to be compatible with MySQL.
     * @param \mysqli $mysqli
     * @param string $value Value to parse.
     * @return string MySQL compatible string.
     */
    public static function parseToMySql(\mysqli $mysqli, $value) {
        return $mysqli->real_escape_string($value);
    }

    /**
     * Parses string to be compatible with MySQL, such that an empty value is properly inserted as NULL.
     * @param \mysqli $mysqli
     * @param string $value Value to parse.
     * @return string MySQL compatible string.
     */
    public static function parseToMySqlPotentialNull(\mysqli $mysqli, $value) {
        if (!empty($value)){
            $value = ParseToMySql($mysqli, $value);
        }
        return !empty($value)? "'$value'" : "NULL";
    }



    /**
     * Fixes the html text to correct for symbols and other character sets for foreign names by encoding data to UTF8.
     * @see https://stackoverflow.com/questions/4095899/utf8-encoding-problem-with-good-examples
     * @param $text
     * @return array|string
     */
    public static function cleanHTMLText($text){
        // $str="Ã«en twÃ©Ã© drÃ¯eviÃªr vijf zes Ã§   nnneeeeeeggeeeeennn";
        // $string2 = html_entity_decode($str);
        // echo utf8_decode($string2);
        return utf8_decode($text);
    }


    /**
     * Calls helper function for HTML 4 entity decoding.
     * @see http://www.lazycat.org/software/html_entity_decode_full.phps
     * @see https://stackoverflow.com/questions/34066638/decode-html-entities-in-php
     * @see https://stackoverflow.com/questions/2651711/convert-php-entities-like-ndash-or-scaron-to-their-applicable-characters
     * @param string $string
     * @param int $quotes
     * @param string $charset
     * @return string
     */
    public static function decode_entities_full($string, $quotes = ENT_COMPAT, $charset = 'ISO-8859-1') {
        // &acirc;&#128;&#156; with '
        // &acirc;&#128;&#157; with '
        // &Atilde;&ordf; with ê
        // For preg_replace_callback, an issue as fixed by considering the following: https://stackoverflow.com/questions/6575008/preg-replace-callback-problem
        return html_entity_decode(preg_replace_callback('/&([a-zA-Z][a-zA-Z0-9]+);/', array('markpthomas\library\StringHelper', 'convert_entity'), $string), $quotes, $charset);
    }

    /**
     * Helper function for decode_entities_full().
     *
     * This contains the full HTML 4 Recommendation listing of entities, so the default to discard
     * entities not in the table is generally good. Pass false to the second argument to return
     * the faulty entity unmodified, if you're ill or something.
     * @see http://www.lazycat.org/software/html_entity_decode_full.phps
     * @param $matches
     * @param bool $destroy
     * @return string
     */
    public static function convert_entity($matches, $destroy = true) {
        static $table = array(
            'quot' => '&#34;',
            'amp' => '&#38;',
            'lt' => '&#60;',
            'gt' => '&#62;',
            'OElig' => '&#338;',
            'oelig' => '&#339;',
            'Scaron' => '&#352;',
            'scaron' => '&#353;',
            'Yuml' => '&#376;',
            'circ' => '&#710;',
            'tilde' => '&#732;',
            'ensp' => '&#8194;',
            'emsp' => '&#8195;',
            'thinsp' => '&#8201;',
            'zwnj' => '&#8204;',
            'zwj' => '&#8205;',
            'lrm' => '&#8206;',
            'rlm' => '&#8207;',
            'ndash' => '&#8211;',
            'mdash' => '&#8212;',
            'lsquo' => '&#8216;',
            'rsquo' => '&#8217;',
            'sbquo' => '&#8218;',
            'ldquo' => '&#8220;',
            'rdquo' => '&#8221;',
            'bdquo' => '&#8222;',
            'dagger' => '&#8224;',
            'Dagger' => '&#8225;',
            'permil' => '&#8240;',
            'lsaquo' => '&#8249;',
            'rsaquo' => '&#8250;',
            'euro' => '&#8364;',
            'fnof' => '&#402;',
            'Alpha' => '&#913;',
            'Beta' => '&#914;',
            'Gamma' => '&#915;',
            'Delta' => '&#916;',
            'Epsilon' => '&#917;',
            'Zeta' => '&#918;',
            'Eta' => '&#919;',
            'Theta' => '&#920;',
            'Iota' => '&#921;',
            'Kappa' => '&#922;',
            'Lambda' => '&#923;',
            'Mu' => '&#924;',
            'Nu' => '&#925;',
            'Xi' => '&#926;',
            'Omicron' => '&#927;',
            'Pi' => '&#928;',
            'Rho' => '&#929;',
            'Sigma' => '&#931;',
            'Tau' => '&#932;',
            'Upsilon' => '&#933;',
            'Phi' => '&#934;',
            'Chi' => '&#935;',
            'Psi' => '&#936;',
            'Omega' => '&#937;',
            'alpha' => '&#945;',
            'beta' => '&#946;',
            'gamma' => '&#947;',
            'delta' => '&#948;',
            'epsilon' => '&#949;',
            'zeta' => '&#950;',
            'eta' => '&#951;',
            'theta' => '&#952;',
            'iota' => '&#953;',
            'kappa' => '&#954;',
            'lambda' => '&#955;',
            'mu' => '&#956;',
            'nu' => '&#957;',
            'xi' => '&#958;',
            'omicron' => '&#959;',
            'pi' => '&#960;',
            'rho' => '&#961;',
            'sigmaf' => '&#962;',
            'sigma' => '&#963;',
            'tau' => '&#964;',
            'upsilon' => '&#965;',
            'phi' => '&#966;',
            'chi' => '&#967;',
            'psi' => '&#968;',
            'omega' => '&#969;',
            'thetasym' => '&#977;',
            'upsih' => '&#978;',
            'piv' => '&#982;',
            'bull' => '&#8226;',
            'hellip' => '&#8230;',
            'prime' => '&#8242;',
            'Prime' => '&#8243;',
            'oline' => '&#8254;',
            'frasl' => '&#8260;',
            'weierp' => '&#8472;',
            'image' => '&#8465;',
            'real' => '&#8476;',
            'trade' => '&#8482;',
            'alefsym' => '&#8501;',
            'larr' => '&#8592;',
            'uarr' => '&#8593;',
            'rarr' => '&#8594;',
            'darr' => '&#8595;',
            'harr' => '&#8596;',
            'crarr' => '&#8629;',
            'lArr' => '&#8656;',
            'uArr' => '&#8657;',
            'rArr' => '&#8658;',
            'dArr' => '&#8659;',
            'hArr' => '&#8660;',
            'forall' => '&#8704;',
            'part' => '&#8706;',
            'exist' => '&#8707;',
            'empty' => '&#8709;',
            'nabla' => '&#8711;',
            'isin' => '&#8712;',
            'notin' => '&#8713;',
            'ni' => '&#8715;',
            'prod' => '&#8719;',
            'sum' => '&#8721;',
            'minus' => '&#8722;',
            'lowast' => '&#8727;',
            'radic' => '&#8730;',
            'prop' => '&#8733;',
            'infin' => '&#8734;',
            'ang' => '&#8736;',
            'and' => '&#8743;',
            'or' => '&#8744;',
            'cap' => '&#8745;',
            'cup' => '&#8746;',
            'int' => '&#8747;',
            'there4' => '&#8756;',
            'sim' => '&#8764;',
            'cong' => '&#8773;',
            'asymp' => '&#8776;',
            'ne' => '&#8800;',
            'equiv' => '&#8801;',
            'le' => '&#8804;',
            'ge' => '&#8805;',
            'sub' => '&#8834;',
            'sup' => '&#8835;',
            'nsub' => '&#8836;',
            'sube' => '&#8838;',
            'supe' => '&#8839;',
            'oplus' => '&#8853;',
            'otimes' => '&#8855;',
            'perp' => '&#8869;',
            'sdot' => '&#8901;',
            'lceil' => '&#8968;',
            'rceil' => '&#8969;',
            'lfloor' => '&#8970;',
            'rfloor' => '&#8971;',
            'lang' => '&#9001;',
            'rang' => '&#9002;',
            'loz' => '&#9674;',
            'spades' => '&#9824;',
            'clubs' => '&#9827;',
            'hearts' => '&#9829;',
            'diams' => '&#9830;',
            'nbsp' => '&#160;',
            'iexcl' => '&#161;',
            'cent' => '&#162;',
            'pound' => '&#163;',
            'curren' => '&#164;',
            'yen' => '&#165;',
            'brvbar' => '&#166;',
            'sect' => '&#167;',
            'uml' => '&#168;',
            'copy' => '&#169;',
            'ordf' => '&#170;',
            'laquo' => '&#171;',
            'not' => '&#172;',
            'shy' => '&#173;',
            'reg' => '&#174;',
            'macr' => '&#175;',
            'deg' => '&#176;',
            'plusmn' => '&#177;',
            'sup2' => '&#178;',
            'sup3' => '&#179;',
            'acute' => '&#180;',
            'micro' => '&#181;',
            'para' => '&#182;',
            'middot' => '&#183;',
            'cedil' => '&#184;',
            'sup1' => '&#185;',
            'ordm' => '&#186;',
            'raquo' => '&#187;',
            'frac14' => '&#188;',
            'frac12' => '&#189;',
            'frac34' => '&#190;',
            'iquest' => '&#191;',
            'Agrave' => '&#192;',
            'Aacute' => '&#193;',
            'Acirc' => '&#194;',
            'Atilde' => '&#195;',
            'Auml' => '&#196;',
            'Aring' => '&#197;',
            'AElig' => '&#198;',
            'Ccedil' => '&#199;',
            'Egrave' => '&#200;',
            'Eacute' => '&#201;',
            'Ecirc' => '&#202;',
            'Euml' => '&#203;',
            'Igrave' => '&#204;',
            'Iacute' => '&#205;',
            'Icirc' => '&#206;',
            'Iuml' => '&#207;',
            'ETH' => '&#208;',
            'Ntilde' => '&#209;',
            'Ograve' => '&#210;',
            'Oacute' => '&#211;',
            'Ocirc' => '&#212;',
            'Otilde' => '&#213;',
            'Ouml' => '&#214;',
            'times' => '&#215;',
            'Oslash' => '&#216;',
            'Ugrave' => '&#217;',
            'Uacute' => '&#218;',
            'Ucirc' => '&#219;',
            'Uuml' => '&#220;',
            'Yacute' => '&#221;',
            'THORN' => '&#222;',
            'szlig' => '&#223;',
            'agrave' => '&#224;',
            'aacute' => '&#225;',
            'acirc' => '&#226;',
            'atilde' => '&#227;',
            'auml' => '&#228;',
            'aring' => '&#229;',
            'aelig' => '&#230;',
            'ccedil' => '&#231;',
            'egrave' => '&#232;',
            'eacute' => '&#233;',
            'ecirc' => '&#234;',
            'euml' => '&#235;',
            'igrave' => '&#236;',
            'iacute' => '&#237;',
            'icirc' => '&#238;',
            'iuml' => '&#239;',
            'eth' => '&#240;',
            'ntilde' => '&#241;',
            'ograve' => '&#242;',
            'oacute' => '&#243;',
            'ocirc' => '&#244;',
            'otilde' => '&#245;',
            'ouml' => '&#246;',
            'divide' => '&#247;',
            'oslash' => '&#248;',
            'ugrave' => '&#249;',
            'uacute' => '&#250;',
            'ucirc' => '&#251;',
            'uuml' => '&#252;',
            'yacute' => '&#253;',
            'thorn' => '&#254;',
            'yuml' => '&#255;'
        );
        if (isset($table[$matches[1]])) return $table[$matches[1]];
        // else
        return $destroy ? '' : $matches[0];
    }
} 