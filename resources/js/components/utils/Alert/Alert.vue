<template>
    <div class="alert" :class="[alertClass]" role="alert" v-if="showAlert">
        {{ alertText }}
    </div>
</template>

<script>
export default {
    name: "Alert",
    props: ["showAlert", "alertClass", "alertText"]
};
</script>

<style lang="scss" scoped>
.alert {
    padding: 10px;
    border-radius: 5px;
}
</style>
