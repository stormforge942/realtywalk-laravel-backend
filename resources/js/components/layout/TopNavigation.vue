<template>
  <div class="top-navigation" v-if="shown">
    <a href="javascript:;" @click="back">
      <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor" class="icon">
        <path fill-rule="evenodd"
          d="M13.28 3.97a.75.75 0 010 1.06L6.31 12l6.97 6.97a.75.75 0 11-1.06 1.06l-7.5-7.5a.75.75 0 010-1.06l7.5-7.5a.75.75 0 011.06 0zm6 0a.75.75 0 010 1.06L12.31 12l6.97 6.97a.75.75 0 11-1.06 1.06l-7.5-7.5a.75.75 0 010-1.06l7.5-7.5a.75.75 0 011.06 0z"
          clip-rule="evenodd" />
      </svg>

      <span>Back</span>
    </a>
  </div>
</template>

<script>
export default {
  name: 'TopNavigation',
  data () {
    return {
      shown: true,
    }
  },
  methods: {
    back () {
      window.history.back();
    }
  },
  mounted() {
    const referrer = document.referrer.split('/')[2];
    if (referrer !== undefined && referrer !== window.location.hostname) {
      this.shown = false;
    } else if (window.history.length <= 1) {
      this.shown = false;
    }
  },
}
</script>

<style lang="scss">
.top-navigation {
  background: #eeeeee;
  padding: 12px 16.7% 12px 16.6%;

  a {
    color: #333333;
    display: inline-flex;
    align-items: center;

    .icon {
      position: relative;
      top: -1px;
      width: 14px;
      height: 14px;
      margin-right: 3px;
    }

    &:hover {
      color: #000000;
      transition: 250ms;
      text-decoration: none;
    }
  }

  @media screen and (max-width: 991px) {
    padding: 8px 0;
  }
}
</style>
