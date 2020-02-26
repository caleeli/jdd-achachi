<template>
/*{if(@$template):}*/
    /*{$template*//*}*/

/*{else:}*/
    </*{$tag*/g/*}*/>
/*{foreach($sequences as $e):}*/        </*{$e['ref'] . (@$e['multiple'] ? ' v-for="(' . camel_case($e['ref']) . ', index) in ' . str_plural(camel_case($e['ref'])) . '" :key="' . camel_case($e['ref']) . '.$.id"' : '') */bpmn-plane/*}*/ v-model="/*{@$e['multiple'] ? str_plural(camel_case($e['ref'])) . '[index]' : camel_case($e['ref'])*/BpmnPlane/*}*/" :root="root" />
/*{endforeach;}*/    <//*{$tag*/g/*}*/>
/*{endif}*/
</template>
<script>

/*{foreach($imports($sequences, $mixins) as $e):}*/    import /*{$e['name']*/BpmnPlane/*}*/ from /*{$e['path']*/BpmnPlane/*}*/;
/*{endforeach;}*/

    export default {
/*{if ($sequences):}*/
        components: {
/*{foreach($sequences as $e):}*/            /*{$importComponent($e)*/BpmnPlane/*}*/,
/*{endforeach;}*/
        },
/*{endif}*/
        mixins: [/*{@$mixins ? implode(', ', $mixins) : ''*/Element/*}*/],
        props: {
            value: null,
        },
        computed: {
/*{foreach($sequences as $e):}*/            /*{@$e['multiple'] ? str_plural(camel_case($e['ref'])) : camel_case($e['ref']) */BpmnPlane/*}*/() {
                return this./*{$sequenceGetter($e)*/getElementsByTagName("BpmnPlane")/*}*/;
            },
/*{endforeach;}*/
/*{if(@$attributes) foreach($attributes as $e):}*/            /*{$e['name']*/bpmnElement/*}*/() {
                return this./*{$e['type']*/getElementById/*}*/(this.value.$[/*{json_encode($e['name'])*/"bpmnElement"/*}*/]);
            },
/*{endforeach;}*/
        }
    }
</script>
/*{@$style ? "\n" . $style . "\n" : ''*//*}*/