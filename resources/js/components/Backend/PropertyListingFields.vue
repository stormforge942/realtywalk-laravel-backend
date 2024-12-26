<template>
  <div class="col row m-0 p-0">
    <div class="form-group col-sm-12 col-md-4">
      <label>Listing / Posting Type</label>
      <select name="type" v-model="type" class="form-control">
        <option value="0">Listing</option>
        <option value="1">Posting</option>
      </select>
      <div class="alert alert-warning w-100 mt-2">
        Listings are offficial listings must be from registered MLS services.
        Postings are manually entered properties.
      </div>
    </div>
    <div class="form-group col-md-9" v-if="type == 0">
      <label for="agent">Agent*:</label>
      <input
        maxlength="191"
        v-model="agent"
        name="agent"
        type="text"
        id="agent"
        class="form-control"
      />
    </div>
  </div>
</template>

<script>
export default {
  data: function () {
    return {type: '1', agent: ''};
  },
  props: ['defaulttype', 'defaultagent'],
  mounted() {
    this.type = this.defaulttype;
    this.agent = this.defaultagent;
    if (this.type == '') {
      this.type = '1';
    }
  },
};
</script>
