<?php

namespace App\Http\Controllers;

use App\Services\ShortlinkService;
use App\Statistics;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

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
        $service = new ShortlinkService();
        $link = $service->getLink($hash);
        if ($link){
            $statistics = Statistics::find($link->id);
            $statistics->counter++;
            $statistics->save();
        }

        return redirect(urldecode($link->url));
    }
}
