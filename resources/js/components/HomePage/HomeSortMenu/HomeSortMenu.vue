<template>
  <ul :class="`list-${type}`">
    <li class="d-lg-none">
      {{ $t('home.filter.columns.sort_by') }}
    </li>
    <li>
      <a
        href="javascript:;"
        @click="orderProperties('price_from')"
        :class="toggles.price_from !== false ? 'active' : ''"
      >
        <i class="fa" :class="toggles.price_from ? 'asc' : 'desc'">&#xf0d8;</i>

        <span>{{ $t('home.filter.columns.price') }}</span>
      </a>
    </li>
    <li>
      <a
        href="javascript:;"
        @click="orderProperties('address')"
        :class="toggles.address !== false ? 'active' : ''"
      >
        <i class="fa" :class="toggles.address ? 'asc' : 'desc'">&#xf0d8;</i>
        <span>{{ $t('home.filter.columns.address') }}</span>
      </a>
    </li>
    <li>
      <a
        href="javascript:;"
        @click="orderProperties('neighborhood')"
        :class="toggles.neighborhood !== false ? 'active' : ''"
      >
        <i class="fa" :class="toggles.neighborhood ? 'asc' : 'desc'">&#xf0d8;</i>
        <span class="d-none d-lg-inline">{{ $t('home.filter.columns.sub_neighborhood') }}</span>
        <span class="d-lg-none">{{ $t('home.filter.columns.neighborhood') }}</span>
      </a>
    </li>
  </ul>
</template>

<script>
import {EventBus} from "../../helpers";

export default {
  name: "HomeSortMenu",
  props: ["type", "toggles", "setSort"],
  data() {
    return {}
  },
  methods: {
    orderProperties(column) {
      const direction = this.setSort(column);
      EventBus.$emit("orderProperties", column, direction)
    }
  }
}
</script>

<style lang="scss" scoped>
ul {
  margin: 0 0 5px 0;
  padding: 0;
  display: flex;
  justify-content: flex-end;
}
ul li {
  list-style-type: none;
  margin-left: 80px;
}
.list-lg {
  width: calc(100% - 95px);
  margin-left: auto;
  padding: 0 8px;
  justify-content: flex-start;

  @media (min-width: 1025px) {
    width: calc(100% - 235px);
  }

  li {
    margin-left: 0;

    &:nth-child(2) {
      width: 16.6667%;
      padding-right: 15px;
    }

    &:nth-child(3) {
      width: 33.333333%;
      padding-right: 15px;
    }
    &:nth-child(4) {
      width: 25%;
      padding-right: 15px;
    }
  }
}
ul li a {
  display: block;
  color: #454E67;
  font-size: 15px;
  transition: 250ms;
}

ul li a span {
  position: relative;
  margin-left: 0.25rem;
}

ul li a span::after {
  content: "";
  position: absolute;
  bottom: 0;
  left: 0;
  width: 100%;
  height: 1px;
  background: #454E67;
  transition: 250ms;
}

ul li a:hover,
ul li a.active {
  color: #ffc501;
  text-decoration: none;
}

ul li a:hover span::after,
ul li a.active span::after {
  background: #ffc501;
}

ul li a i {
  transform-origin: center;
}

ul li a i.desc {
  transform: rotateX(180deg);
  width: auto!important;
}

@media screen and (max-width: 1439px) {
  ul li {
    margin-left: 60px;
  }
}

@media screen and (max-width: 1279px) {
  ul li {
    margin-left: 40px;
  }
}

@media screen and (max-width: 991px) {
  ul {
    background-color: #012e55;
    padding: 10px 5px;
    justify-content: space-between;
    margin-bottom: 0;
  }

  ul li {
    border-bottom: none;
    margin: 0;
    flex-grow: 1;
  }

  ul li:not(:first-child) {
    border-left: 1px solid #ffc501;
  }

  ul li,
  ul li a {
    color: #FFF;
    text-transform: none;
    text-align: center;
  }

  ul li a i {
    display: none;
  }
}
</style>
