<template>
    <div style="text-align: center;margin-top: 50px;">
        <h2 v-html="appName"></h2>
        Developed By: Narayan Adhikary (<a target="_blank" href="https://wovosoft.com">Wovo Soft</a> )
    </div>
</template>

<script>
export default {
    name: "Home",
    computed: {
        appName() {
            return APP_NAME;
        }
    }
}
</script>

<style scoped>

</style>
