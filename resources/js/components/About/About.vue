<template>
  <PrimaryLayout>
    <template v-slot:contentlayout>
      <TopNavigation />

      <div id="pagebody">
        <div id="content" class="container">
          <LeftSidebar></LeftSidebar>
          <div id="right" class="pl-4">
            <div class="card border-0">
              <div class="card-header">
                <h2 class="card-title m-0 py-2">
                  {{ $t('pages.about_us.title') }}
                </h2>
              </div>
              <div class="card-body">
                <div class="row">
                  <div class="col">
                    <div class="about-text-justify">
                      <p class="mb-3 mb-lg-4">
                        A third generation Houstonian and a graduate of Texas A&M University, Ray has over 38 years of experience in real estate. Ray has been affiliated with several of Houston's premier real estate firms. In 2000, he helped build the nation's first on-line brokerage, eRealty.com. They helped define how buying and selling homes is transacted today. Ray is a leader among his peers, who honored him by naming him a Trustee for the Texas Association of Realtors Political Action Committee (TREPAC) in 2006 through 2009.
                      </p>

                      <div class="about-quote mb-3 mb-lg-4">
                        <div class="content">
                          <div>"I am very proud that Houston is my hometown. Compare our city to others and you will find diversity, spirit, and life like no other."</div>

                          <div class="signature">
                            - Ray Walker
                          </div>
                        </div>
                        <div class="image">
                          <img src="/images/ray_hess.jpg" />
                        </div>
                      </div>

                      <p class="mb-3 mb-lg-4">
                        Ray's newest endeavor is his own Brokerage, RealtyWalk. The web site is a patented-interactive-map that allows users a new, fun to use way of searching for homes.
                      </p>

                      <p class="contact-me mb-3 mb-lg-4">
                        <span class="font-weight-bold">
                          Contact Ray:
                        </span>
                        <br/>

                        Cell:
                        <a href="tel:2816878429" class="link">281.687.8429</a>
                        &nbsp;<strong>|</strong>&nbsp;
                        Email: <a href="mailto:walker@realtywalk.com" class="link">walker@realtywalk.com</a>
                      </p>
                    </div>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </template>
  </PrimaryLayout>
</template>

<script>
import PrimaryLayout from '../layout/PrimaryLayout/PrimaryLayout'
import LeftSidebar from '../layout/LeftSidebar'
import TopNavigation from '../layout/TopNavigation'

export default {
  name: 'About',
  components: {
    LeftSidebar,
    PrimaryLayout,
    TopNavigation
  }
};
</script>

<style lang="scss" scoped>
.about-text-justify {
  text-align: justify;
  text-justify: inter-word;
  color: #374151;
  line-height: 1.5;
}
.about-text-underline {
  text-decoration: underline;
}

ul.about-text {
  list-style-type: none;
  margin: 0;
  padding: 0;
}

ul.about-text li > p {
  font-size: 31px;
}

.about-quote {
  display: flex;
  justify-content: space-between;
  text-align: left;

  > div {
    width: 50%;
  }

  @media screen and (max-width: 1199px) {
    flex-direction: column;
    // justify-content: center;

    > div {
      width: 100%;
    }
  }

  .content {
    display: flex;
    flex-direction: column;
    justify-content: center;

    font-style: italic;
    font-size: 28px;
    background: rgb(235,235,235);
    padding: 40px;

    @media screen and (max-width: 768px) {
      font-size: 22px;
      padding: 30px;
    }

    .signature {
      width: 100%;
      text-align: right;
      margin-top: 25px;
      font-weight: bold;
    }
  }

  .image img {
    width: 100%;
    height: 100%;
    object-fit: cover;
    object-position: center;
  }
}

.contact-me {
  font-weight: 500;

  .link {
    color: #000;
    text-decoration: none;

    &:hover {
      text-decoration: underline;
    }
  }
}
</style>
