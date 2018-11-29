<?php
  
$this->bind("/api/grp/:group", function($params) {  
    
    header("Content-Type: application/json");
    $group = $params["group"];
    $allS = [];
    $allC = [];
    $allC = $this->module("collections")->collections();
    $allS = $this->module("singletons")->singletons();
    $options = [];

    if ($lang = $this->param("lang", false)) $options["lang"] = $lang;
    $options["populate"] = true;
    if ($ignoreDefaultFallback = $this->param("ignoreDefaultFallback", false)) $options["ignoreDefaultFallback"] = $ignoreDefaultFallback;
    if ($user) $options["user"] = $user;

    foreach($allS as $key => $value) { 
        $singleton = $this->module("singletons")->getData($key, $options);
        if($value["group"] == $group){ 
            $singletons[] = $singleton; 
        }
    }
    
    foreach($allC as $key => $value) { 
        //$collections = $this->module("collections")->getData($key, $options);
        $collection = $this->module("collections")->find($key, $options);
        if($value["group"] == $group){
            $collections[] = $collection;
        }
    }
    $returnArray = [];
    $returnArray["singletons"] = $singletons;
    $returnArray["collections"] = $collections;
    echo json_encode($returnArray);
    
    
    exit();


});
