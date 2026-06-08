<template>
    <div :id="'db-submenu-' + section.slug"
        class="dashboard-menu__submenu"
    >
        <div class="dashboard-menu__filter-container">
            <input class="dashboard-menu__filter" placeholder="Filtrar opciones" type="text" v-model="filter" ref="filter">

            <button class="dashboard-menu__filter-btn" v-show="filter" @click="clearFilter">
                &times;
            </button>
        </div>

        <div :id="'db-links-' + section.slug" class="dashboard-menu__link-group-container">

            <dashboard-submenu v-for="submenu in submenus"
                :key="submenu.id"
                :submenu="submenu"
            ></dashboard-submenu>

        </div>
    </div>
</template>

<script>
    import { remove as removeDiacritics } from 'diacritics';
    import DashboardSubmenu from './DashboardSubmenu';

    export default {
        components: { DashboardSubmenu },

        props: {
            section: Object
        },

        data() {
            return {
                filter: '',
                submenus: this.section.submenus
            };
        },

        watch: {
            filter: function() {
                const query = removeDiacritics(this.filter).toLowerCase().trim();

                this.submenus = this.section.submenus.filter(submenu => {
                    return submenu.searchable_name.includes(query);
                });
            }
        },

        methods: {
            /**
             * Clear filter and return focus to input.
             */
            clearFilter() {
                this.filter = '';
                this.$refs.filter.focus();
            }
        }
    };
</script>
