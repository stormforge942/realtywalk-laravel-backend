<template>
  <PrimaryLayout>
    <template v-slot:contentlayout>
      <TopNavigation />
      <div class="page-content">
      <div class="loading-state" v-if="loading">
        <div class="lds-ripple">
        <div></div>
        <div></div>
        </div>
      </div>
      <div v-else-if="neighborhood" class="section-one">
        <!-- v-else -->
        <div class="neighborhood-header">
          <poly-map :initial-geo-json="neighborhood.geometry" :polygon-id="neighborhood.id" :initial-zoom="neighborhood.zoom" :show-neighbors="true" :clickable-neighbors="true" disableLabel></poly-map>
        </div>
      </div>

      <div v-if="!loading && neighborhood" id="section-two">
        <div class="row neighborhood-info-header">
          <h3 class="col-lg-9 col-md-8 m-0">{{ neighborhood ? neighborhood.title : '' }}</h3>

          <div class="col col-lg-3 col-md-4">
            <a style="cursor:pointer" @click="showModal" class="view-neighs">
              View Properties in this Neighborhood
            </a>
          </div>
        </div>

        <div class="row">
          <div class="poly-hierarchy col-12" v-if="neighborhood.ancestors && neighborhood.ancestors.length">
          <span v-for="ancestorPoly in neighborhood.ancestors" :key="ancestorPoly.id">{{ancestorPoly.title}} > </span>
          <span>{{neighborhood.title}}</span>
          </div>
        </div>
        <div class="row">
          <div class="col col-md-7">
            <div>
              <!--{{ property.video_embed }}-->

              <div v-if="neighborhood.zoom == 1">
                <div class="h4 mt-3">
                  Neighborhoods
                </div>

                <LiquorTree
                  v-if="neighborhood.descendants"
                  ref="tree"
                  :data="neighborhood.descendants"
                  :options="treeOptions"

                  @node:clicked="onNeighborhoodClicked"
                />
              </div>
              <div v-else>

                <div class="neighborhood-desc card-text" v-html="neighborhood.description"></div>
                <div v-if="schools && schools.length > 0">
                  <div class="h3 mt-3">
                    Schools
                  </div>
                  <div class="table-responsive">
                    <table class="table">
                      <thead>
                        <tr>
                          <th>School Name</th>
                          <th>Address</th>
                          <th>Website</th>
                          <th>School District</th>
                          <th>Instructional Level</th>
                          <th>Grade Low</th>
                          <th>Grade High</th>
                          <th>Student Teacher Ratio</th>
                          <th>Enrollment</th>
                          <th>Virtual</th>
                          <th>Magnet</th>
                          <th>Charter</th>
                          <th>Catholic Type</th>
                          <th>Private Orientation</th>
                          <th>Bilingual School</th>
                          <th>American Indian Enrollment</th>
                          <th>Asian Enrollment</th>
                          <th>Hispanic Enrollment</th>
                          <th>Black Enrollment</th>
                          <th>White Enrollment</th>
                          <th>Pacific Enrollment</th>
                          <th>Mulit-race Enrollment</th>
                          <th>Male Enrollment</th>
                          <th>Female Enrollment</th>
                          <th>Advance Placement</th>
                          <th>Before/After School Program</th>
                          <th>Gifted & Talented Program</th>
                          <th>Bilingual Education</th>
                          <th>Enrollment Change</th>
                          <th>Rating</th>
                        </tr>
                      </thead>
                      <tbody>

                        <tr v-for="school in schools" :key="`district-${school.id}`">
                          <td>{{school.name}}</td>
                          <td>{{school.address}}</td>
                          <td><a :href="school.school_url" target="_blank" rel="noopener noreferrer">View Website</a></td>
                          <td>{{school.district_name}}</td>
                          <td>{{school.instructional_level}}</td>
                          <td>{{school.grade_low}}</td>
                          <td>{{school.grade_high}}</td>
                          <td>{{school.student_teacher_ratio}}</td>
                          <td>{{school.enrollment}}</td>
                          <td>{{formatIndicator(school.virtual_school_ind)}}</td>
                          <td>{{formatIndicator(school.magnet_ind)}}</td>
                          <td>{{formatIndicator(school.charter_ind)}}</td>
                          <td>{{school.catholic_school_type}}</td>
                          <td>{{school.private_school_orientation}}</td>
                          <td>{{formatIndicator(school.bilingual_schools_assoc_ind)}}</td>
                          <td>{{school.enroll_am}}</td>
                          <td>{{school.enroll_asian}}</td>
                          <td>{{school.enroll_hisp}}</td>
                          <td>{{school.enroll_black}}</td>
                          <td>{{school.enroll_white}}</td>
                          <td>{{school.enroll_pacific}}</td>
                          <td>{{school.enroll_multiple_races}}</td>
                          <td>{{school.enroll_male}}</td>
                          <td>{{school.enroll_female}}</td>
                          <td>{{formatIndicator(school.ap_ind)}}</td>
                          <td>{{formatIndicator(school.before_and_after_school_prog_ind)}}</td>
                          <td>{{formatIndicator(school.gifted_and_talented_prog_ind)}}</td>
                          <td>{{formatIndicator(school.bilingual_education_ind)}}</td>
                          <td>{{school.enrollment_change_cnt}}</td>
                          <td>{{school.school_rating}}</td>
                        </tr>

                      </tbody>
                    </table>
                  </div>
                </div>

                <div v-if="schoolDistricts && schoolDistricts.length > 0">
                  <div class="h3 mt-3">
                    School Districts
                  </div>
                  <div class="table-responsive">
                    <table class="table">
                      <thead>
                        <tr>
                          <th>District Name</th>
                          <th>Website</th>
                          <th>Grade Low</th>
                          <th>Grade High</th>
                          <th>School Count</th>
                          <th>Enrollment</th>
                          <th>Urban Local</th>
                          <th>Urban Community</th>
                          <th>Per Pupil Expendature</th>
                          <th>Charter Schools</th>
                          <th>Magnet Schools</th>
                          <th>Child Poverty</th>
                          <th>Rating</th>
                        </tr>
                      </thead>
                      <tbody>

                        <tr v-for="school in schoolDistricts" :key="`school-${school.ext_id}`">
                          <td>{{school.district_name}}</td>
                          <td><a :href="school.school_url" target="_blank" rel="noopener noreferrer">View Website</a></td>
                          <td>{{school.grade_low}}</td>
                          <td>{{school.grade_high}}</td>
                          <td>{{school.school_cnt}}</td>
                          <td>{{school.enrollment}}</td>
                          <td>{{school.urban_centric_locale_type}}</td>
                          <td>{{school.urban_centric_community_type}}</td>
                          <td>{{school.total_per_pupil_expenditure_amt}}</td>
                          <td>{{school.charter_school_cnt}}</td>
                          <td>{{school.magnet_school_cnt}}</td>
                          <td>{{school.pop_age_5_17_below_poverty_level_pct}}</td>
                          <td>{{school.school_district_rating}}</td>
                        </tr>

                      </tbody>
                    </table>
                  </div>
                </div>


                <div class="h4 mt-2" v-if="stats.some(stat => stat.statistics.some(v => v.value))">
                  Statistics
                </div>
                <div v-for="stat in stats" :key="stat.id">
                  <div v-for="value in stat.statistics.filter(v => v.value)" :key="value.id">
                    <tr class="ml-5">
                      <td> {{value.name}}:</td>
                      <td v-if="value.value == '0.00'" class="ml-4">
                        <span class="p-2">N/A</span>
                      </td>
                      <td v-else class="ml-4">
                        <span class="p-2">{{value.value}}</span>
                      </td>
                    </tr>
                  </div>
                </div>
              </div>
            </div>
          </div>
          <div class="col col-md-5">
            <div class="links-box">
              <div class="card">
                <div class="card-body">
                  <div>
                    <h2 class="card-title">
                      Local Links & Attractions
                    </h2>
                    <hr />
                  </div>
                  <div class="inner-links">
                    <!--row -->
                    <div v-if="neighborhood && neighborLinks.length">
                      <div class="card w-100 relative text-muted border rounded-lg mb-2 overflow-hidden" v-for="(link, index) in neighborLinks" :key="index">
                        <img v-if="link.image" class="card-img-top rounded-top object-cover max-h-10" :src="link.image" :alt="link.label || link.title">
                        <div class="card-body bg-white">
                          <h5 class="card-title" :class="{'mb-0': !link.description}">
                            <a :href="'//' + link.url.replace(/(^\w+:|^)\/\//, '')" class="stretched-link text-brand" target="_blank">
                                {{ link.label || link.title || link.url }}
                            </a>
                          </h5>
                          <p v-if="link.description" class="card-text p-0">{{ trimText(link.description, 100) }}</p>
                          <small class="card-text p-0">{{ getDomain(link.url) }}</small>
                        </div>
                      </div>
                    </div>
                    <div v-else>
                        <p class="text-light text-center px-2 px-lg-5 m-0">{{ $t('neighborhood.list.empty_link') }}</p>
                    </div>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>
        </div>
      </div>

      <Modal class="modal-custom" ref="modal" :showCloseBtn="false" @closeModal="closeModal">
        <template v-slot:body>
          <div class="modal-body">
            <NeighborhoodList :fetch="fetchNeighborhood" :closeModal="closeModal" :neighborhood="neighborhood ? neighborhood : null" />
          </div>
        </template>
      </Modal>
    </template>
  </PrimaryLayout>
</template>

<script>
  import Vue from "vue";
  import { EventBus } from "../../helpers";
  import PrimaryLayout from "../../layout/PrimaryLayout/PrimaryLayout";
  import TopNavMember from "../../layout/TopNavMember";
  import TopNavigation from "../../layout/TopNavigation";
  import $ from "jquery";
  import Modal from "../../utils/Modal/Modal";
  import NeighborhoodList from './NeighborhoodList/NeighborhoodList';
  import "leaflet/dist/leaflet.css";
  import LiquorTree from 'liquor-tree';

  export default {
    name: "SingleNeighborhood",
    components: {
      LiquorTree,
      PrimaryLayout,
      TopNavMember,
      TopNavigation,
      NeighborhoodList,
      Modal
    },
    data() {
      return {
        property: {},
        builder: {},
        schools: [],
        schoolDistricts: [],
        points: {},
        stats: [],
        properties: undefined,
        loading: true,
        images: [],
        index: null,
        isFavorited: false,
        neighborhood: null,
        propertiesLength: "",
        treeOptions: {
          checkbox: false,
          checkOnSelect: false,
          keyboardNavigation: false
        },
        fetchNeighborhood: false
      };
    },
    computed: {
      neighborLinks() {
        if (!this.neighborhood || !this.neighborhood.all_links) {
          return [];
        }

        const uniqueLinks = new Map();

        this.neighborhood.all_links.forEach(link => {
          if ((!link.status || link.status === 200) && link.url) {
            if (!uniqueLinks.has(link.url)) {
              uniqueLinks.set(link.url, link);
            }
          }
        });

        return Array.from(uniqueLinks.values());
      },
      neighborhoodHasImages() {
        return this.images.length > 0
      },
      isLoggedIn() {
        return this.$store.state.auth.status.loggedIn;
      },
      breadcrumbs () {
        if (! this.neighborhood) return ''

        let polygons = this.neighborhood.ancestors.map(item => {
          return {
            id: item.id,
            url: item.path_url,
            title: item.title
          };
        })

        let links = [...polygons, {
          id: this.neighborhood.id,
          url: this.neighborhood.path_url,
          title: this.neighborhood.title
        }];

        return links
      },
    },
    methods: {
      getDomain(url) {
        try {
          const parsedUrl = new URL(url);
          return parsedUrl.hostname;
        } catch (e) {
          return url;
        }
      },
      trimText(text, length) {
        return text.length > length ? text.substring(0, length) + "..." : text;
      },
      showModal() {
        let element = this.$refs.modal.$el;
        $(element).modal("show");
        this.fetchNeighborhood = true
      },
      closeModal() {
        let element = this.$refs.modal.$el;
        $(element).modal("hide");
      },
      formatIndicator(field) {
        return field ? 'Y' : 'N'
      },
      loadNeighborhood() {
        this.$Progress.start();
        axios
          .get("/api/polygon/" + this.$route.params.path)
          .then(data => {
            this.neighborhood = data.data;
            this.sortMedia(data.data.media);
            this.images = data.data.media;
            this.stats = this.neighborhood.statisticTypes;
            console.log(this.stats);

            this.loading = false;

            axios
              .get("/api/polygon/schools/" + this.neighborhood.id)
              .then(data => {
                this.schools = data.data.schools;
                this.schoolDistricts = data.data.districts;
                //   console.log(this.schools);
              })
              .catch(err => {
                console.log(err);
                // this.$Progress.finish();
              });

            let innerElems = description.querySelectorAll("*");
            let innerElemsMobile = descriptionMobile.querySelectorAll(
              "*"
            );

            innerElems.forEach(el => {
              el.removeAttribute("style");
            });
            innerElemsMobile.forEach(el => {
              el.removeAttribute("style");
            });

            // axios
            //     .get("/api/polygon/points-of-interest/" + this.neighborhood.slug)
            //     .then(data => {
            //         this.points = data.data.data;
            //     })
            //     .catch(err => {
            //         console.log(err);
            //         // this.$Progress.finish();
            //     });
          })
          .catch(err => {
            this.$Progress.finish();
          });
        this.$Progress.finish();
      },
      findInsideTree(id) {
        for (let node of this.neighborhood.descendants) {
          if (node.id == id) {
            return node;
          }

          for (let child of node.children) {
            if (child.id == id) {
              return child;
            }

            for (let child2 of child.children) {
              if (child2.id == id) {
                return child2;
              }

              for (let child3 of child2.children) {
                if (child3.id == id) {
                  return child3;
                }
              }
            }
          }
        }
        return null;
      },
      onNeighborhoodClicked(node) {
        // If clicked on the text
        if (event.target.tagName == 'SPAN' || event.target.nodeName == 'SPAN') {
          let data = this.findInsideTree(node.id)
          window.location.href = data.page_url;
        }
        return false;
      },
      sortMedia(media) {
        media.sort((a, b) => {
          return a.order_column - b.order_column;
        });
      }
    },
    created(){
      EventBus.$on("properties", (data) => {
        this.propertiesLength = data;
      })
    },
    mounted() {
      this.loadNeighborhood();
    },
    beforeDestroy() {
      this.closeModal();
    }
  };

</script>
<style scoped>
.loading-state {
  padding: 50px 0;
}

.modal-link a {
  color: #c41230 !important;
}

.neighborhood-desc {
  width: 100%;
  padding-right: 80px;

}

.neighborhood-desc .card .card-body .card-text {
  padding: 1.25rem;
}

@media only screen and (max-width: 480px) {
  .neighborhood-desc {
    padding-left: 1rem;
    padding-right: 1rem;
    padding-bottom: 1.5rem;
  }
}

.neighborhood-info-header {
  background-color: #022E55;
}

.neighborhood-info-header h3 {
  color: #fff;
}

.tree-arrow.has-child::after {
  border-color: #fff;
}

.poly-hierarchy {
  color: #022E55;
}

.builder-header {
  display: flex;

}

#section-two {
  background-color: #fff;
}

#section-two > div {
  margin: 0 auto -25px auto;
  width: 100% !important;
  padding:20px 16.7% 20px 16.6%;
}

#section-two .card-info {
  padding-top: 0;
}

@media screen and (max-width: 991px) {
   #section-two > div {
    margin: 0;
    padding: 8px 0;
  }
}

.links-box .card {
  background-color: #022E55;
}

.links-box .card-title {
  color: #fff;
  margin-top: 0;
}

.links-box hr {
  border-color: #fff;
}

.links-box .inner-links li {
  margin: 4px 0;
}

.links-box .inner-links a {
  color: #ffc603;
}

.view-neighs {
  display: block;
  background: #ffc603;
  text-align: center;
  padding: 0.65rem 0.5rem 0.5rem !important;
  color: #012e55;
  font-weight: bold;
}

.desc {
  width: 19.6%;
}

.desc hr {
  width: 90%;
  margin: 0 auto;
}

.tree-checkbox {
  width: 15px;
  height: 15px;
  background: #000;
  border: 1px solid #fff;
}

.tree-checkbox::after {
  opacity: 0;
  transition: 0.2s;
}

.tree-checkbox.checked,
.tree-checkbox.indeterminate {
  background: #000;
  border-color: #fff;
}

.tree-checkbox.checked::after {
  background: #ffc501;
  border: none;
  width: 6px;
  height: 6px;
  top: 50%;
  left: 50%;
  transform: translate(-50%, -50%);
  opacity: 1;
  transition: 0.2s;
}

.tree-checkbox.indeterminate::after {
  background-color: #ffc501;
  top: 50%;
  left: 50%;
  right: initial;
  height: 2px;
  width: 6px;
  transform: translate(-50%, -50%);
  opacity: 1;
}

.breadcrumbs .item a {
  color: white;
}

.breadcrumbs .item a:not(.has-link) {
  cursor: default;
}

.breadcrumbs .item a:not(.has-link):hover {
  text-decoration: none;
}

.breadcrumbs .item:not(:last-child):after {
  content: '/';
  color: #ffc501;
  margin: 0 5px 0;
}

</style>
