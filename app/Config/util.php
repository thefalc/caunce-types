<?php

/**
 * Class for generic utility functions.
 *
 * @author sfalc
 */
class Util {
    static function removeNonNumeric($s) {
        return ereg_replace("[^0-9-]", "", $s);
    }

    static function getValue($value, $start_string, $end_string) {
        $start = strpos($value, $start_string);

        if($start === false) {
            echo "bad ".$value." - ".$start_string;
            exit;
        }

        $start += strlen($start_string);

        $end = strpos($value, $end_string, $start);

        return trim(strip_tags(substr($value, $start, $end - $start)));
    }

    static function getResponse($url, $fields = false, $ssl = false, $username = false) {
        $useragent = "Mozilla/5.0 (Windows; U; Windows NT 5.1; en-US; rv:1.8.1.1) Gecko/20061204 Firefox/2.0.0.1";
        
        $fields_string = "";
        if ($fields !== false) {
            foreach($fields as $key => $value) { $fields_string .= $key.'='.$value.'&'; }
            rtrim($fields_string, '&');
        }
        
        //open connection
        $ch = curl_init();

        if($username) {
            curl_setopt($ch, CURLOPT_USERPWD, $username);    
        }

        //set the url, number of POST vars, POST data
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 60);
        curl_setopt($ch, CURLOPT_TIMEOUT, 60);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
        curl_setopt($ch, CURLOPT_USERAGENT, $useragent);
        curl_setopt($ch, CURLOPT_HTTP_VERSION, CURL_HTTP_VERSION_1_0 );
        if ($fields !== false) {
            curl_setopt($ch, CURLOPT_POST, count($fields));
            curl_setopt($ch, CURLOPT_POSTFIELDS, $fields_string);
        } 
        if ($ssl) {
            curl_setopt($ch, CURLOPT_VERBOSE, 1);
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        }

        //execute post
        $result = curl_exec($ch);

        $httpcode = curl_getinfo($ch, CURLINFO_HTTP_CODE);

        // if ($error = curl_error($ch)) { 
        //     echo "Error: $error<br />\n"; 
        // } 

        //close connection
        curl_close($ch);
        
        return $result;
   }

    static function escapeDoubleQuotes($data) {
        return '"' . preg_replace('/"/','""',$data) . '"';
    }
    
    static function getIfSet(&$thing, $numeric=false) {
        if (isset($thing)) {
            return $thing;
        }
        if ($numeric) {
            return 0;
        } else {
            return "";
        }
    }
    
    static function firstName($full_name) {
        $parts = explode(" ", $full_name);
        $first_name = array_shift($parts);
        return empty($first_name) ? "" : $first_name;
    }

    static function lastName($full_name) {
        $parts = explode(" ", $full_name);
        $first_name = array_shift($parts);
        $last_name = implode(" ", $parts);
        return empty($last_name) ? "" : $last_name;
    }

    static function addOrdinalNumberSuffix($num) {
        if (!in_array(($num % 100), array(11, 12, 13))) {
            switch ($num % 10) {
                // Handle 1st, 2nd, 3rd
                case 1: return number_format($num, 0) . 'st';
                case 2: return number_format($num, 0) . 'nd';
                case 3: return number_format($num, 0) . 'rd';
            }
        }
        return number_format($num, 0) . 'th';
    }
    
    static function capitalizeNames($str, $is_name = true) {
        if(preg_match('/^(?=.*?[A-Z])(?=.*?[a-z])/', $str)) return $str;

        // exceptions to standard case conversion
        if ($is_name) {
            $all_uppercase = '';
            $all_lowercase = 'De La|De Las|Der|Van De|Van Der|Vit De|Von|Or|And';
        } else {
            // addresses, essay titles ... and anything else
            $all_uppercase = 'Po|Rr|Se|Sw|Ne|Nw';
            $all_lowercase = 'A|And|As|By|In|Of|Or|To';
        }
        $prefixes = 'Mc';
        $suffixes = "'S";

        // captialize all first letters
        $str = preg_replace('/\\b(\\w)/e', 'strtoupper("$1")', strtolower(trim($str)));

        if ($all_uppercase) {
            // capitalize acronymns and initialisms e.g. PHP
            $str = preg_replace("/\\b($all_uppercase)\\b/e", 'strtoupper("$1")', $str);
        }
        if ($all_lowercase) {
            // decapitalize short words e.g. and
            if ($is_name) {
                // all occurences will be changed to lowercase
                $str = preg_replace("/\\b($all_lowercase)\\b/e", 'strtolower("$1")', $str);
            } else {
                // first and last word will not be changed to lower case (i.e. titles)
                $str = preg_replace("/(?<=\\W)($all_lowercase)(?=\\W)/e", 'strtolower("$1")', $str);
            }
        }
        if ($prefixes) {
            // capitalize letter after certain name prefixes e.g 'Mc'
            $str = preg_replace("/\\b($prefixes)(\\w)/e", '"$1".strtoupper("$2")', $str);
        }
        if ($suffixes) {
            // decapitalize certain word suffixes e.g. 's
            $str = preg_replace("/(\\w)($suffixes)\\b/e", '"$1".strtolower("$2")', $str);
        }
        return $str;
    }
}
?>
