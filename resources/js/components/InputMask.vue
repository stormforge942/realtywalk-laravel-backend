<template>
  <div>
    <input
      v-input-mask
      class="form-control"
      v-model="modelValue"
      :id="name"
      :data-inputmask="mask"
      :type="type"
    />

    <input type="hidden" :name="name" :value="modelValue" />
  </div>
</template>

<script>
export default {
  name: "InputMask",
  data() {
    return {
      modelValue: null
    };
  },
  props: {
    mask: { type: String, required: true },
    name: { type: String, required: true },
    type: { type: String, default: "text" },
    value: { type: [String, Number], default: null }
  },
  created() {
    this.modelValue = this.$props.value;
  }
};
</script>
