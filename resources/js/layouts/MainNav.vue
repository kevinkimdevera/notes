<template>
  <header>
    <!-- NAVIGATION DRAWER -->
    <v-navigation-drawer
      app
      clipped
      :dark="darkMode"
      permanent
      mini-variant
      :disable-resize-watcher="true"
    >
      <!-- NAV LINKS -->
      <v-list
        dense
        nav
        flat
      >
        <template v-for="(link, i) in navLinks">
          <v-tooltip
            right
            :key="`nav-link-${i}`"
          >
            <template #activator="{ on }">
              <v-list-item
                link
                exact
                v-on="on"
                :active-class="`${ darkMode ? 'primary--text' : 'primary lighten-4' }`"
                :to="link.to"
              >
                <v-list-item-icon>
                  <v-icon>{{ link.icon }}</v-icon>
                </v-list-item-icon>
                <v-list-item-title>{{ link.text }}</v-list-item-title>
              </v-list-item>
            </template>
            <span>{{ link.text }}</span>
          </v-tooltip>
        </template>
      </v-list>
      <template #append>
        <v-list
          dense
          nav
        >
          <!-- TOGGLE DARK MODE -->
          <v-tooltip right>
            <template #activator="{ on }">
              <v-list-item
                @click.stop="toggleDarkMode"
                link
                v-on="on"
              >
                <v-list-item-action-text>
                  <template v-if="changingTheme">
                    <v-progress-circular
                      indeterminate
                      color="primary"
                      size="24" />
                  </template>
                  <template v-else>
                    <v-icon>mdi-{{ darkMode ? 'white-balance-sunny' : 'weather-night' }}</v-icon>
                  </template>
                </v-list-item-action-text>
              </v-list-item>
            </template>
            <span>{{ darkMode ? 'Disable' : 'Enable' }} dark theme</span>
          </v-tooltip>
        </v-list>
      </template>
    </v-navigation-drawer>

    <!-- MAIN APP BAR -->
    <v-app-bar
      app
      :dark="darkMode"
      clipped-left
      elevation="0"
      class="v-bar__underlined"
    >
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
          localStorage.setItem('settings.dark', val?.settings?.dark)
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

<style>
  .theme--light.v-bar__underlined {
    border-bottom: thin solid rgba(0,0,0,.12) !important;
  }

  .theme--dark.v-bar__underlined {
    border-bottom: thin solid hsla(0,0%,100%,.12) !important;
  }
</style>
