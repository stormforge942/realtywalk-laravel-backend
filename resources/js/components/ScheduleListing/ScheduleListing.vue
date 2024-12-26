<template>
  <div>
    <div>
      <button
        type="button"
        class="btn-add"
        :class="classBtn"
        @click="showModal"
      >
        {{ $t('schedule.listing.btn_view') }}
        <span class="calendar-icon"></span>
      </button>
    </div>
    <Modal ref="scheduleModal" :showCloseBtn="false" @closeModal="closeModal">
      <template v-slot:body>
        <div class="modal-body" >
          <ScheduleForm :itemId="itemId" :closeModal="closeModal" />
        </div>
      </template>
    </Modal>
  </div>
</template>

<script>
import $ from "jquery";
import Modal from "../utils/Modal/Modal";
import ScheduleForm from "./ScheduleForm/ScheduleForm";

export default {
  name: "ScheduleListing",
  data () {
    return {
      isNeighborhoodPath: false,
    }
  },
  components: {
    Modal,
    ScheduleForm
  },
  props: {
    isLoggedIn: {
      type: Boolean,
      default: false
    },
    itemId: {
      type: Number
    },
    classBtn: {
      type: String,
      default: ""
    }
  },
  mounted () {
    let element = this.$refs.scheduleModal.$el
    let _path = this.$route.path;

    $(element).on('hidden.bs.modal', function (e) {
      this.isNeighborhoodPath = _path.includes('neighborhood')

      if (this.isNeighborhoodPath) {
        document.body.classList.add('modal-open')
      }
    })
  },
  methods: {
    showModal() {
      let element = this.$refs.scheduleModal.$el;
      $(element).modal("show");
    },
    closeModal() {
      let element = this.$refs.scheduleModal.$el;
      $(element).modal("hide");
    },
    gotoLogin() {
      this.closeModal();
      this.$router.push(`/users/signin`);
    }
  }
};
</script>

<style scoped>
button {
  border: 0px solid;
  padding: 2px 10px;
  box-shadow: none;
  outline: 0;
  background: none;
  display: flex;
  align-items: center;
}

.btn-add {
  color: #012e55;
}

.calendar-icon {
  background-image: url(/images/calendar.png);
  height: 20px;
  width: 20px;
  display: inline-block;
  vertical-align: middle;
  margin-left: 6px;
  background-size: contain;
  background-repeat: no-repeat;
}
</style>
