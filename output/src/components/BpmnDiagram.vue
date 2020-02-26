<template>
    <div>
        <bpmn-plane v-model="bpmnPlane" />
    </div>
</template>
<script>

    import Element from ".\/Element";
    import BpmnPlane from ".\/BpmnPlane";

    export default {
        components: {
            BpmnPlane,

        },
        mixins: [Element],
        props: {
            value: null,
        },
        computed: {
            bpmnPlane() {
                return this.getElementByTagName("BpmnPlane");
            },


        }
    }
</script>
