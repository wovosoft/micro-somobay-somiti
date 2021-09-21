<template>
    <Toolbar class="p-mb-3 p-p-1">
        <template #left>
            <Button label="New" icon="pi pi-plus" class="p-button-secondary p-button-sm" @click="addFormShow"/>
            <Button label="Delete" icon="pi pi-trash" class="p-button-secondary p-ml-2 p-button-sm"
                    @click="trashItems" v-if="selectedProducts.length"/>
        </template>
        <template #right>
            <InputText type="search" @input="getItems" placeholder="Search Users" v-model="filter"/>
        </template>
    </Toolbar>
    <DataTable
        sort-field="id"
        :value="items"
        class="p-datatable-sm"
        resizable-columns
        editMode="row"
        data-key="id"
        @row-edit-save="rowEditSave"
        v-model:selection="selectedProducts"
        v-model:editingRows="editingRows">
        <Column selectionMode="multiple" style="width: 40px;"></Column>
        <Column :rowEditor="true" bodyStyle="text-align:center" style="width: 80px"></Column>
        <Column field="id" header="ID" :sortable="true"/>

        <Column field="name" header="Name" :sortable="true">
            <template #editor="item">
                <InputText v-model="item.data.name" style="width: 100%;"/>
            </template>
        </Column>
        <Column field="email" header="Email" :sortable="true">
            <template #editor="item">
                <InputText v-model="item.data.email" type="email" style="width: 100%;"/>
            </template>
        </Column>
        <Column field="password" header="Password" :sortable="true">
            <template #editor="item">
                <InputText v-model="item.data.password" style="width: 100%;"
                           placeholder="Only Fill when changes required" type="password"/>
            </template>
        </Column>
        <Column field="created_at" header="Created At" :sortable="true">
            <template #body="item">
                {{
                    new Intl.DateTimeFormat("id", {
                        year: 'numeric', month: '2-digit', day: '2-digit',
                        hour: 'numeric', minute: 'numeric', second: 'numeric',
                        hour12: true,
                        timeZone: 'Asia/Dhaka'
                    }).format(new Date(item.data.created_at))
                }}
            </template>
        </Column>
        <template #footer>
            <div class="p-grid">
                <div class="p-col-12 p-md-6">
                    Total: {{ datatable.total }}
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
            @hide="item=null"
            modal
            header="User Info"
            position="top"
            close-on-escape
            :draggable="false"
            :breakpoints="{'960px': '75vw', '640px': '100vw'}"
            :style="{width: '500px'}"
            maxiizable>
        <form ref="the_form" @submit.prevent="handleSubmit">
            <div class="p-fluid p-formgrid p-grid">
                <div class="p-field p-col-12">
                    <label>Name</label>
                    <InputText
                        placeholder="User Name"
                        v-model="item.name"
                        :required="true"
                        type="text"/>
                </div>
                <div class="p-field p-col-12">
                    <label>Email</label>
                    <InputText
                        placeholder="User Name"
                        v-model="item.email"
                        :required="true"
                        type="email"/>
                </div>
                <div class="p-field p-col-12">
                    <label>Password</label>
                    <InputText
                        placeholder="Password"
                        v-model="item.password"
                        :required="true"
                        type="password"/>
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
import InputNumber from "primevue/inputnumber"
import Toolbar from "primevue/toolbar";

export default {
    components: {
        DataTable, Column, Button, Dialog, InputText, Toast, Paginator,
        InputNumber, Toolbar
    },
    mounted() {
        this.getItems();
    },

    data() {
        return {
            query: {},
            editingRows: [],
            selectedProducts: [],
            items: [],
            datatable: {
                offset: 0,
                per_page: 15,
                current_page: 1,
                total: 0
            },
            form_visibility: false,
            item: null,
        }
    },

    methods: {
        rowEditSave(e) {
            axios
                .post("/admin/users/store", JSON.parse(JSON.stringify(e.data)))
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

        getItems(params = {}) {
            axios.post("/admin/users", {...params, ...this.datatable, filter: this.filter}).then(res => {
                this.items = res.data.data;
                this.datatable.total = res.data.total;
            }).catch(err => {
                this.items = [];
                this.datatable.total = 0;
                console.log(err.response)
            });
        },

        pageChanged(p) {
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
                .post("/admin/users/store", JSON.parse(JSON.stringify(this.item)))
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
                    console.log(err.response);
                });
        },
        trashItems() {
            if (this.selectedProducts.length && confirm("Are You Sure?")) {
                let ids = this.selectedProducts.map(i => i.id);
                axios
                    .post("/admin/users/destroy", {id: ids})
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

