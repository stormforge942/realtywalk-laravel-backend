<template>
  <PrimaryLayout>
    <template v-slot:contentlayout>
      <div id="content" class="container">
        <LeftSidebar></LeftSidebar>
        <div id="right">
          <div class="card border-0">
            <div class="card-header">
              <h2 class="card-title m-0 py-0">
                {{ $t('profile.user.title') }}
              </h2>
            </div>
            <div class="card-body">
              <div class="row py-4">
                <div class="col-md-7 mx-md-auto">
                  <UpdateProfileForm />
                </div>
              </div>
            </div>
          </div>

          <div class="card border-0">
            <div class="card-header">
              <h2 class="card-title m-0 py-0">
                {{ $t('profile.user.change_password_title') }}
              </h2>
            </div>
            <div class="card-body">
              <div class="row py-4">
                <div class="col-md-7 mx-md-auto">
                  <UpdatePasswordForm />
                </div>
              </div>
            </div>
          </div>
        </div>

        <div v-if="role === 'builder'" id="right" class="mt-5">
          <div class="card border-0">
            <div class="card-header">
              <h2 id="builder_profile" class="card-title m-0 py-0">
                {{ $t('profile.builder.title') }}
              </h2>
            </div>
            <div class="card-body">
              <div class="row py-4">
                <div class="col-md-7 mx-md-auto">
                  <BuilderProfileForm />
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
import PrimaryLayout from "../layout/PrimaryLayout/PrimaryLayout";
import LeftSidebar from "../layout/LeftSidebar";
import UpdateProfileForm from "./Forms/UpdateProfileForm";
import UpdatePasswordForm from './Forms/UpdatePasswordForm';
import BuilderProfileForm from "./BuilderProfileForm";

export default {
  name: "UserProfile",
  components: {
    LeftSidebar,
    PrimaryLayout,
    UpdateProfileForm,
    UpdatePasswordForm,
    BuilderProfileForm
  },
  data() {
    return {
      role:
        this.$store.state.auth.user !== null
          ? this.$store.state.auth.user.role
          : null
    };
  }
};
</script>

<style lang="scss">
#right {
  border-left: 0px solid;
}
</style>
