<template>
    <navbar></navbar>
    <router-view/>
</template>

<script>
import Navbar from "@/Components/Admin/Navbar";

export default {
    components: {Navbar}
}
</script>

<style>
.p-datatable-wrapper .p-cell-editing {
    padding: 0 !important;
}

.p-datatable-wrapper .p-cell-editing input {
    border-radius: 0;
}

.p-datatable-wrapper .p-datatable-tbody > tr > td {
    padding: 1px 5px !important;
}

.p-datatable-wrapper .p-dropdown {
    border-radius: 0;
    width: 100%;
}

.p-datatable-wrapper .p-dropdown-label.p-inputtext {
    padding: 5px;
}

.p-dialog-header {
    padding: 5px 15px !important;
}
.p-dialog-footer {
    padding: 5px 15px !important;
}
</style>
