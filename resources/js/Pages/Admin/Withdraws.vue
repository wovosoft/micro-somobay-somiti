<template>
    <Toolbar class="p-mb-3 p-p-1">
        <template #left>
            <Button label="New" icon="pi pi-plus" class="p-button-secondary p-button-sm" @click="addFormShow"/>
            <Button label="Delete" icon="pi pi-trash" class="p-button-secondary p-ml-2 p-button-sm"
                    @click="trashItems" v-if="selectedProducts.length"/>
            <Button label="Export" icon="pi pi-print" class="p-button-secondary p-ml-2 p-button-sm"
                    @click="exportItems"/>
        </template>
        <template #right>
            <div class="p-inputgroup p-mr-2">
                <Dropdown
                    placeholder="Select Type"
                    option-value="value"
                    option-label="label"
                    v-model="query.type"
                    :options="withdraw_types"/>
                <Button icon="pi pi-trash"
                        class="p-button-sm p-button-secondary"
                        @click="query.type=null;"/>
            </div>
            <div class="p-inputgroup p-mr-2">
                <input-text type="date" v-model="query.starting_date" class="p-inputtext-sm"/>
                <input-text type="date" v-model="query.ending_date" class="p-inputtext-sm"/>
                <Button icon="pi pi-trash"
                        class="p-button-sm p-button-secondary"
                        @click="query.starting_date=null;query.ending_date=null;"/>
            </div>
            <div class="p-inputgroup">
                <CustomDropdown
                    placeholder="Select Member"
                    filter-placeholder="Search Member"
                    style="min-width: 300px;"
                    :filter-fields="null"
                    :filter="true"
                    v-model="query.member_id"
                    @filter="searchMembers"
                    :option-label="v=>[v.id+' # '+v.name,v.membership_no].join(' | ')"
                    option-value="id"
                    :options="members">
                </CustomDropdown>
                <Button icon="pi pi-trash"
                        class="p-button-sm p-button-secondary"
                        @click="query.member_id=null;"/>
            </div>
        </template>
    </Toolbar>
    <DataTable
        sort-field="id"
        :value="items"
        class="p-datatable-sm"
        resizable-columns
        editMode="row"
        data-key="id"
        @row-edit-init="rowEditInit"
        @row-edit-save="rowEditSave"
        v-model:selection="selectedProducts"
        v-model:editingRows="editingRows">
        <Column selectionMode="multiple" style="width: 40px;"></Column>
        <Column :rowEditor="true" bodyStyle="text-align:center" style="width: 80px"></Column>
        <Column field="id" header="ID" :sortable="true"/>
        <Column field="date" header="Date" :sortable="true" :editable="true">
            <template #editor="item">
                <InputText type="date" v-model="item.data.date" class="p-inputtext-sm"/>
            </template>
        </Column>
        <Column field="name" header="Name" :sortable="true">
            <template #editor="item">
                <CustomDropdown
                    :filter-fields="null"
                    :filter="true"
                    @filter="searchMembers"
                    v-model="item.data.member_id"
                    :option-label="v=>[v.id+' # '+v.name].join(' | ')"
                    option-value="id"
                    :options="members">
                </CustomDropdown>
            </template>
        </Column>
        <Column field="membership_no" header="Membership No" :sortable="true"/>
        <Column field="pf_index" header="PF Index" :sortable="true"/>
        <Column field="type" header="Type" :sortable="true">
            <template #body="item">
                {{ item.data.type ? withdraw_types.find(i => i.value === item.data.type).label : "" }}
            </template>
            <template #editor="item">
                <Dropdown
                    v-model="item.data.type"
                    option-label="label"
                    option-value="value"
                    :options="withdraw_types"/>
            </template>
        </Column>
        <Column field="description" header="Description" :sortable="true">
            <template #editor="item">
                <InputText v-model="item.data.description" class="p-inputtext-sm" style="width: 100%;"/>
            </template>
        </Column>
        <Column field="amount" header="Amount" :sortable="true">
            <template #editor="item">
                <InputNumber v-model="item.data.amount" class="p-inputtext-sm"/>
            </template>
        </Column>

        <template #paginatorLeft>
            <Button type="button" icon="pi pi-refresh" class="p-button-text"/>
        </template>
        <template #paginatorRight>
            <Button type="button" icon="pi pi-cloud" class="p-button-text"/>
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
                        :rowsPerPageOptions="[10,15,20,30,50,100,250,300,350,500]"
                    />
                </div>
            </div>
        </template>
    </DataTable>
    <Toast :breakpoints="{'920px': {width: '100%', right: '0', left: '0'}}"></Toast>
    <Dialog v-model:visible="form_visibility"
            @show="dropdownShown"
            closable
            @hide="item=null"
            modal
            header="Deposit Info"
            position="top"
            close-on-escape
            :draggable="false"
            :breakpoints="{'960px': '75vw', '640px': '100vw'}"
            :style="{width: '75vw'}"
            maxiizable>
        <form ref="the_form" @submit.prevent="handleSubmit">
            <div class="p-fluid p-formgrid p-grid">
                <div class="p-field p-col-12 p-md-6 p-lg-6">
                    <label>Member *</label>
                    <CustomDropdown
                        required
                        filter-placeholder="Search Member"
                        placeholder="Select Member"
                        :filter-fields="null"
                        :filter="true"
                        @filter="searchMembers"
                        v-model="item.member_id"
                        :option-label="v=>[v.id+' # '+v.name,v.membership_no].join(' | ')"
                        option-value="id"
                        :options="members">
                    </CustomDropdown>
                </div>
                <div class="p-field p-col-12 p-md-6 p-lg-3">
                    <label>Date *</label>
                    <InputText required v-model="item.date" :required="true" type="date"/>
                </div>
                <div class="p-field p-col-12 p-md-6 p-lg-3">
                    <label>Type *</label>
                    <Dropdown
                        required
                        placeholder="Deposit Type"
                        v-model="item.type"
                        option-label="label"
                        option-value="value"
                        :options="withdraw_types"/>
                </div>
                <div class="p-field p-col-12 p-md-6 p-lg-6">
                    <label>Description</label>
                    <InputText placeholder="Deposit Description" v-model="item.description" :required="true"
                               type="text"/>
                </div>
                <div class="p-field p-col-12 p-md-6 p-lg-3">
                    <label>Amount *</label>
                    <InputNumber
                        required
                        placeholder="Deposit Amount"
                        v-model="item.amount"
                        :max-fraction-digits="2"
                        :min="0"/>
                </div>
            </div>
        </form>

        <template #footer>
            <Button label="Cancel" icon="pi pi-times"
                    @click="form_visibility=false;item=null;"
                    class="p-button-text p-button-sm"/>
            <Button label="Submit" icon="pi pi-check"
                    @click="handleSubmit"
                    class="p-button-sm"/>
        </template>
    </Dialog>
</template>

<script>
import axios from "axios"
import DataTable from "primevue/datatable";
import Column from "primevue/column";
import Button from "primevue/button"
import Dialog from "primevue/dialog";
import InputText from "primevue/inputtext";
import Toast from "primevue/toast"
import Paginator from "primevue/paginator";
import Dropdown from "primevue/dropdown";
import InputNumber from "primevue/inputnumber"
import CustomDropdown from "@/Components/Admin/CustomDropdown";
import Toolbar from "primevue/toolbar";

export default {
    components: {
        DataTable, Column, Button, Dialog, InputText, Toast, Paginator, Dropdown,
        InputNumber, CustomDropdown, Toolbar
    },
    mounted() {
        this.getItems();
    },

    data() {
        return {
            query: {
                member_id: null,
                starting_date: null,
                ending_date: null,
                type: null,
            },
            editingRows: [],
            selectedProducts: [],
            items: [],
            members: [],
            datatable: {
                offset: 0,
                per_page: 15,
                current_page: 1,
                total: 0
            },
            form_visibility: false,
            item: null,
            withdraw_types: [
                {value: 'monthly', label: 'Monthly'},
                {value: 'onetime', label: 'One Time'},
                {value: 'others', label: 'Others'}
            ],
        }
    },
    watch: {
        query: {
            deep: true,
            handler() {
                this.getItems();
            }
        }
    },
    methods: {
        sorted(e) {
            console.log(e)
        },
        rowEditInit(e) {
            this.dropdownShown(e.data.member_id);
        },
        rowEditSave(e) {
            axios
                .post("/admin/withdraws/store", JSON.parse(JSON.stringify(e.data)))
                .then(res => {
                    this.$toast.add({
                        severity: 'success',
                        summary: 'Success',
                        detail: res.data.message,
                        life: 3000,
                        position: 'top-right'
                    });
                })
                .catch(err => {
                    this.$toast.add({
                        severity: 'error',
                        summary: err.response.data.message,
                        detail: Object.values(err.response.data.errors).join(" | "),
                        life: 3000,
                        position: 'top-right'
                    });
                });
        },
        dropdownShown(memberId = null) {
            if (memberId && !this.members.find(i => i.id === memberId)) {
                axios
                    .post("/admin/members/find/" + memberId)
                    .then(res => {
                        this.members.push(res.data);
                    })
                    .catch(err => {
                        console.log(err.response.data);
                    });
            }
        },
        searchMembers(e) {
            axios
                .post("/admin/members/search", {filter: e.value})
                .then(res => {
                    this.members = res.data;
                })
                .catch(err => {
                    console.log(err.response.data);
                    this.members = [];
                });
        },
        getItems(params = {}) {
            axios.post("/admin/withdraws", {...params, ...this.datatable, ...this.query}).then(res => {
                this.items = res.data.data;
                this.datatable.total = res.data.total;
            }).catch(err => {
                this.items = [];
                this.datatable.total = 0;
                console.log(err.response)
            });
        },
        exportItems() {
            let form = document.createElement("form");
            form.setAttribute("action", "/admin/withdraws/export");
            form.setAttribute("method", "POST");
            form.setAttribute("target", "_blank");
            form.setAttribute("hidden", "hidden");

            let token = document.createElement("input");
            token.setAttribute("name", "_token");
            token.setAttribute("value", document.querySelector('[name="csrf-token"]').content)
            token.setAttribute("hidden", "hidden");
            form.appendChild(token);
            let params = this.query;
            for (let x in params) {
                if (params[x]) {
                    let input = document.createElement("input");
                    input.setAttribute("name", x);
                    if (typeof params[x] === "number") {
                        input.setAttribute("type", "number");
                    }
                    input.setAttribute("value", params[x])
                    form.appendChild(input);
                }
            }

            document.body.appendChild(form);
            form.submit();
            document.body.removeChild(form);
        },
        pageChanged(p) {
            console.log(p)
            this.getItems({
                page: p.page + 1,
                per_page: p.rows
            });
        },
        addFormShow() {
            this.form_visibility = true;
            this.item = {};
        },
        handleSubmit() {
            if (!this.$refs.the_form.checkValidity()) {
                this.$refs.the_form.reportValidity();
                return false;
            }
            axios
                .post("/admin/withdraws/store", JSON.parse(JSON.stringify(this.item)))
                .then(res => {
                    this.$toast.add({
                        severity: 'success',
                        summary: 'Success',
                        detail: res.data.message,
                        life: 3000,
                        position: 'top-right'
                    });
                    this.form_visibility = false;
                    this.item = null;
                    this.getItems();
                })
                .catch(err => {
                    this.$toast.add({
                        severity: 'error',
                        summary: err.response.data.message,
                        detail: Object.values(err.response.data.errors).join(" | "),
                        life: 3000,
                        position: 'top-right'
                    });
                });
        },
        trashItems() {
            if (this.selectedProducts.length && confirm("Are You Sure?")) {
                let ids = this.selectedProducts.map(i => i.id);
                axios
                    .post("/admin/withdraws/destroy", {id: ids})
                    .then(res => {
                        this.$toast.add({
                            severity: 'success',
                            summary: 'Success',
                            detail: res.data.message,
                            life: 3000,
                            position: 'top-right'
                        });
                        this.getItems();
                        this.selectedProducts = [];
                    })
                    .catch(err => {
                        this.$toast.add({
                            severity: 'error',
                            summary: err.response.data.message,
                            detail: Object.values(err.response.data.errors).join(" | "),
                            life: 3000,
                            position: 'top-right'
                        });
                        console.log(err.response);
                    });
            }
        }
    }
}
</script>

