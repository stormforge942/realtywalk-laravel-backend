<template>
  <div>
    <label :for="name" :class="required == 'true'? 'required' : ''">{{label}}:</label>

    <div class="combo-row">
      <div class="input-group">
        <span v-if="money == 'true'" class="input-group-text border-right-0">$</span>
        <input
          type="text"
          :name="name"
          v-model="decoratedValue"
          class="form-control"
          aria-label="Amount (to the nearest dollar)"
        />
        <span v-if="money == 'true'" class="input-group-text border-left-0">.00</span>
      </div>
      
      <div class="input-slider align-self-center">
        <input
          type="range"
          class="form-range w-100 mt-1"
          v-model="sliderValue"
          min="1"
          max="1000"
          :id="name"
        />
      </div>
    </div>
  </div>
</template>

<script>

export default {
  data: function () {
    return {
      value: 100000,
      sliderValue: 1,
    };
  },
  computed: {
    decoratedValue: {
      get: function() {
        return this.addCommas(this.value);
      },
      set: function(newValue) {
        const number = parseFloat(newValue.replace(/,/g, ""));
        this.value = number;
      }
    }
  },
  props: ["default", "label", "name", 'money', 'step', 'required'],
  watch: {
    sliderValue() {
      this.value = this.sliderValue * this.step;
      this.$emit('update', [{name: this.name, value: this.value}]);
    },
    value() {
      this.sliderValue = this.value / this.step;
    },
  },
  mounted() {
    if (this.default) {
      this.value = parseFloat(this.default);
    }
  },
  methods: {
    addCommas: function(value) {
      return value ? value.toString()
        .split(".")[0]
        .replace(/\D/g, "")
        .replace(/\B(?=(\d{3})+(?!\d))/g, ",") : "";
    }
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
  min-width: 25%;
  max-width: 250px;
  padding-right: 10px;
}
.input-slider {
  flex: 4;
}
</style>