<?php

namespace App\Http\Controllers;

use App\Services\ShortlinkService;
use App\Shortlink;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Validator;

class ShortlinkController extends Controller

{

    /**
     * Add link
     *
     * @return \Illuminate\Http\Response
     */
    public function addLink(Request $request)
    {
        $this->validate($request, [
            'url' => 'required|max:2048|url',
        ]);

        $service = new ShortlinkService();
        return new Response($service->addLink($request->get('url')));
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Shortlink  $shortlink
     * @return redirect to url
     */
    public function show($hash)
    {
        $link = Shortlink::where('hash', $hash)->firstOrFail();
        $link->counter++;
        $link->save();

        return redirect(urldecode($link->url));
    }
}
