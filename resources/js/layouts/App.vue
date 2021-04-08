<template>
  <v-app>
    <!-- NAVIGATION DRAWER -->
    <v-navigation-drawer
      app
      clipped
      floating
      v-model="mainNav"
      :permanent="$vuetify.breakpoint.mdAndUp"
      width="280"
    >
      <!-- NAV LINKS -->
      <v-list shaped dense>
        <template v-for="(link, i) in navLinks">
          <!-- MAKE SURE TO HAVE KEY ON FOR LOOPS -->
          <v-list-item
            exact
            :key="`nav-link-${i}`"
            :to="{ name: link.to }"
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

    </v-navigation-drawer>

    <!-- MAIN APP BAR -->
    <v-app-bar
      app
      color="primary"
      clipped-left
      clipped-right
    >
      <!-- NAVIGATION TOGGLE -->
      <template v-if="$vuetify.breakpoint.smAndDown">
        <v-app-bar-nav-icon @click="toggleNav"></v-app-bar-nav-icon>
      </template>

      <v-toolbar-title>
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
  data() {
    return{
      mainNav: false,
      pageTitle: null,

      navLinks: [
        {
          to: 'notes.index',
          text: 'My Notes',
          icon: 'mdi-note-outline'
        },
        {
          to: 'notes.archive',
          text: 'Archive',
          icon: 'mdi-archive-outline'
        },
        {
          to: 'notes.trash',
          text: 'Trash',
          icon: 'mdi-delete-outline'
        }
      ]
    }
  },
  created() {
    this.pageTitle = this.currentPageTitle
  },
  computed: {
    ...mapGetters({
      notesLoading: 'notes/LOADING',
      notesSaving: 'notes/SAVING',
      viewMode: 'notes/VIEW_MODE'
    }),
    syncing() {
      return this.notesLoading || this.notesSaving
    },
    currentPageTitle() {
      return this.$router.currentRoute.meta.title
    },
    navColor() {
      return this.$router.currentRoute.meta.color
    }
  },
  watch: {
    $route(to, from) {
      this.pageTitle = to.meta.title
    }
  },
  methods: {
    ...mapActions({
      login: 'notes/LOGIN',
      setLoading: 'notes/CHANGE_LOADING_STATUS',
      changeView: 'notes/CHANGE_VIEW_MODE',
      _getNotes: 'notes/GET'
    }),
    toggleNav() {
      this.mainNav = !this.mainNav
    },
    toggleView() {
      var newView = this.viewMode == 'grid' ? 'agenda' : 'grid'
      this.changeView(newView)
    },
    refreshNotes() {
      this.setLoading(true)

      this._getNotes()
        .then(() => {
          this.setLoading(false)
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
  }
</style>
