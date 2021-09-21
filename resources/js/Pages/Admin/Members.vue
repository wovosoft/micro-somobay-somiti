<template>
    <Toolbar class="p-mb-3 p-p-1">
        <template #left>
            <Button
                label="New"
                icon="pi pi-plus"
                class="p-button-secondary p-button-sm"
                @click="addFormShow"
            />
        </template>
        <template #right>
            <div class="p-inputgroup p-mr-2">
                <InputText
                    type="date"
                    v-model="query.starting_date"
                    class="p-inputtext-sm"
                />
                <InputText
                    type="date"
                    v-model="query.ending_date"
                    class="p-inputtext-sm"
                />
                <InputText
                    @input="getItems"
                    v-model="filter"
                    placeholder="Search Members"
                    type="search"
                />
                <Button
                    label="Balance Sheet"
                    icon="pi pi-list"
                    class="p-button-secondary p-button-sm p-ml-2"
                    @click="exportBalanceSheet"
                />
                <Button
                    label="Reset"
                    icon="pi pi-list"
                    class="p-button-secondary p-button-sm p-ml-2"
                    @click="
                        filter = null;
                        query.starting_date = null;
                        query.ending_date = null;
                    "
                />
            </div>
        </template>
    </Toolbar>
    <DataTable :value="items" responsiveLayout="scroll" class="p-datatable-sm">
        <Column field="id" header="ID"></Column>
        <Column field="name" header="Name"></Column>
        <Column field="pf_index" header="PF Index"></Column>
        <Column field="current_workplace" header="Current Workplace"></Column>
        <Column field="phone" header="Phone"></Column>
        <Column header="Action">
            <template #body="row">
                <div class="p-buttonset">
                    <Button
                        class="p-button-sm p-button-secondary"
                        @click="showViewModal(row)"
                        icon="pi pi-eye"
                    />

                    <Button
                        class="p-button-sm"
                        @click="rowClicked(row)"
                        icon="pi pi-pencil"
                    />

                    <Button
                        class="p-button-sm p-button-danger"
                        @click="trash(row)"
                        icon="pi pi-trash"
                    />
                </div>
            </template>
        </Column>
        <template #paginatorLeft>
            <Button type="button" icon="pi pi-refresh" class="p-button-text" />
        </template>
        <template #paginatorRight>
            <Button type="button" icon="pi pi-cloud" class="p-button-text" />
        </template>
        <template #footer>
            <div class="p-grid">
                <div class="p-col-12 p-md-6">
                    Total: {{ datatable.total }}
                    <!--                    {{ datatable }}-->
                </div>
                <div class="p-col-12 p-md-6">
                    <paginator
                        @page="pageChanged"
                        always-show
                        v-model:first="datatable.offset"
                        :total-records="datatable.total"
                        :rows="datatable.per_page"
                        :rowsPerPageOptions="[
                            10, 15, 20, 30, 50, 100, 250, 300, 350, 500,
                        ]"
                    />
                </div>
            </div>
        </template>
    </DataTable>
    <Toast
        :breakpoints="{ '920px': { width: '100%', right: '0', left: '0' } }"
    ></Toast>
    <Dialog
        v-model:visible="form_visibility"
        closable
        @hide="item = null"
        modal
        position="top"
        close-on-escape
        :draggable="false"
        :breakpoints="{ '960px': '75vw', '640px': '100vw' }"
        :style="{ width: '90vw' }"
        maximizable
        header="Member Details"
    >
        <form ref="the_form" @submit.prevent="handleSubmit">
            <div class="p-fluid p-formgrid p-grid">
                <div class="p-field p-col-12 p-md-6 p-lg-3">
                    <label>Membership No</label>
                    <InputText
                        v-model="item.membership_no"
                        :required="true"
                        type="text"
                    />
                </div>
                <div class="p-field p-col-12 p-md-6 p-lg-3">
                    <label>PF Index</label>
                    <InputText
                        v-model="item.pf_index"
                        :required="true"
                        type="text"
                    />
                </div>
                <div class="p-field p-col-12 p-md-6 p-lg-3">
                    <label>Name</label>
                    <InputText v-model="item.name" type="text" />
                </div>
                <div class="p-field p-col-12 p-md-6 p-lg-3">
                    <label>Current Workplace</label>
                    <InputText v-model="item.current_workplace" type="text" />
                </div>
                <div class="p-field p-col-12 p-md-6 p-lg-3">
                    <label>Bank Joining Date</label>
                    <InputText v-model="item.bank_joining_date" type="date" />
                </div>
                <div class="p-field p-col-12 p-md-6 p-lg-3">
                    <label>Entry Date</label>
                    <InputText
                        v-model="item.membership_entry_date"
                        type="date"
                    />
                </div>
                <div class="p-field p-col-12 p-md-6 p-lg-3">
                    <label>Home District</label>
                    <Dropdown
                        :filter="true"
                        :options="districts"
                        v-model="item.home_district"
                        optionLabel="name"
                        optionValue="name"
                    />
                </div>
                <div class="p-field p-col-12 p-md-6 p-lg-3">
                    <label>NID No.</label>
                    <InputText v-model="item.nid_no" type="text" />
                </div>
                <div class="p-field p-col-12 p-md-6 p-lg-3">
                    <label>Tin No</label>
                    <InputText v-model="item.tin_no" type="text" />
                </div>
                <div class="p-field p-col-12 p-md-6 p-lg-3">
                    <label>Phone</label>
                    <InputText v-model="item.phone" type="text" />
                </div>
                <div class="p-field p-col-12 p-md-6 p-lg-3">
                    <label>Secondary Phone</label>
                    <InputText v-model="item.secondary_phone" type="text" />
                </div>
                <div class="p-field p-col-12 p-md-6 p-lg-3">
                    <label>Email</label>
                    <InputText v-model="item.email" type="email" />
                </div>
            </div>
        </form>

        <template #footer>
            <Button
                label="Cancel"
                icon="pi pi-times"
                @click="
                    form_visibility = false;
                    item = null;
                "
                class="p-button-text p-button-sm"
            />
            <Button
                label="Submit"
                icon="pi pi-check"
                @click="handleSubmit"
                class="p-button-sm"
            />
        </template>
    </Dialog>
    <Dialog
        v-model:visible="show_view"
        header="Member Profile"
        @hide="item = null"
        modal
        position="top"
        close-on-escape
        :draggable="false"
        :breakpoints="{ '960px': '75vw', '640px': '100vw' }"
        :style="{ width: '50vw' }"
        maximizable
    >
        <MemberView :member-id="memberId" />
        <template #footer>
            <Button
                label="Close"
                @click="show_view = false"
                icon="pi pi-times"
                class="p-button-text"
            />
        </template>
    </Dialog>
</template>

<script>
import axios from "axios";
import DataTable from "primevue/datatable";
import Column from "primevue/column";
import Button from "primevue/button";
import Dialog from "primevue/dialog";
import InputText from "primevue/inputtext";
import Toast from "primevue/toast";
import Paginator from "primevue/paginator";
import Toolbar from "primevue/toolbar";
import Dropdown from "primevue/dropdown";
import MemberView from "@/Components/Admin/MemberView";
import districts from "@/partials/districts";
export default {
    components: {
        DataTable,
        Column,
        Button,
        Dialog,
        InputText,
        Toast,
        Paginator,
        Toolbar,
        MemberView,
        Dropdown,
    },
    computed: {
        districts() {
            return districts;
        },
    },
    mounted() {
        this.getItems();
    },
    data() {
        return {
            filter: null,
            query: {
                starting_date: null,
                ending_date: null,
            },
            items: [],
            datatable: {
                offset: 10,
                per_page: 10,
                current_page: 1,
                total: 0,
            },
            form_visibility: false,
            item: null,
            memberId: null,
            show_view: false,
        };
    },
    methods: {
        exportBalanceSheet() {
            let form = document.createElement("form");
            form.setAttribute("action", "/admin/members/export/balance_sheet");
            form.setAttribute("method", "POST");
            form.setAttribute("target", "_blank");
            form.setAttribute("hidden", "hidden");

            let token = document.createElement("input");
            token.setAttribute("name", "_token");
            token.setAttribute(
                "value",
                document.querySelector('[name="csrf-token"]').content
            );
            token.setAttribute("hidden", "hidden");
            form.appendChild(token);
            let params = { ...this.query, filter: this.filter };
            for (let x in params) {
                if (params[x]) {
                    let input = document.createElement("input");
                    input.setAttribute("name", x);
                    if (typeof params[x] === "number") {
                        input.setAttribute("type", "number");
                    }
                    input.setAttribute("value", params[x]);
                    form.appendChild(input);
                }
            }

            document.body.appendChild(form);
            form.submit();
            document.body.removeChild(form);
        },
        getItems(params = {}) {
            return axios
                .post("/admin/members", { ...params, filter: this.filter })
                .then((res) => {
                    this.items = res.data.data;
                    this.datatable.total = res.data.total;
                })
                .catch((err) => {
                    this.items = [];
                    this.datatable.total = 0;
                });
        },
        pageChanged(p) {
            console.log(p);
            this.getItems({
                page: p.page + 1,
                per_page: p.rows,
            });
        },
        rowClicked(row) {
            this.form_visibility = true;
            this.item = JSON.parse(JSON.stringify(row.data));
        },
        showViewModal(row) {
            this.show_view = true;
            this.memberId = row.data.id;
        },
        trash(row) {
            if (confirm("Are You Sure?")) {
                axios
                    .post("/admin/members/destroy", { id: row.data.id })
                    .then((res) => {
                        this.$toast.add({
                            severity: "success",
                            summary: "Success",
                            detail: res.data.message,
                            life: 3000,
                            position: "top-right",
                        });
                        this.getItems();
                    })
                    .catch((err) => {
                        this.$toast.add({
                            severity: "error",
                            summary: err.response.data.message,
                            detail: Object.values(
                                err.response.data.errors
                            ).join(" | "),
                            life: 3000,
                            position: "top-right",
                        });
                        console.log(err.response);
                    });
            }
        },
        handleSubmit() {
            if (!this.$refs.the_form.checkValidity()) {
                this.$refs.the_form.reportValidity();
                return false;
            }
            axios
                .post(
                    "/admin/members/store",
                    JSON.parse(JSON.stringify(this.item))
                )
                .then((res) => {
                    this.$toast.add({
                        severity: "success",
                        summary: "Success",
                        detail: res.data.message,
                        life: 3000,
                        position: "top-right",
                    });
                    this.form_visibility = false;
                    this.item = null;
                    this.getItems();
                })
                .catch((err) => {
                    this.$toast.add({
                        severity: "error",
                        summary: err.response.data.message,
                        detail: Object.values(err.response.data.errors).join(
                            " | "
                        ),
                        life: 3000,
                        position: "top-right",
                    });
                });
        },
        addFormShow() {
            this.form_visibility = true;
            this.item = {};
        },
    },
};
</script>
