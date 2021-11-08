<template>
  <v-app>
    <v-overlay
      :value="userLoading"
      opacity="1"
      absolute
      color="white"
    >
      <v-progress-circular
        indeterminate
        color="primary"
      />
    </v-overlay>

    <router-view
      name="navigation"
      :title="pageTitle"
    />

    <!-- MAIN -->
    <v-main class="pb-16">
      <router-view />
    </v-main>
  </v-app>
</template>

<script>
import { mapActions, mapGetters } from 'vuex'

export default {
  data () {
    return {
      pageTitle: null,
      userLoading: false
    }
  },

  created () {
    const token = this.token
    this.pageTitle = this.currentPageTitle

    if (token) {
      this.authenticate(token)
    }
  },
  computed: {
    ...mapGetters({
      user: 'auth/USER',
      token: 'auth/TOKEN'
    }),
    currentPageTitle () {
      const currentRoute = this.$router.currentRoute
      return currentRoute.params.filter || currentRoute.meta.title
    }
  },
  watch: {
    $route (to, from) {
      this.pageTitle = to.params.filter || to.meta.title
    }
  },
  methods: {
    ...mapActions({
      _authenticate: 'auth/AUTHENTICATE',
      _getUserData: 'auth/GET_USER_DATA'
    }),

    authenticate (token) {
      this._authenticate(token)
      this.getUserData()
    },

    getUserData () {
      this.userLoading = true
      this._getUserData()
        .then((response) => {
          if (response) {
            this.userLoading = false
          }
        })
    },

    toggleNav () {
      this.mainNav = !this.mainNav
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

    .note-wrap {
      white-space: pre-line !important;
      word-break: break-word !important;
    }

    .note-actions {
      min-height: 52px;
    }
  }
</style>
