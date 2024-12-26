<template>
  <!-- {{ property }} -->
  <table id="property-table">
    <tbody>
      <tr id="row-top">
        <td colspan="2"></td>
        <td
          id="pester"
          class="clickable"
          onclick="window.location.href = '/system/users/create'"
        >
          BECOME A MEMBER
        </td>
      </tr>

      <tr id="row-big">
        <td id="logo-cell" class="firstcell">
          <div id="logo-div">
            <a href="/">
              <img src="/images/logo-rw-horizontal.png" alt="" />
            </a>
          </div>

          <br />
          <br />

          {{ property.full_address }}
          <hr />

          <!-- ARMANDWILDE -->

          <div id="back-link" class="clickable">
            <a href="/">{{ $t("general.navigation.back_to_map") }}</a>
          </div>
        </td>

        <!--
                <td id="title-cell">
                    <a id="title-image-anchor" href="/media/property/264681" data-gallery="">
                        <img src="/media/property/264681">
                    </a>
                </td>
                <td id="gallery-cell">
                    <div class="title">Gallery</div>


                            <a href="/media/property/264682" title="" data-gallery="">
                                <img src="/media/property/264682?tile=1&amp;width=75">
                            </a>


                            <a href="/media/property/264683" title="" data-gallery="">
                                <img src="/media/property/264683?tile=1&amp;width=75">
                            </a>


                            <a href="/media/property/264684" title="" data-gallery="">
                                <img src="/media/property/264684?tile=1&amp;width=75">
                            </a>


                            <a href="/media/property/264685" title="" data-gallery="">
                                <img src="/media/property/264685?tile=1&amp;width=75">
                            </a>


                            <br>
                            <a href="javascript:void(0)" style="color: white; margin-left: 6px;" onclick="
                                    // Open lightbox on the first image
                                    $('#title-image-anchor').click();
                               ">View more images</a>

                                                        <a href="/media/property/264687" title="" data-gallery="" style="display:none"></a>
                                                        <a href="/media/property/264688" title="" data-gallery="" style="display:none"></a>
                                                        <a href="/media/property/264689" title="" data-gallery="" style="display:none"></a>
                                                        <a href="/media/property/264690" title="" data-gallery="" style="display:none"></a>
                                                        <a href="/media/property/264691" title="" data-gallery="" style="display:none"></a>
                                                        <a href="/media/property/264692" title="" data-gallery="" style="display:none"></a>
                                                        <a href="/media/property/264693" title="" data-gallery="" style="display:none"></a>
                                                        <a href="/media/property/264694" title="" data-gallery="" style="display:none"></a>

                </td>
            </tr>

            -->
      </tr>

      <tr id="row-skinny">
        <td class="firstcell">Search Results</td>
        <td id="address-cell">
          {{ property.full_address }}
        </td>
        <td class="bluetext blue-border-left">Features</td>
      </tr>

      <tr id="row-youtube">
        <td class="firstcell">{{ property.video_embed }}</td>
        <td id="descr-cell">
          {{ property.descr }}

          <br />

          <a
            data-toggle-status="off"
            data-property-id="13116"
            class="property-fav-button"
            href="javascript:favoriteToggle(13116)"
            ><span class="glyphicon glyphicon-star-empty"></span>Add to
            favorites</a
          >

          &nbsp; &nbsp; &nbsp;

          <a href="javascript:scheduleViewing(13116)">Schedule a Viewing</a>
        </td>
        <td rowspan="2" class="blue-border-left">
          Status: {{ property.status }}<br />
          Price: {{ property.price }}<br />
          Bedrooms: {{ property.bedrooms }}<br />
          Square Feet: {{ property.sqft }}<br />
          Garage Capacity: {{ property.garage_capacity }}<br />
          Built in: {{ property.year_built }}<br />
          Zip: {{ property.zipcode }}<br />
        </td>
      </tr>
      <tr id="row-googlemap">
        <td id="builderlogo-cell" class="firstcell">
          <a :href="`/builder/${property.builder_id}`">{{
            property.mls_number
          }}</a>
        </td>
        <td style="">
          <div style="width: 100%">
            <iframe
              width="100%"
              height="624"
              :src="`https://maps.google.com/maps?width=100%&amp;height=624&amp;hl=en&amp;q=${property.full_address_with_zip}+(${property.full_address_with_zip})&amp;ie=UTF8&amp;t=k&amp;z=14&amp;iwloc=A&amp;output=embed`"
              frameborder="0"
              scrolling="no"
              marginheight="0"
              marginwidth="0"
            ></iframe>
          </div>
        </td>
      </tr>
    </tbody>
  </table>
</template>

<script>
export default {
  name: "SingleProperty",
  props: ["property"],
  data() {
    return {};
  },
};
</script>
