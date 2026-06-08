@auth
    <user-bar inline-template v-cloak
        :dashboard-menu-is-visible="menuIsVisible"
        @toggle-menu="toggleMenu"
    >
        <section class="user-bar">
            
            <a class="user-bar__dashboard-link" href="{{ url('admin') }}" v-if="! atDashboard">
                Panel de administración
            </a>
            
            <div>
                <button id="user-bar-btn"
                    class="user-bar__btn"
                    :aria-expanded="dashboardMenuIsVisible ? 'true' : 'false'"
                    aria-controls="dashboard-menu"
                    @click="$emit('toggle-menu')"
                    v-if="atDashboard"
                >
                    <span class="user-bar__btn-icon">
                        <span></span>
                    </span>

                    <span class="user-bar__btn-text">Menú</span>

                    <span class="visually-hidden">
                        <template v-if="dashboardMenuIsVisible">Ocultar menú</template>
                        <template v-else>Mostrar menú</template>
                    </span>
                </button>
                <a class="user-bar__dashboard-link pl-0" href="{{ url('admin') }}" v-if="atDashboard">
                    « Ver estadística diaria                
                </a>
            </div>
            <div class="d-flex items-center">
                <notifications></notifications>

                <div class="user-bar__profile" @mouseenter="profileMenuVisible = true" @mouseleave="profileMenuVisible = false">
                    <div class="user-bar__avatar-container">
                        <img class="user-bar__avatar" src="{{ asset('archivos/avatars/' . ($authUser->image ?: 'default.png')) }}" alt="">
                    </div>

                    <p class="user-bar__user-name">
                        {{ $authUser->name }} {{ $authUser->last_name }}
                    </p>

                    <transition
                        enter-active-class="user-bar__profile-menu--show"
                        leave-active-class="user-bar__profile-menu--hide"
                    >
                        <ul class="user-bar__profile-menu" v-show="profileMenuVisible">
                            @if($authUser->role->key_name == 'artista')
                                <li class="user-bar__profile-menu-item">
                                    <a class="user-bar__profile-menu-link" href="{{ url('artista/'.$authUser->email) }}">
                                            Perfil público
                                        </a>
                                    @endif
                                </li>
                            <li class="user-bar__profile-menu-item">
                                @if($authUser->role->key_name == 'artista')
                                    <a class="user-bar__profile-menu-link" href="{{ url('editar-perfil') }}">
                                        Actualizar perfil
                                    </a>
                                @else
                                    <a class="user-bar__profile-menu-link" href="{{ url('admin/perfil/editar') }}">
                                        Actualizar perfil
                                    </a>
                                @endif
                            </li>

                            <li class="user-bar__profile-menu-item">
                                @if($authUser->role->key_name == 'artista')
                                    <a class="user-bar__profile-menu-link" href="{{ url('cambiar-contrasena') }}">
                                        Cambiar contraseña
                                    </a>
                                @else
                                    <a class="user-bar__profile-menu-link" href="{{ url('admin/perfil/cambiar-contrasena') }}">
                                        Cambiar contraseña
                                    </a>
                                @endif
                            </li>

                            <li class="user-bar__profile-menu-item">
                            

                                <a class="user-bar__profile-menu-link user-bar__profile-menu-link--logout" href="{{ route('logout') }}"
                                    onclick="event.preventDefault();
                                        document.getElementById('logout-form').submit();">
                                        <svg class="user-bar__logout-icon" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 15 15"><path fill="none" stroke-width="1.885" stroke-linecap="round" stroke-miterlimit="10" d="M3.348 4.949a5.433 5.433 0 1 0 8.296 0"/><path fill="none" stroke-width="2" stroke-linecap="round" stroke-miterlimit="10" d="M7.5 1v5"/></svg>
                                        {{ __('Cerrar sesion') }}
                                    </a>

                                    <form id="logout-form" action="{{ route('logout') }}" method="POST" class="d-none">
                                        @csrf
                                    </form>
                            </li>
                        </ul>
                    </transition>
                </div>
            </div>
                
        </section>
    </user-bar>
@endauth
