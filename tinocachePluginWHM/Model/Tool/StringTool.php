<?php

namespace tinocachePlugin\Model\Tool;

class StringTool
{

    public static function getStringBetween($string, $start, $end)
    {
        $string = ' '.$string;
        $ini    = strpos($string, $start);

        if ($ini == 0)
        {
            return '';
        }

        $ini += strlen($start);
        $len = strpos($string, $end, $ini) - $ini;

        return substr($string, $ini, $len);
    }

    public static function generatePassword($length = 16)
    {
        $chars = ['0123456789', 'abcdefghijklmnopqrstuvwxyz', 'ABCDEFGHIJKLMNOPQRSTUVWXYZ', '[]{};:.,!@#$%^&*()'];

        $pass = [];

        for ($i = 0; $i < (int)($length / 4); $i++)
        {
            for ($j = 0; $j < 4; $j++)
            {
                $pass[] = $chars[$j][rand(0, strlen((string)$chars[$j]) - 1)];
            }
        }

        shuffle($pass);

        return implode($pass);
    }

    public static function generateUsername($string = "")
    {
        return trim(uniqid($string));
    }

    /**
     * Generates strong password.
     *
     * @param integer $length   Length of password. Can be set between 1 and 2^17 of chars.
     * @param boolean $upperCs  Should function use uppercases?
     * @param boolean $lowerCs  Should function use lowercases?
     * @param boolean $numbers  Should function use numbers?
     * @param boolean $special  Should function use special characters?
     * @param array   $ownChars Additional characters passed in array. Example: ['a', 'd', '=', '.']
     *
     * @return string Generated password.
     */
    private static function genPass($length = 8, $upperCs = true, $lowerCs = true, $numbers = true, $special = true, array $ownChars = [])
    {
        if (!($upperCs || $lowerCs || $numbers || $special) && empty($ownChars) || $length < 1)
        {
            return "";
        }

        $len = ($length > pow(2, 17)) ? pow(2, 17) : $length;

        (!$upperCs) ?: $chars[] = range("A", "Z");
        (!$lowerCs) ?: $chars[] = range("a", "z");
        (!$numbers) ?: $chars[] = range("0", "9");
        (!$special) ?: $chars[] = array_merge(range("!", "/"), range(":", "@"), range("[", "`"));
        (empty($ownChars)) ?: $chars[] = $ownChars;

        for ($i = $j = 0; $i < $len; $i++)
        {
            ($i % (count($chars)) > 0) ? $j++ : $j = 0;
            $pass[] = $chars[$j][rand(0, count($chars[$j]) - 1)];
        }

        shuffle($pass);

        return implode($pass);
    }
}
