<template>
    <ICountUp :delay="delay" :endVal="endVal" :options="options" />
</template>

<script>
import ICountUp from "vue-countup-v2";

export default {
    name: "CountupText",
    components: { ICountUp },
    data() {
        return {
            options: {
                useEasing: true,
                useGrouping: true,
                separator: ",",
                decimal: ".",
                prefix: "",
                suffix: ""
            }
        };
    },
    props: {
        delay: { type: Number, default: 300 },
        endVal: { type: Number, required: true }
    },
    methods: {
        onReady: (instance, CountUp) => instance.update(this.endVal + 100)
    }
};
</script>
