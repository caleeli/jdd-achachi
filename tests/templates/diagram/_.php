<?php
return function ($data) {
    return [
        'tag' => isset($data['tag']) ? $data['tag'] : 'g',
        'imports' => function ($sequences, $mixins) {
            $imports = [];
            foreach ($sequences as $e) {
                if (@$e['is_recursive']) {
                    continue;
                }
                $imports[] = [
                    'name' => ucfirst(camel_case($e['ref'])),
                    'path' => str_replace('\/', '/',
                        json_encode('./' . ucfirst(camel_case($e['ref'])))),
                ];
            }
            if (is_array($mixins)) {
                foreach ($mixins as $e) {
                    $imports[] = [
                        'name' => $e,
                        'path' => str_replace('\/', '/',
                            json_encode('../mixins/' . $e)),
                    ];
                }
            }
            return $imports;
        },
        'importComponent' => function ($component) {
            $name = ucfirst(camel_case($component['ref']));
            $path = str_replace('\/', '/',
                json_encode('./' . ucfirst(camel_case($component['ref']))));
            return @$component['is_recursive'] ? $name . ': () => import(' . $path . ')' : $name;
        },
        'sequenceGetter' => function ($e) use ($data) {
            $arrayGetter = isset($data['arrayGetter']) ? $data['arrayGetter'] : 'getArray';
            $singleGetter = isset($data['singleGetter']) ? $data['singleGetter'] : 'getItem';
            $getter = @$e['multiple'] ? $arrayGetter : $singleGetter;
            $name = json_encode(ucfirst(camel_case($e['ref'])));
            return $getter ? $getter . '(' . $name . ')' : 'value.' . camel_case($e['ref']);
        },
    ];
};
