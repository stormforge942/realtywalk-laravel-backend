<template>
    <div>
        <div class="form-group col-md-6">
            <label>{{ roleLabel }}</label>
            <treeselect
                name="role"
                v-model="role"
                placeholder="Select user role"
                :options="roles"
            />
        </div>

        <div v-if="role === 'builder'" class="form-group col-md-6">
            <label>{{ builderLabel }}</label>
            <builder-select
                name="builder_id"
                fetch-url="/system/builders/list"
                :selected-option="defaultBuilder"
            />
        </div>
    </div>
</template>

<script>
import Treeselect from "@riophae/vue-treeselect";
import "@riophae/vue-treeselect/dist/vue-treeselect.css";

export default {
    components: {
        Treeselect
    },
    props: {
        roles: { type: Array, required: true },
        defaultRole: { type: Number },
        defaultBuilder: { type: Number },
        roleLabel: { type: String, default: 'Role' },
        builderLabel: { type: String, default: 'Builder' }
    },
    data() {
        return {
            role: "admin"
        };
    },
    mounted() {
        this.role = this.defaultRole;
    }
};
</script>
