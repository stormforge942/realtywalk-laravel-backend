<template>
    <div class="modal fade" tabindex="-1" role="dialog" data-backdrop="static">
        <div class="modal-dialog modal-dialog-centered" role="document" :class="contentClass">
            <div class="modal-content">
                <div class="modal-header" v-if="showCloseBtn">
                    <slot name="header">
                        <button type="button" class="close" @click="closeModal">
                            x
                        </button>
                    </slot>
                </div>

                <slot name="body"></slot>

                <slot name="footer"></slot>
            </div>
        </div>
    </div>
</template>

<script>
export default {
    name: "Modal",
    props: {
        showCloseBtn: {
            type: Boolean,
            default: true
        },
        contentClass: {
            type: String,
            default: ''
        }
    },
    methods: {
        closeModal() {
            this.$emit("closeModal");
        }
    }
};
</script>

<style scoped>
.modal-header {
    border-bottom: 0px solid;
}

.modal-header button {
    outline: 0px;
    box-shadow: none;
}
</style>
