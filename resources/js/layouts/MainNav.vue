<template>
  <header>
    <!-- NAVIGATION DRAWER -->
    <v-navigation-drawer
      app
      clipped
      floating
      :dark="darkMode"
      v-model="mainNav"
      :permanent="$vuetify.breakpoint.mdAndUp"
      width="280"
    >
      <!-- NAV LINKS -->
      <v-list
        shaped
        dense
      >
        <template v-for="(link, i) in navLinks">
          <!-- MAKE SURE TO HAVE KEY ON FOR LOOPS -->
          <v-list-item
            link
            exact
            :key="`nav-link-${i}`"
            :active-class="`${ darkMode ? 'primary--text darken-4' : 'primary lighten-4' }`"
            :to="link.to"
          >
            <v-list-item-icon>
              <v-icon>{{ link.icon }}</v-icon>
            </v-list-item-icon>
            <v-list-item-title>
              {{ link.text }}
            </v-list-item-title>
          </v-list-item>
        </template>
      </v-list>

      <v-list
        shaped
        dense
        subheader
      >
        <v-subheader>LABELS</v-subheader>
      </v-list>
    </v-navigation-drawer>

    <!-- MAIN APP BAR -->
    <v-app-bar
      app
      color="primary"
      light
      clipped-left
      clipped-right
      elevate-on-scroll
    >
      <!-- NAVIGATION TOGGLE -->
      <template v-if="$vuetify.breakpoint.smAndDown">
        <v-app-bar-nav-icon @click="toggleNav" />
      </template>

      <v-toolbar-title class=" text-capitalize">
        {{ title }}
      </v-toolbar-title>

      <v-spacer />

      <!-- REFRESH -->
      <v-tooltip bottom>
        <template #activator="{ on }">
          <v-btn
            icon
            :loading="syncing"
            @click="refreshNotes"
            v-on="on"
          >
            <v-icon>
              mdi-refresh
            </v-icon>
          </v-btn>
        </template>
        <span>Refresh</span>
      </v-tooltip>

      <!-- DARK MODE -->
      <v-tooltip bottom>
        <template #activator="{ on }">
          <v-btn
            icon
            @click="toggleDarkMode"
            v-on="on"
            :loading="changingTheme"
          >
            <v-icon>
              mdi-{{ darkMode ? 'white-balance-sunny' : 'weather-night' }}
            </v-icon>
          </v-btn>
        </template>
        <span>{{ darkMode ? 'Disable' : 'Enable' }} dark theme</span>
      </v-tooltip>

      <template v-if="$vuetify.breakpoint.mdAndUp">
        <!-- VIEW MODE -->
        <v-tooltip bottom>
          <template #activator="{ on }">
            <v-btn
              icon
              @click="toggleView"
              v-on="on"
              :loading="changingView"
            >
              <v-icon>
                mdi-view-{{ viewMode == 'grid' ? 'agenda' : 'grid' }}-outline
              </v-icon>
            </v-btn>
          </template>
          <span>{{ viewMode == 'grid' ? 'List' : 'Grid' }} View</span>
        </v-tooltip>
      </template>

      <template v-if="authenticated">
        <v-menu
          offset-y
          transition="slide-y-transition"
        >
          <template #activator="{ on }">
            <v-btn
              icon
              v-on="on"
            >
              <v-avatar
                color="secondary lighten-1"
                class="white--text"
                size="40"
              >
                <template v-if="userAvatar.type === 'placeholder'">
                  {{ userAvatar.avatar }}
                </template>
              </v-avatar>
            </v-btn>
          </template>

          <v-card width="360">
            <v-list nav>
              <v-list-item two-line>
                <v-list-item-avatar
                  size="48"
                  color="secondary"
                  class="white--text"
                >
                  <v-avatar>
                    <template v-if="userAvatar.type === 'placeholder'">
                      {{ userAvatar.avatar }}
                    </template>
                  </v-avatar>
                </v-list-item-avatar>
                <v-list-item-content>
                  <v-list-item-title class="font-weight-medium">
                    {{ userName }}
                  </v-list-item-title>
                  <v-list-item-subtitle>{{ userEmail }}</v-list-item-subtitle>
                </v-list-item-content>
              </v-list-item>

              <v-divider class="my-2" />

              <v-list-item @click="logout">
                <v-list-item-icon>
                  <v-icon>mdi-logout-variant</v-icon>
                </v-list-item-icon>
                <v-list-item-content>
                  <v-list-item-title>Sign Out</v-list-item-title>
                </v-list-item-content>
              </v-list-item>
            </v-list>
          </v-card>
        </v-menu>
      </template>
    </v-app-bar>
  </header>
</template>

<script>
import { mapActions, mapGetters } from 'vuex'

export default {
  data () {
    return {
      mainNav: false,
      pageTitle: null,

      changingTheme: false,
      changingView: false,

      navLinks: [
        {
          to: { name: 'notes' },
          text: 'Notes',
          icon: 'mdi-note-outline'
        },
        {
          to: { name: 'notes', params: { filter: 'archive' } },
          text: 'Archive',
          icon: 'mdi-archive-outline'
        },
        {
          to: { name: 'notes', params: { filter: 'trash' } },
          text: 'Trash',
          icon: 'mdi-delete-outline'
        }
      ]
    }
  },
  props: {
    title: {
      type: String,
      default: 'Notes'
    }
  },
  computed: {
    ...mapGetters({
      user: 'auth/USER',
      userSettings: 'auth/USER_SETTINGS',
      notesLoading: 'notes/LOADING',
      notesSaving: 'notes/SAVING'
    }),
    authenticated () {
      return !!this.user
    },
    userID () {
      return this.user?.id
    },
    userName () {
      return this.user?.name
    },
    userEmail () {
      return this.user?.email
    },
    _userAvatar () {
      return this.user?.avatar
    },
    userAvatar () {
      return {
        type: this._userAvatar ? 'image' : 'placeholder',
        avatar: this._userAvatar || this.userName[0].toUpperCase()
      }
    },
    darkMode () {
      return this.userSettings?.dark
    },
    viewMode () {
      return this.userSettings?.view
    },
    syncing () {
      return this.notesLoading
    },
    currentPageTitle () {
      const currentRoute = this.$router.currentRoute
      return currentRoute.params.filter || currentRoute.meta.title
    }
  },
  watch: {
    user (val) {
      if (val) {
        this.$nextTick(() => {
          this.$vuetify.theme.dark = val?.settings?.dark
        })
      }
    }
  },

  methods: {
    ...mapActions({
      _logout: 'auth/LOGOUT',
      _getUserData: 'auth/GET_USER_DATA',
      _updateSettings: 'settings/UPDATE',
      _getNotes: 'notes/GET'
    }),
    toggleNav () {
      this.mainNav = !this.mainNav
    },
    toggleView () {
      this.changingView = true
      const newView = this.viewMode === 'grid' ? 'agenda' : 'grid'

      this._updateSettings({
        dark: this.darkMode,
        view: newView
      })
        .then((response) => {
          if (response.saved) {
            this._getUserData()
              .then(() => {
                this.changingView = false
              })
          }
        })
    },
    toggleDarkMode () {
      this.changingTheme = true
      const newTheme = !this.darkMode

      this._updateSettings({
        dark: newTheme,
        view: this.viewMode
      })
        .then((response) => {
          if (response.saved) {
            this._getUserData()
              .then(() => {
                this.changingTheme = false
                this.$vuetify.theme.dark = newTheme
              })
          }
        })
    },
    refreshNotes () {
      this._getNotes({
        filter: this.$router.currentRoute.params.filter
      })
    },

    logout () {
      this._logout()
      this.$router.go({
        name: 'auth.login'
      })
    }
  }
}
</script>
