import '../boot.js';
import BaseForm from './components/forms/base/BaseForm.vue';
import ContactForm from './components/forms/ContactForm.vue';
import LoginForm from './components/forms/LoginForm.vue';
import Header from './components/Header.vue';
import Introduction from './components/introduction.vue';
import Menu from './components/Menu.vue';
import SiteOverlay from './components/SiteOverlay.vue';
import GalleryCard from './components/GalleryCard.vue';
import GalleryLink from './components/GalleryLink.vue';
import UserBar from '../admin/components/UserBar.vue';
import ProductRegistrationForm from './components/forms/product-registration-form/ProductRegistrationForm.vue';
import ProductRegistrationForms from './components/forms/product-registration-forms/ProductRegistrationForms.vue';
import StatusProjectForm from './components/forms/status-project-form/StatusProjectForm.vue';
import ButtonSearch from './components/ButtonSearch.vue';
import TruncatedText from './components/TruncatedText.vue';

import Tabs from './components/tabs/Tabs.vue';

(function() {
    /* Base components
    ------------------------------------------------------------------------- */
    Vue.component('base-form', BaseForm);
    Vue.component('contact-form', ContactForm);
    Vue.component('login-form', LoginForm);
    Vue.component('product-registration-form',ProductRegistrationForm);
    Vue.component('product-registration-forms',ProductRegistrationForms);
    Vue.component('status-project-form', StatusProjectForm);


    /* App components
    ------------------------------------------------------------------------- */
    Vue.component('site-menu', Menu);
    Vue.component('site-header', Header);
    Vue.component('presentation', Introduction);
    Vue.component('site-overlay', SiteOverlay);
    Vue.component('gallery-card', GalleryCard);
    Vue.component('gallery-link', GalleryLink);

    Vue.component('button-search', ButtonSearch);
    Vue.component('truncated-text', TruncatedText);

    Vue.component('tabs-component', Tabs);

    /**
     * Vue instance
     */
    const app = new Vue({
        el: '#app',
        components: { UserBar },
        data: {
            isLoading: true,
            path: document.body.getAttribute('data-root') || '',
            menuIsVisible: false,
            mq: false,
            resourceCount: 0,
            model: {}
        },
        mounted() {
            this.mq = window.matchMedia('(min-width:1100px)');
            this.menuIsVisible = this.mq.matches;

            this.mq.addListener(e => this.menuIsVisible = e.matches);

            Vue.nextTick(() => this.isLoading = false);
        },

        methods: {
            /**
             * Show or hide dashboard menu.
             */
            toggleMenu() {
                this.menuIsVisible = ! this.menuIsVisible;
            }
        }
    });



})();
