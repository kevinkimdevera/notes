<template>
  <v-hover v-slot="{ hover }">
    <v-card
      class="note-card flex d-flex flex-column"
      min-height="200"
      :color="cardColor"
      :elevation="hover ? 3 : 0"
      outlined
      rounded
      :ripple="false"
      @click="showNote"
    >
      <template v-if="hasTitle">
        <v-card-title class="flex flex-grow-0 note-title text-subtitle-2 font-weight-bold pb-2">{{ noteTitle }}</v-card-title>
      </template>

      <v-card-text class="flex flex-grow-1 note-text overflow-hidden">{{ noteContent }}</v-card-text>

      <v-card-actions class="note-actions d-flex align-baseline">
        <template v-if="hover || $vuetify.breakpoint.smAndDown">

          <template v-if="!noteTrashed">

            <template v-if="!noteArchived">
              <!-- PIN/UNPIN NOTE -->
              <v-tooltip bottom>
                <template #activator="{ on }">
                  <v-btn @click.stop="togglePin" v-on="on" icon>
                    <v-icon>mdi-pin{{ notePinned ? '' : '-outline'}}</v-icon>
                  </v-btn>
                </template>
                <span>{{ notePinned ? 'Unpin' : 'Pin' }} Note</span>
              </v-tooltip>
            </template>

            <!-- CHANGE COLOR -->
            <v-menu offset-y top :attach="true">
              <template #activator="{ on: menu }">
                <v-tooltip bottom>
                  <template #activator="{ on: tooltip }">
                    <v-btn icon v-on="{ ...menu, ...tooltip }">
                      <v-icon>mdi-palette-outline</v-icon>
                    </v-btn>
                  </template>

                  <span>Change Color</span>
                </v-tooltip>
              </template>

              <v-card class="pa-1" width="117">
                <template v-for="(color, i) in colors">
                  <v-btn @click.stop="changeColor(color)" :key="`color-col-${i}`" icon small :class="`${color} ${ darkMode ? 'darken-4' : 'lighten-4' } ma-1 btn-color`" elevation="1">
                    <v-icon>
                      {{ color == noteColor ? 'mdi-check' : '' }}
                    </v-icon>
                  </v-btn>
                </template>
              </v-card>
            </v-menu>

            <!-- ARCHIVE -->
            <v-tooltip bottom>
              <template #activator="{ on }">
                <v-btn @click.stop="toggleArchive" v-on="on" icon>
                  <v-icon>mdi-archive-arrow-{{ noteArchived ? 'up' : 'down' }}-outline</v-icon>
                </v-btn>
              </template>
              <span>{{ noteArchived ? 'Unarchive' : 'Archive' }}</span>
            </v-tooltip>

            <v-spacer></v-spacer>

            <!-- DELETE NOTE -->
            <v-tooltip bottom>
              <template #activator="{ on }">
                <v-btn @click.stop="deleteNote" v-on="on" icon>
                  <v-icon>mdi-delete-outline</v-icon>
                </v-btn>
              </template>
              <span>Delete Note</span>
            </v-tooltip>
          </template>

          <template v-else>
            <!-- DELETE FOREVER -->
            <v-tooltip bottom>
              <template #activator="{ on }">
                <v-btn v-on="on" icon @click.stop="deleteForever">
                  <v-icon>mdi-delete-forever</v-icon>
                </v-btn>
              </template>
              <span>Delete Forever</span>
            </v-tooltip>

            <!-- RESTORE -->
            <v-tooltip bottom>
              <template #activator="{ on }">
                <v-btn v-on="on" icon @click.stop="restore">
                  <v-icon>mdi-delete-restore</v-icon>
                </v-btn>
              </template>
              <span>Restore</span>
            </v-tooltip>

          </template>
        </template>
      </v-card-actions>
    </v-card>
  </v-hover>
</template>

<script>
import moment from 'moment'
import { mapGetters } from 'vuex'

export default {
  props: {
    selected: {
      type: Boolean
    },
    note: {
      type: Object
    }
  },

  data () {
    return {
      colors: [
        'default', 'pink', 'indigo', 'light-blue', 'teal', 'orange'
      ]
    }
  },

  computed: {
    ...mapGetters({
      viewMode: 'settings/VIEW_MODE',
      theme: 'settings/THEME'
    }),
    // Check App Theme
    darkMode () {
      return this.theme === 'dark'
    },

    // Note ID
    noteId () {
      return this.note?.id
    },

    // Note is pinned
    notePinned () {
      return this.note?.pinned
    },

    // Note is archived
    noteArchived () {
      return this.note?.archived
    },

    // Note is trashed
    noteTrashed () {
      return this.note?.trashed
    },

    // Note Color
    noteColor () {
      return this.note?.color
    },

    // Color applied to the card
    cardColor () {
      const color = this.noteColor === 'default' ? null : this.noteColor
      const theme = this.darkMode ? 'darken-4' : 'lighten-4'

      return `${color} ${theme}`
    },

    // Check if note has saved title
    hasTitle () {
      return !!this.note?.title
    },

    // Note Card Title
    noteTitle () {
      return this.note?.title
    },

    // Note Card Content
    noteContent () {
      const limit = ((this.viewMode === 'grid') && (this.$vuetify.breakpoint.mdAndUp)) ? '70' : '150'

      return (this.note?.contents?.length > limit)
        ? this.note?.contents?.substring(0, limit) + '...'
        : this.note?.contents
    }
  },

  methods: {
    // Show Note Dialog
    showNote () {
      this.$emit('click', this.note)
    },

    // Change Note Color
    changeColor (color) {
      this.$emit('update', {
        id: this.noteId,
        data: {
          color: color
        }
      })
    },

    // Toggle pin status of note
    togglePin () {
      this.$emit('update', {
        id: this.noteId,
        data: {
          pinned_at: this.notePinned ? null : moment().format('Y-MM-DD HH:mm:ss')
        }
      })
    },

    // Toggle archive status of Note
    toggleArchive () {
      this.$emit('archive', {
        status: !this.noteArchived,
        note: {
          id: this.noteId,
          data: {
            pinned_at: null,
            archived_at: this.noteArchived ? null : moment().format('Y-MM-DD HH:mm:ss')
          }
        }
      })
    },

    // Trash Note (Soft Delete)
    deleteNote () {
      this.$emit('delete', {
        id: this.noteId
      })
    },

    // Permanently Remove Note
    deleteForever () {
      this.$emit('delete-forever', {
        id: this.noteId
      })
    },

    // Restore note if Trashed
    restore () {
      this.$emit('restore', {
        id: this.noteId
      })
    }
  }
}
</script>
