<?php

namespace App\Services;


use App\Shortlink;
use App\Statistics;
use Illuminate\Support\Str;

class ShortlinkService
{

    function __construct() {
        $this->CHARSET = env('SHORTENER_CHARSET','abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789');
        $this->SALT = env('SHORTENER_SALT','qqq');
        $this->PADDING = (int) env('SHORTENER_PADDING','1');
    }

    /**
     * Create shortened link
     * @param $url original link
     * @return array id and hash of shortened link
     */
    public function addLink($url)
    {
        $url = urlencode($url);
        $link = Shortlink::where('url', $url)->first();

        if (!$link) {
            $link = new Shortlink();
            $link->setAttribute('url', $url);
            $link->save();

            $statistics = new Statistics();
            $link->setAttribute('counter', 0);
            $link->setAttribute('id', $link->id);
            $statistics->save();
        }

        return [$link->getAttribute('id'), $this->encode($link->id)];
    }

    /**
     * Get link by hash
     * @param $size start size
     * @return string
     */
    public function getLink($hash)
    {
        $id = $this->decode($hash);

        if ($id){
            $link = Shortlink::find($id);
            if ($link) {
                return $link;
            }
        }

        return null;
    }

    /**
     * Converts an id to an encoded string
     *
     * @param int $n Number to encode
     * @return string Encoded string
     */
    public function encode($id)
    {
        $suff = 0;
        if ($this->PADDING > 0 && !empty($this->SALT)) {
            $suff = $this->getSeed($id, $this->SALT, $this->PADDING);

            $id = (int)($suff . $id);
        }
        return $this->numToAlpha($id, $this->CHARSET);

    }

    /**
     * Converts an encoded string into a number
     *
     * @param string $s String to decode
     * @return int Decoded number
     */
    public function decode($link)
    {
        $id = $this->alphaToNum($link, $this->CHARSET);
        return (!empty($this->SALT)) ? substr($id, $this->PADDING) : $id;
    }

    /**
     * Gets a number for padding based on a salt
     *
     * @param int $n Number to pad
     * @param string $salt Salt string
     * @param int $padding Padding length
     * @return int Number for padding
     */
    private function getSeed($n, $salt, $padding)
    {
        $hash = md5($n . $salt);
        $dec = hexdec(substr($hash, 0, $padding));
        $num = $dec % pow(10, $padding);
        if ($num == 0) $num = 1;
        $num = str_pad($num, $padding, '0');
        return $num;
    }

    /**
     * Converts a number to an alpha-numeric string
     *
     * @param int $id Number to convert
     * @param string $set String of characters for conversion
     * @return string Alpha-numeric string
     */
    private function numToAlpha($id, $set)
    {
        $len = strlen($set);
        $m = $id % $len;
        if ($id - $m == 0) return substr($set, $id, 1);
        $hash = '';
        while ($m > 0 || $id > 0) {
            $hash = substr($set, $m, 1) . $hash;
            $id = ($id - $m) / $len;
            $m = $id % $len;
        }
        return $hash;
    }

    /**
     * Converts an alpha numeric string to a number
     *
     * @param string $hash Alpha-numeric string to convert
     * @param string $set String of characters for conversion
     * @return int Converted number
     */
    private function alphaToNum($hash, $set)
    {
        $slen = strlen($set);
        $hlen = strlen($hash);
        for ($id = 0, $i = 0; $i < $hlen; $i++) {
            $id += strpos($set, substr($hash, $i, 1)) * pow($slen, $hlen - $i - 1);
        }
        return $id;
    }

}