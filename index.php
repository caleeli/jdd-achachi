<?php

use JDD\Achachi\Builder\FolderTemplate;

require_once 'vendor/autoload.php';
require_once '/Users/davidcallizaya/Netbeans/audit-extjs/vendor/autoload.php';

$path = 'tests/templates/diagram';

$builder = new FolderTemplate($path);

$def = [
    'tag' => 'div',
    'components' => [
        [
            'name' => 'JddForm',
            'mixins' => ['CanContain', 'Configurable'],
            'template' => '<div class="can-contain">
        <jdd-container v-for="(jddContainer, index) in jddContainers" :key="jddContainer.$.id" v-model="jddContainers[index]" :root="root" />
    </div>',
            'sequences' => [
                [
                    'ref' => 'jdd-container',
                    'multiple' => true,
                ],
            ],
            'style' => '<style scoped>
    .can-contain {
        border: 1px dashed black;
        padding-bottom: 3em;
    }
</style>
',
        ],
        [
            'name' => 'JddContainer',
            'mixins' => ['CanContain', 'Configurable', 'Additionable'],
            'sequences' => [
                [
                    'ref' => 'jdd-container',
                    'multiple' => true,
                    'is_recursive' => true,
                ],
            ],
        ],
        [
            'name' => 'JddControl',
            'mixins' => ['Configurable', 'Additionable'],
            'template' => '<component :is="component" />',
            'sequences' => [
            ],
            'attributes' => [
            ],
        ],
        [
            'name' => 'Inspector',
            'mixins' => ['Inspector'],
            'template' => '<div>
        <property v-for="(property, index) in properties" :key="index" v-model="properties[index]" :root="root" />
    </div>',
            'sequences' => [
                [
                    'ref' => 'property',
                    'multiple' => true,
                ],
            ],
            'attributes' => [
            ],
        ],
        [
            'name' => 'Property',
            'template' => '<div class="form-group px-2">
        <div class="inspector-label text-muted">{{value.label}}</div>
        <input class="form-control" v-model="value.owner[value.key]">
    </div>',
            'mixins' => [],
            'sequences' => [
            ],
            'attributes' => [
            ],
            'style' => '<style scoped>
.inspector-label {
  position: absolute;
  right: 2em;
  text-align: right;
  margin-top: 0.5em;
}
</style>'
        ],
        [
            'name' => 'Palete',
            'mixins' => ['Palete'],
            'sequences' => [
                [
                    'ref' => 'palete-item',
                    'multiple' => true,
                ],
            ],
            'attributes' => [
            ],
        ],
        [
            'name' => 'PaleteItem',
            'mixins' => ['PaleteItem'],
            'sequences' => [
            ],
            'attributes' => [
            ],
        ],
    ]
];
$builder->build($def, '/Users/davidcallizaya/Netbeans/vue-jdd-form');

return;

$builder = new FolderTemplate($path);

$def = [
    'arrayGetter' => 'getElementsByTagName',
    'singleGetter' => 'getElementByTagName',
    'components' => [
        [
            'name' => 'Definitions',
            'mixins' => ['Element'],
            'sequences' => [
                [
                    'ref' => 'bpmn-diagram',
                    'multiple' => false,
                ],
            ],
        ],
        [
            'name' => 'BpmnDiagram',
            'mixins' => ['Element'],
            'sequences' => [
                [
                    'ref' => 'bpmn-plane',
                    'multiple' => false,
                ],
            ],
        ],
        [
            'name' => 'BpmnPlane',
            'mixins' => ['Element'],
            'sequences' => [
                [
                    'ref' => 'bpmn-edge',
                    'multiple' => true,
                ],
                [
                    'ref' => 'bpmn-shape',
                    'multiple' => true,
                ],
            ],
            'attributes' => [
                [
                    'name' => 'bpmnElement',
                    'type' => 'getElementById',
                ],
            ],
        ],
        [
            'name' => 'BpmnShape',
            'template' => '<component :is="component" :shape="value" :root="root" v-model="this.bpmnElement" />',
            'mixins' => ['BPMNShape'],
            'sequences' => [
            ],
            'attributes' => [
                [
                    'name' => 'bpmnElement',
                    'type' => 'getElementById',
                ],
            ],
        ],
        [
            'name' => 'BpmnEdge',
            'template' => '<component :is="component" :shape="value" :root="root" v-model="this.bpmnElement" />',
            'mixins' => ['BPMNEdge'],
            'sequences' => [
            ],
            'attributes' => [
                [
                    'name' => 'bpmnElement',
                    'type' => 'getElementById',
                ],
            ],
        ],
    ]
];
$builder->build($def, '/Users/davidcallizaya/Netbeans/vue-jdd-diagram');

return;

// TEST 1
$path = 'tests/templates/plugin';

$builder = new FolderTemplate($path);

$def = [
    'vendor' => 'JDD',
    'name' => 'Achachi',
    'description' => 'Crea módulos JDD',
    'author' => 'david',
    'email' => 'davidcallizaya@gmail.com',
    'components' => [
    ]
];
$builder->build($def, './output');

$zip = new ZipArchive();
$ret = $zip->open(store_path('app/public/output.zip'), ZipArchive::CREATE | ZipArchive::OVERWRITE);
$source = './output';
$paths = [$source];
while ($path = array_shift($paths)) {
    foreach (glob($path . '/*') as $filename) {
        if (is_dir($filename)) {
            $paths[] = $filename;
        } else {
            $zip->addFile($filename, substr($filename, strlen($source) + 1));
        }
    }
}
$zip->close();
