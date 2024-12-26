<template>
  <div>
    <label :for="`${name}_range`" :class="required == 'true'? 'required' : ''">{{label}}:</label>

    <div class="row combo-row">
      <div class="input-group">
        <span v-if="money == 'true'" class="input-group-text border-right-0">$</span>
        <input
          type="text"
          :name="`${name}_from`"
          v-model="decoratedValueFrom"
          class="form-control"
          aria-label="Amount (to the nearest dollar)"
        />
        <span v-if="money == 'true'" class="input-group-text border-left-0">.00</span>
      </div>
      
      <div class="input-slider align-self-center">
        <vue-range-slider
          class="form-range w-100 mt-1"
          v-model="sliderRange"
          :enable-cross="false"
          :min="1"
          :max="1000"
          :tooltip="false"
          :id="`${name}_range`"
        />
      </div>
    </div>
    <div class="combo-row">
      <div class="input-group">
        <span v-if="money == 'true'" class="input-group-text border-right-0">$</span>
        <input
          type="text"
          :name="`${name}_to`"
          v-model="decoratedValueTo"
          class="form-control"
          aria-label="Amount (to the nearest dollar)"
        />
        <span v-if="money == 'true'" class="input-group-text border-left-0">.00</span>
      </div>
    </div>
  </div>
</template>

<script>
import VueRangeSlider from 'vue-range-component-fixed';
import 'vue-range-component-fixed/dist/vue-range-slider.css';

export default {
  data: function () {
    return {
      valueFrom: 25000,
      valueTo: 100000,
      sliderRange: [1, 4]
    };
  },
  computed: {
    decoratedValueFrom: {
      get: function() {
        return this.addCommas(this.valueFrom);
      },
      set: function(newValue) {
        const number = parseFloat(newValue.replace(/,/g, ""));
        if (number < this.valueTo) {
          this.valueFrom = number;
        }
      }
    },
    decoratedValueTo: {
      get: function() {
        return this.addCommas(this.valueTo);
      },
      set: function(newValue) {
        const number = parseFloat(newValue.replace(/,/g, ""));
        if (number > this.valueFrom) {
          this.valueTo = number;
        }
      }
    }
  },
  props: ["defaultFrom", "defaultTo", "label", "name", 'money', 'step', 'required'],
  watch: {
    sliderRange() {
      this.valueFrom = this.sliderRange[0] * this.step;
      this.valueTo = this.sliderRange[1] * this.step;
      this.$emit('update', [
        {name: `${this.name}_from`, value: this.valueFrom},
        {name: `${this.name}_to`, value: this.valueTo}
      ]);
    },
    valueFrom() {
      this.sliderRange = [this.valueFrom / this.step, this.valueTo / this.step];
    },
    valueTo() {
      this.sliderRange = [this.valueFrom / this.step, this.valueTo / this.step];
    }
  },
  methods: {
    addCommas: function(value) {
      return value ? value.toString()
        .split(".")[0]
        .replace(/\D/g, "")
        .replace(/\B(?=(\d{3})+(?!\d))/g, ",") : "";
    }
  },
  mounted() {
    if (this.defaultFrom) {
      if (this.defaultTo) {
        this.sliderRange = [parseFloat(this.defaultFrom) / this.step, parseFloat(this.defaultTo) / this.step];
      } else {
        this.sliderRange = [parseFloat(this.defaultFrom) / this.step, (parseFloat(this.defaultFrom) / this.step) + 3];
      }
    }
  },
  components: {
    "vue-range-slider": VueRangeSlider
  }
};
</script>

<style scoped>
.border-left-0 {
    border-top-left-radius: 0;
    border-bottom-left-radius: 0;
}
.border-right-0 {
    border-top-right-radius: 0;
    border-bottom-right-radius: 0;
}
.combo-row {
  display: flex;
  flex-wrap: wrap;
}
.input-group {
  flex: 1 1 25%;
  max-width: 33%;
  min-width: 25%;
  padding-right: 10px;
}
.input-slider {
  flex: 4;
}
</style>