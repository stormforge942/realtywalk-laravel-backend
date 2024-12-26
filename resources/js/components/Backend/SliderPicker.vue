<template>
  <div class=" mb-3">
    <div class="form-group col-sm-12 col-md-4">
      <label>{{label}} Format:</label>
      <select name="price_format_id" v-model="formatId" class="form-control">
        <option v-for="format in formats" :key="format.id" :value="format.id">{{format.name}}</option>
      </select>
    </div>

    <div class="form-group col-10" v-if="formatId == 1">
      <slider-combo :label="label" :name="`${name}_from`" :money="money" :step="step" :default="defaultFrom"></slider-combo>
    </div>
    
    <div class="form-group col-10" v-if="formatId == 2">
      <range-slider-combo :label="label" :name="name" :money="money" :step="step" :default-from="defaultFrom" :default-to="defaultTo"></range-slider-combo>
    </div>
  </div>
</template>

<script>

import RangeSliderCombo from "./RangeSliderCombo";
import SliderCombo from "./SliderCombo";

export default {
  data: function () {
    return {
      formatId: 1
    };
  },
  props: ["defaultFrom", "defaultTo", "label", "name", 'money', 'step', 'formats', 'defaultFormat'],
  mounted() {
    if (this.defaultFormat) {
      this.formatId = this.defaultFormat;
    }
  },
  components: {
    "slider-combo": SliderCombo,
    "range-slider-combo": RangeSliderCombo
  }
};
</script>