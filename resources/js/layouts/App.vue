<template>
  <v-app>
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
      <v-list shaped dense>
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

      <v-list shaped dense subheader>
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
        <v-app-bar-nav-icon @click="toggleNav"></v-app-bar-nav-icon>
      </template>

      <v-toolbar-title class=" text-capitalize">
        {{ pageTitle }}
      </v-toolbar-title>

      <v-spacer></v-spacer>

      <!-- REFRESH -->
      <v-tooltip bottom>
        <template #activator="{ on }">
          <v-btn icon :loading="syncing" @click="refreshNotes" v-on="on">
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
          <v-btn icon @click="toggleDarkMode" v-on="on">
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
            <v-btn icon @click="toggleView" v-on="on">
              <v-icon>
                mdi-view-{{ viewMode == 'grid' ? 'agenda' : 'grid' }}-outline
              </v-icon>
            </v-btn>
          </template>
          <span>{{ viewMode == 'grid' ? 'List' : 'Grid' }} View</span>
        </v-tooltip>
      </template>
    </v-app-bar>

    <!-- MAIN -->
    <v-main class="pb-16">
      <router-view></router-view>
    </v-main>
  </v-app>
</template>

<script>
import { mapActions, mapGetters } from 'vuex'
export default {
  data () {
    return {
      mainNav: false,
      pageTitle: null,

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
  created () {
    this.getSettings()
  },
  beforeRouteEnter (to, from, next) {
    next(vm => {
      vm.getSettings()
    })
  },
  computed: {
    ...mapGetters({
      notesLoading: 'notes/LOADING',
      notesSaving: 'notes/SAVING',
      viewMode: 'settings/VIEW_MODE',
      theme: 'settings/THEME'
    }),
    syncing () {
      return this.notesLoading
    },
    currentPageTitle () {
      const currentRoute = this.$router.currentRoute
      return currentRoute.params.filter || currentRoute.meta.title
    },
    darkMode () {
      return this.theme === 'dark'
    }
  },
  watch: {
    $route (to, from) {
      this.pageTitle = to.params.filter || to.meta.title
    }
  },
  methods: {
    ...mapActions({
      _getSettings: 'settings/GET',
      changeView: 'settings/CHANGE_VIEW_MODE',
      changeTheme: 'settings/CHANGE_THEME',
      _getNotes: 'notes/GET'
    }),
    toggleNav () {
      this.mainNav = !this.mainNav
    },
    toggleView () {
      const newView = this.viewMode === 'grid' ? 'agenda' : 'grid'
      this.changeView(newView)
    },
    toggleDarkMode () {
      const newTheme = this.darkMode ? 'light' : 'dark'
      this.$vuetify.theme.dark = !this.darkMode
      this.changeTheme(newTheme)
    },
    refreshNotes () {
      this._getNotes({
        filter: this.$router.currentRoute.params.filter
      })
    },
    getSettings () {
      this._getSettings()
        .then(() => {
          this.$vuetify.theme.dark = this.darkMode
          this.pageTitle = this.currentPageTitle
        })
    }
  }
}
</script>

<style lang="scss">
  $body-font-family: "Poppins",sans-serif;

  .v-application {
    font-family: $body-font-family !important;

    .title,
    .subtitle-1,
    .subtitle-2,
    .headline,
    [class*="text-"]{
      font-family: $body-font-family !important;
    }

    .note-form {
      .note-input {
        &.theme--light {
          caret-color: black !important;
        }
      }
    }
  }

  .btn-color {
    border: 1px solid;
  }

  .note-card {
    transition-property: all;
    transition-duration: 125ms;
    transform-style: flat;
    transition-timing-function: ease-in-out;

    .note-select {
      left: -16px;
    }

    .note-title {
      white-space: break-spaces;
    }

    .note-text {
      white-space: pre-line;
    }

    .note-actions {
      min-height: 52px;
    }
  }
</style>
