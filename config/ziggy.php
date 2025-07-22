<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Route Filter
    |--------------------------------------------------------------------------
    |
    | Filters are used to filter routes by name or group, allowing you to
    | conditionally disable them.
    |
    */

    'route_filter' => ['web', 'api'],

    /*
    |--------------------------------------------------------------------------
    | Route Groups
    |--------------------------------------------------------------------------
    |
    | You can pass an array of route groups to only make certain sets of
    | routes available to JavaScript.
    |
    */
    'groups' => [
        'web',
    ],

    /*
    |--------------------------------------------------------------------------
    | Route URL Generation
    |--------------------------------------------------------------------------
    |
    | This value determines whether the base URL should be automatically
    | determined from the incoming request or whether you want to
    | explicitly set it yourself.
    |
    */
    'url' => null,

    /*
    |--------------------------------------------------------------------------
    | Absolute URLs
    |--------------------------------------------------------------------------
    |
    | This value determines whether to generate absolute URLs, e.g. "https://example.com/foo/bar".
    | By default, relative URLs are generated, e.g. "/foo/bar".
    |
    */
    'absolute' => false,

    /*
    |--------------------------------------------------------------------------
    | Asset URL
    |--------------------------------------------------------------------------
    |
    | This value sets the path to your asset manifest. You should use this if you
    | are using a bundler like webpack and need versioned assets.
    |
    */
    'asset_url' => null,

    /*
    |--------------------------------------------------------------------------
    | Ziggy Version
    |--------------------------------------------------------------------------
    |
    | This value is used to enable the Ziggy @routes directive. This is useful
    | if you're using server-side rendering with a tool like Inertia.js.
    |
    */
    'blade' => [
        'enabled' => true,
        'file_path' => resource_path('views/vendor/ziggy'),
    ],

    /*
    |--------------------------------------------------------------------------
    | Skip Route Name
    |--------------------------------------------------------------------------
    |
    | This value determines whether to skip the route name when generating
    | URLs. By default, Ziggy will include the route name in the generated
    | JavaScript object.
    |
    */
    'skip-route-name' => false,
];






