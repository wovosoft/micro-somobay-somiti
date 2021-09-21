const Home = () => import("@/Pages/Admin/Home")
const Members = () => import("@/Pages/Admin/Members")
const Expenses = () => import("@/Pages/Admin/Expenses")
const Deposits = () => import("@/Pages/Admin/Deposits")
const Withdraws = () => import("@/Pages/Admin/Withdraws")
const Users = () => import("@/Pages/Admin/Users")
export default [
    {path: '/', component: Home},
    {path: '/members', component: Members, name: 'members'},
    {path: '/expenses', component: Expenses, name: 'expenses'},
    {path: '/deposits', component: Deposits, name: 'deposits'},
    {path: '/withdraws', component: Withdraws, name: 'withdraws'},
    {path: '/users', component: Users, name: 'users'},
]
