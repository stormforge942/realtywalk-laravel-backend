<template>
  <div id="pagebody">
    <div id="content" class="container">
      <div id="left" class="d-none d-sm-none d-md-none d-lg-block d-xl-block">
        <div id="logo">
          <a href="/">
            <img
              class="img-fluid"
              src="/images/logo-rw-horizontal.png"
              alt="NHW Logo"
            />
          </a>
        </div>
        <div class="footprints">
          <img id="footprints-img" src="/images/footprints-vert.png" alt="" />
        </div>
      </div>
      <div id="right">
        <div class="loading-state" v-if="loading">
          <div class="lds-ripple">
            <div></div>
            <div></div>
          </div>
        </div>
        <div class="card" v-else>
          <div class="card-header">
            <h2 class="card-title">{{ builder.name }}</h2>
          </div>

          <div
            class="builder"
            v-if="
              `${builder.descr}` !== null ||
              `${builder.phone}` !== null ||
              `${builder.email}` !== null ||
              `${builder.website}` !== null ||
              `${builder.address1}` !== null ||
              `${builder.address2}` !== null ||
              `${builder.address3}` !== null ||
              `${builder.city}` !== null
            "
          >
            <div class="card-body">
              <div v-if="`${builder.descr}` !== null">
                <p class="card-text">{{ builder.descr }}</p>
                <hr />
              </div>
              <p class="card-text">
                {{ builder.address1 }} {{ builder.address2 }}
                {{ builder.address3 }} {{ builder.city }}
              </p>
              <a
                :href="`${builder.website}`"
                class="card-link"
                >{{ builder.website }}</a
              >
              <p class="card-text">{{ builder.phone }}</p>
              <a :href="`mailto:${builder.email}`" class="card-link">{{
                builder.email
              }}</a>
            </div>
          </div>
        </div>

        <div id="builderprops" v-if="!loading">
          <div id="div-listing">
            <h1>Featured Homes</h1>

            <div v-for="homeitem of builder.properties">
              <table class="search-result-item">
                <tbody>
                  <tr>
                    <td class="image-cell" rowspan="2">
                      <a href="#"
                        ><img class="item-title-image" src="" alt=""
                      /></a>
                    </td>
                    <td class="item-info-bar">
                      <table>
                        <tbody>
                          <tr>
                            <td>Price: ${{ homeitem.price }}</td>
                            <td>
                              Addr:
                              {{ homeitem.address_number }}
                              {{ homeitem.address_street }}
                            </td>
                            <td>
                              Bldr:
                              {{ builder.name }}
                            </td>
                          </tr>
                        </tbody>
                      </table>
                    </td>
                  </tr>

                  <tr>
                    <td>{{ homeitem.descr }}</td>
                  </tr>
                  <tr>
                    <td class="builder-div"></td>
                    <td>
                      <router-link :to="`/builders/property/${homeitem.id}`"
                        >View Listing</router-link
                      >
                      &nbsp; &nbsp; &nbsp;
                      <a class="property-fav-button" href="#"
                        >Add to favorites</a
                      >
                      &nbsp; &nbsp; &nbsp;

                      <a href="#">Schedule a Viewing</a>
                    </td>
                  </tr>
                </tbody>
              </table>
            </div>
            <!-- end loop -->
          </div>
        </div>
      </div>
      <!-- end right -->
    </div>
    <!-- end content -->
  </div>
  <!-- end pagebody -->
</template>

<script>
export default {
  name: "SingleBuilder",
  //props:['builder','properties'],
  watch: {
    // call again the method if the route changes
    $route: "loadBuilder",
  },
  data() {
    return {
      builder: {},
      properties: {},
      loading: true,
    };
  },
  methods: {
    loadBuilder() {
      axios.get("/api/builder/" + this.$route.params.slug).then((data) => {
        this.builder = data.data;
        this.properties = data.data.properties;
        this.loading = false;
      });
    },
  },

  mounted() {
    this.loadBuilder();
  },
};
</script>
<style scoped>
.loading-state {
  padding: 50px 0;
}
@media screen and (max-width: 991px) {
  #content {
    margin-top: 0;
  }
}
</style>
