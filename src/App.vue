<template>
    <div id="app">
        <img alt="Achachi" src="./assets/main.svg">
        <h1>Hello world</h1>
    </div>
</template>

<script>
    import Vue from 'vue';
    import VueRouter from 'vue-router';
    import {components} from 'jdd-vue-components';

    const router = new VueRouter({});

    // Global objects
    window.taskMixin = {};
    window.router = router;

    window.ApiArray = function() {
        return [];
    };

    // Load jdd components
    Object.keys(components).forEach(name => {
        Vue.component(name, components[name]);
    });

    Vue.use(VueRouter);

    // Load index.js
    require('./index.js');

    export default {
        router,
        name: 'app',
    }
</script>

<style>
</style>
