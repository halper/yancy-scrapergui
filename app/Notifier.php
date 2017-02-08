<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Notifier extends Model
{
    //
    const TEXT_FILE = '../notifier.properties';
    var $consumer_key;
    var $consumer_secret;
    var $access_token;
    var $token_secret;
    var $gmail_username;
    var $gmail_password;

    function __construct()
    {
        $my_arr = $this->parse_properties();
        $this->consumer_key = $my_arr['ConsumerKey'];
        $this->consumer_secret = $my_arr['ConsumerSecret'];
        $this->access_token = $my_arr['AccessToken'];
        $this->token_secret = $my_arr['AccessTokenSecret'];
        $this->gmail_username = $my_arr['GMailUsername'];
        $this->gmail_password = $my_arr['GMailPassword'];
    }

    function parse_properties()
    {
        $result = array();
        $key = "";
        $value = "";
        $isWaitingOtherLine = false;
        $handle = fopen(self::TEXT_FILE, "r");
        if ($handle) {
            while (($line = fgets($handle)) !== false) {
                if (empty($line) || (!$isWaitingOtherLine && strpos($line, "#") === 0))
                    continue;
                $line = preg_replace('/\n/i', '', $line);
                if (!$isWaitingOtherLine) {
                    $key = substr($line, 0, strpos($line, '='));
                    $value = substr($line, strpos($line, '=') + 1, strlen($line));
                } else {
                    $value .= $line;
                }
                /* Check if ends with single '\' */
                if (strrpos($value, "\\") === strlen($value) - strlen("\\")) {
                    $value = substr($value, 0, strlen($value) - 1);
                    $isWaitingOtherLine = true;
                } else {
                    $isWaitingOtherLine = false;
                }

                $result[$key] = $value;
            }
        }
        fclose($handle);

        return $result;
    }

    public static function saveNewProps($request)
    {
        $myfile = fopen(self::TEXT_FILE, "w");
        $txt = "ConsumerKey=" . $request['consumer-key'] . PHP_EOL;
        fwrite($myfile, $txt);
        $txt = "ConsumerSecret=" . $request['consumer-secret'] . PHP_EOL;
        fwrite($myfile, $txt);
        $txt = "AccessToken=" . $request['access-token'] . PHP_EOL;
        fwrite($myfile, $txt);
        $txt = "AccessTokenSecret=" . $request['token-secret'] . PHP_EOL;
        fwrite($myfile, $txt);
        $txt = "GMailUsername=" . $request['username'] . PHP_EOL;
        fwrite($myfile, $txt);
        $txt = "GMailPassword=" . $request['password'];
        fwrite($myfile, $txt);
        fclose($myfile);
    }
}
