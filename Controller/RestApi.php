<?php

namespace DataGroupApi\Controller;

class RestApi extends \LimeExtra\Controller {

    protected function before() {
        $this->app->response->mime = 'json';
    }

    public function get($dataGroupName) {
        $current_user = $this->module('cockpit')->getUser(); // this MAY be null if no valid token is provided to the API be the callee

        $allCollections = $this->module("collections")->collections();
        $allSingletons = $this->module("singletons")->singletons();

        // TODO: i'm not sure what this is supposed to be and if its necessary
        $options = [];
        if ($lang = $this->param("lang", false)) $options["lang"] = $lang;
        $options["populate"] = true;
        if ($ignoreDefaultFallback = $this->param("ignoreDefaultFallback", false)) $options["ignoreDefaultFallback"] = $ignoreDefaultFallback;
        if ($current_user) $options["user"] = $current_user;

        /*
         * gather both singletons+collections data
         */
        $singletons = [];
        foreach($allSingletons as $singletonName => $singletonSchema) {
            $allowed_through_token = $this->module('singletons')->hasaccess($singletonName, 'data');
            $allowed_through_public = $singletonSchema && ($singletonSchema['acl']['public']['data']??false)===true;

            if($singletonSchema['group'] === $dataGroupName && ($allowed_through_token || $allowed_through_public))
                $singletons[] = $this->module("singletons")->getData($singletonName, $options);
        }
        $collections = [];
        foreach($allCollections as $collectionName => $collectionSchema) {
            $allowed_through_token = $this->module('collections')->hasaccess($collectionName, 'entries_view');
            $allowed_through_public = $collectionSchema && ($collectionSchema['acl']['public']['entries_view']??false)===true;

            if($collectionSchema['group'] === $dataGroupName && ($allowed_through_token || $allowed_through_public))
                $collections[] = $this->module("collections")->find($collectionName, $options);
        }

        return json_encode(compact('singletons', 'collections'));
    }

}