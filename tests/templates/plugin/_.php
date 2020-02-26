<?php
return function ($data) {
    extract($data);
    return [
        "namespace" => "$vendor\\$name",
    ];
};
