<?php

if (COCKPIT_API_REQUEST) {

    $this->on('cockpit.rest.init', function($routes) {
        $routes['datagroup'] = 'DataGroupApi\\Controller\\RestApi';
    });

    // allow public access to this addon's routes
    $this->on('cockpit.api.authenticate', function($data) {
        if ($data['user'] || $data['resource'] != 'datagroup') return;

        if (isset($data['query']['params'][1])) {
            // copied this from a cockpit core-module - without this our API requests are *always* blocked while we are not authenticated.
            // However this looks dangerous - not sure about it ...
            $data['authenticated'] = true;
            $data['user'] = ['_id' => null, 'group' => 'public'];
        }
    });
}
