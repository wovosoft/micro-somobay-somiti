<template>
    <!--    <pre v-html="JSON.stringify(item)"></pre>-->
    <table v-if="item" class="p-datatable">
        <tr v-for="x in Object.keys(item)" class="row">
            <template v-if="!['deposits'].includes(x)">
                <td style="text-transform: capitalize">{{ x.toString().replaceAll("_", " ") }}</td>
                <td>
                    <template v-if="['total_deposit','total_withdraw','balance'].includes(x)">
                        {{ new Intl.NumberFormat('en-US', {style: 'currency', currency: 'BDT'}).format(item[x]) }}
                    </template>
                    <template v-else-if="['created_at','updated_at'].includes(x) && item[x]">
                        {{
                            new Intl.DateTimeFormat("id", {
                                year: 'numeric', month: '2-digit', day: '2-digit',
                                hour: 'numeric', minute: 'numeric', second: 'numeric',
                                hour12: true,
                                timeZone: 'Asia/Dhaka'
                            }).format(new Date(item[x]))
                        }}
                    </template>
                    <template v-else>{{ item[x] }}</template>
                </td>
            </template>
        </tr>
        <tr>
            <td>Balance</td>
            <td>{{new Intl.NumberFormat('en-US', {style: 'currency', currency: 'BDT'}).format(item.total_deposit - item.total_withdraw)}}</td>
        </tr>
    </table>
</template>

<script>
import axios from "axios";

export default {
    name: "MemberView",
    props: {
        memberId: {
            type: Number,
            required: true
        }
    },
    data() {
        return {
            item: null
        }
    },
    beforeMount() {
        axios
            .post("/admin/members/profile/" + this.memberId)
            .then(res => {
                this.item = res.data;
            })
            .catch(err => {
                console.log(err.response)
                this.item = null;
            });
    }
}
</script>
<style scoped>
table, tr, th, td {
    border: 1px solid lightgray;
    border-collapse: collapse;
}

th, td {
    padding: 5px 10px;
}

table {
    width: 100%;
}
</style>

