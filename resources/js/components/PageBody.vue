<template>
    <div id="pagebody">
        <div id="content" class="container">
            <LeftSidebar></LeftSidebar>
            <div id="right">
                <div id="pagehead"></div>

                <div class="container-fluid">
                    <h1>Properties by Builder</h1>
                    <h4 v-if="isLoggedIn">{{ $t('general.welcome', {user: user.name})  }}</h4>
                    <div class="row">
                        <div class="col-xl-9 col-lg-12 col-md-12 col-sm-12">
                            <a
                                href="#"
                                class="keywords"
                                @click.prevent="loadBuilders"
                                >All</a
                            >
                            <a
                                href="#"
                                class="keywords"
                                v-for="(keyword, index) in keywords"
                                :key="index"
                                @click.prevent="searchBuilder(keyword)"
                                >{{ keyword.toUpperCase() }}</a
                            >
                        </div>
                        <div class="col-xl-3 col-lg-12 col-md-12 col-sm-12">
                            <input
                                class="form-control"
                                type="text"
                                v-model="searchWord"
                                placeholder="Search Builder"
                                aria-label="Search"
                            />
                        </div>
                    </div>

                    <pagination
                        :data="builders"
                        :limit="Number(2)"
                        @pagination-change-page="getResults"
                    ></pagination>

                    <div class="builder-list">
                        <div class="loading-state" v-if="loading">
                            <div class="lds-ripple">
                                <div></div>
                                <div></div>
                            </div>
                        </div>

                        <div
                            v-for="builder in builders.data"
                            :key="builder.id"
                            v-else
                        >
                            <h3>
                                <router-link :to="`/builder/${builder.slug}`">{{
                                    builder.name
                                }}</router-link>
                            </h3>
                            <p
                                v-for="property in builder.properties"
                                :key="property.id"
                            >
                                <router-link
                                    :to="property.alt_path_url"
                                >{{ property.title }}</router-link>
                            </p>
                        </div>
                    </div>

                    <pagination
                        :data="builders"
                        :limit="Number(2)"
                        @pagination-change-page="getResults"
                    ></pagination>
                    <div class="p-3"></div>
                </div>
                <!-- end container-fluid -->
            </div>
            <!-- end right -->
        </div>
        <!-- end content -->
    </div>
    <!-- end pagebody -->
</template>

<script>
import LeftSidebar from "./layout/LeftSidebar";

export default {
    name: "PageBody",
    data() {
        return {
            builders: {},
            searchWord: null,
            tempData: "",
            loading: false,
            searchError: false,
            keywords: [
                "a",
                "b",
                "c",
                "d",
                "e",
                "f",
                "g",
                "h",
                "i",
                "j",
                "k",
                "l",
                "m",
                "n",
                "o",
                "p",
                "q",
                "r",
                "s",
                "t",
                "u",
                "v",
                "w",
                "x",
                "y",
                "z"
            ]
        };
    },
    components: {
        LeftSidebar
    },
    computed: {
        isLoggedIn() {
            return this.$store.state.auth.status.loggedIn;
        },
        user() {
            return this.$store.state.auth.user;
        }
    },
    watch: {
        searchWord(after, before) {
            if (after != "") this.searchBuilderText();
            else this.loadBuilders();
        }
    },
    methods: {
        getResults(page = 1) {
            this.searchError = false;
            this.loading = true;
            let url = new URL(this.builders.path);
            let path = url.pathname;
            axios.get(path + "?page=" + page).then(response => {
                this.builders = response.data;
                this.loading = false;
            });
        },
        loadBuilders() {
            this.loading = true;
            axios.get("/api/builders/list").then(data => {
                this.builders = data.data;
                this.loading = false;
            });
        },
        searchBuilder(keyword) {
            this.searchError = false;
            this.searchWord = "";
            this.loading = true;
            axios.get("/api/builders/search/" + keyword).then(data => {
                this.builders = data.data;
                this.loading = false;
            });
        },
        searchBuilderText() {
            this.searchError = false;
            this.loading = true;
            axios
                .get("/api/builders/searchText/" + this.searchWord)
                .then(data => {
                    this.builders = data.data;
                    this.loading = false;
                });

            if (this.builders.data.length == 0) {
                this.searchError = true;
            }
        }
    },
    mounted() {
        this.loadBuilders();
    }
};
</script>

<style scoped>
a.keywords {
    padding: 10px 8px;
    color: grey;
    text-decoration: underline;
    display: inline-block;
}
.loading-state {
    padding: 50px 0;
}
.builder-list {
    padding: 2rem 0;
}
#right {
    border-left: none !important;
}
@media screen and (max-width: 991px) {
    #content {
        margin-top: 0;
    }
    #content #right div#pagehead {
        padding: 0;
        background-color: transparent;
    }
}
</style>
