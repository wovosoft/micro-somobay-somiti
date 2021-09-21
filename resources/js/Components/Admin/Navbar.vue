<template>
    <Menubar id="the_menu" :model="items">
        <template #start>
            <router-link
                to="/"
                style="color: white; padding-left: 10px;text-decoration: none;"
                class="p-text-uppercase">
                <span v-html="appName"></span>
            </router-link>
        </template>

        <template #end>
            <span style="color: white; padding-left: 10px;padding-right:15px;">
               {{ user.name }}
            </span>
        </template>
    </Menubar>
</template>

<script>
import Menubar from "primevue/menubar";

export default {
    name: "Navbar",
    components: {Menubar},
    computed: {
        user() {
            return user;
        },
        appName() {
            return APP_NAME;
        },
    },
    data() {
        return {
            items: [
                {
                    label: "Members",
                    icon: "pi pi-fw pi-file",
                    to: {name: "members"},
                },
                {
                    label: "Expenses",
                    icon: "pi pi-fw pi-file",
                    to: {name: "expenses"},
                },
                {
                    label: "Deposits",
                    icon: "pi pi-fw pi-file",
                    to: {name: "deposits"},
                },
                {
                    label: "Withdraws",
                    icon: "pi pi-fw pi-file",
                    to: {name: "withdraws"},
                },
                {
                    label: "Users",
                    icon: "pi pi-fw pi-file",
                    to: {name: "users"},
                },

                {
                    label: "Logout",
                    icon: "pi pi-fw pi-power-off",
                    command: () => {
                        document.getElementById("logout-form").submit();
                    },
                },
            ],
        };
    },
};
</script>

<style>
#the_menu {
    border-radius: 0 !important;
    border: 0 !important;
    padding: 0 !important;
    background: #1f2937;
}

#the_menu .p-menuitem > a:hover {
    background: initial !important;
}

#the_menu .p-menuitem-icon,
#the_menu .p-menuitem-text {
    color: white !important;
}

#the_menu .p-submenu-list .p-menuitem-text,
#the_menu .p-submenu-list .p-menuitem-icon {
    color: #1a202c !important;
}
</style>
