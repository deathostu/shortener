<?php

namespace App\Services;


use App\Shortlink;
use Illuminate\Support\Str;

class ShortlinkService
{
    const MIN_SIZE = 8;

    /**
     * Create shortened link
     * @param $url original link
     * @return array id and hash of shortened link
     */
    public function addLink($url) {
        $url = urlencode($url);
        $link = Shortlink::where('url', $url)->first();

        if (!$link) {
            $link = new Shortlink();
            $link->setAttribute('url', $url);
            $link->setAttribute('counter', 0);
            $link->setAttribute('hash', $this->generateHash(ShortlinkService::MIN_SIZE));
            $link->save();
        }

        return $link->only(['id', 'hash']);
    }

    /**
     * Generate unique random hash
     * @param $size start size
     * @return string
     */
    private function generateHash($size) {
        $hash = Str::random($size);
        while (Shortlink::where('hash', $hash)->first()){
            $hash = $this->generateHash($size + 1);
        }
        return $hash;
    }

}