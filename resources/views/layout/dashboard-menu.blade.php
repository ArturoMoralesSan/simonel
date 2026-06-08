<dashboard-menu v-cloak
    :is-loading="isLoading"
    :menu-is-visible="menuIsVisible"
    :menu="{{ $dbMenu }}"
    @toggle-menu="toggleMenu"
>
</dashboard-menu>