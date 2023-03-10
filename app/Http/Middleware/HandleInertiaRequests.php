<?php

namespace App\Http\Middleware;

use App\Helpers\Acess\Authorize;
use App\Models\Permission;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Auth;
use Inertia\Middleware;
use Tightenco\Ziggy\Ziggy;

class HandleInertiaRequests extends Middleware
{
    /**
     * The root template that is loaded on the first page visit.
     *
     * @var string
     */
    protected $rootView = 'app';

    /**
     * Determine the current asset version.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return string|null
     */
    public function version(Request $request)
    {
        return parent::version($request);
    }

    /**
     * Define the props that are shared by default.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return mixed[]
     */
    public function share(Request $request)
    {
        return array_merge(parent::share($request), [
            'title' => config()->get('app.name'),
            'auth' => [
                'user' => $request->user($request->is('admin*') ? 'admin' : null),
            ],
            'ziggy' => function () use ($request) {
                return array_merge((new Ziggy)->toArray(), [
                    'location' => $request->url(),
                ]);
            },
            'menu' => function() {
                return collect(config()->get('menu', []))->map(function($item) {
                    return [
                        'label' => Arr::get($item, 'label'),
                        'items' => Arr::where(Arr::get($item, 'items', []), fn($v) => !isset($v['can']) || Authorize::any($v['can']))
                    ];
                })->filter(fn($item) => isset($item['items']) && count($item['items']) > 0);
            },
            'flash' => [
                'message' => fn () => $request->session()->get('message')
            ],
        ]);
    }
}
