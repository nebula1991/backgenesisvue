import { createRouter, createWebHistory} from "vue-router";
import VueCookies, { useCookies } from 'vue3-cookies'

const {cookies} = useCookies()

//Importar componentes

import Login from "../Pages/Auth/Login.vue";
import Dashboard from "../Pages/Dashboard.vue";
import CategoryList from "../Pages/CategoryList.vue";
import SaveProduct from '../Components/SaveProductComponent.vue';
import ProductList from "../Pages/ProductList.vue";
import SaveCategory from "../Components/SaveCategory.vue";
import SubcategoryList from "../Pages/SubcategoryList.vue";
import SaveSubcategory from "../Components/SaveSubcategory.vue";




    const routes = [


        {
            name: "login",
            path: "/vue/login",
            component: Login,
        },
        {
            name: "dashboard",
            path: "/vue/dashboard",
            component: Dashboard,
        },
  
        {
            name: "categories",
            path: "/vue/categories",
            component: CategoryList
        },
        {
            name: "saveCategory",
            path: "/vue/saveCategory/:id?",
            component: SaveCategory
        },
        {
            name: "subcategories",
            path: "/vue/subcategories",
            component: SubcategoryList
        },
        {
            name: "saveSubcategory",
            path: "/vue/saveSubcategory/:id?",
            component: SaveSubcategory
        },
        {
            name: "products",
            path: "/vue/products",
            component: ProductList
        },
        {
            name: "saveProduct",
            path: "/vue/saveProduct/:id?",
            component: SaveProduct
        },
        
    

 
]

const router = createRouter({
    history: createWebHistory(),
    routes: routes
})

router.beforeEach((to, from, next) => {
    const auth = cookies.get('auth');

    if (!auth && to.name !== 'login') {
        return next({ name: 'login' });
    }

    if (auth && to.name === 'login') {
        return next({ name: 'dashboard' }); // Redirigir al dashboard si ya está autenticado
    }

    next();
});
export default router;

