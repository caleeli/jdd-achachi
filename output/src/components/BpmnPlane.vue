<template>
    <div>
        <bpmn-edge v-for="bpmnEdge in bpmnEdges" v-model="bpmnEdge" />
        <bpmn-shape v-for="bpmnShape in bpmnShapes" v-model="bpmnShape" />
    </div>
</template>
<script>

    import Element from ".\/Element";
    import BpmnEdge from ".\/BpmnEdge";
    import BpmnShape from ".\/BpmnShape";

    export default {
        components: {
            BpmnEdge,
            BpmnShape,

        },
        mixins: [Element],
        props: {
            value: null,
        },
        computed: {
            bpmnEdges() {
                return this.getElementsByTagName("BpmnEdge");
            },
            bpmnShapes() {
                return this.getElementsByTagName("BpmnShape");
            },

            bpmnElement() {
                return this.getElementById(this.value.$["bpmnElement"]);
            },

        }
    }
</script>
