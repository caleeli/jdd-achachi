<template>
    <div>
        <bpmn-lane v-model="bpmnLane" />
    </div>
</template>
<script>

    import BpmnLane from ".\/BpmnLane";

    export default {
        components: {
            BpmnLane,

        },
        mixins: [],
        props: {
            value: null,
        },
        computed: {

            bpmnLane() {

            },

        }
    }
</script>
