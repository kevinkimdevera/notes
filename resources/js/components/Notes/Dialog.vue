<template>
  <v-dialog
    width="580"
    :value="show"
    persistent
    scrollable
  >
    <v-form
      @submit.prevent="save"
      class="note-form"
    >
      <v-card
        :color="selectedColor"
        id="createNoteCard"
      >
        <v-card-title class="px-2">
          <!-- TITLE -->
          <v-textarea
            dense
            solo
            flat
            auto-grow
            no-resize
            rows="1"
            placeholder="Title"
            v-model="title"
            :readonly="noteTrashed"
            class="font-weight-bold note-input"
            hide-details
          />
        </v-card-title>

        <v-card-text class="px-2">
          <!-- CONTENT -->
          <v-textarea
            class="note-input"
            rows="2"
            auto-grow
            flat
            solo
            no-resize
            :readonly="noteTrashed"
            v-model="content"
            placeholder="Take a note..."
          />

          <v-row
            dense
            no-gutters
            class="px-2"
          >
            <v-col cols="12" />

            <template v-if="!!updated">
              <v-col
                cols="12"
                class="text-right text-small text-caption"
              >
                <span v-if="noteArchived">
                  Note in Archive&nbsp;&middot;&nbsp;
                </span>
                <span v-if="noteTrashed">
                  Note in Trash&nbsp;&middot;&nbsp;
                </span>
                Edited: {{ updated }}
              </v-col>
            </template>
          </v-row>
        </v-card-text>

        <v-card-actions>
          <!-- IF SELECTED NOTE IS TRASHED -->
          <template v-if="noteTrashed">
            <!-- DELETE FOREVER -->
            <v-tooltip bottom>
              <template #activator="{ on }">
                <v-btn
                  v-on="on"
                  icon
                  @click.stop="deleteForever"
                >
                  <v-icon>mdi-delete-forever</v-icon>
                </v-btn>
              </template>
              <span>Delete Forever</span>
            </v-tooltip>

            <!-- RESTORE -->
            <v-tooltip bottom>
              <template #activator="{ on }">
                <v-btn
                  v-on="on"
                  icon
                  @click.stop="restore"
                >
                  <v-icon>mdi-delete-restore</v-icon>
                </v-btn>
              </template>
              <span>Restore</span>
            </v-tooltip>
          </template>

          <template v-else>
            <!-- TOGGLE PIN -->
            <v-tooltip bottom>
              <template #activator="{ on }">
                <v-btn
                  @click="togglePin"
                  v-on="on"
                  icon
                >
                  <v-icon>mdi-pin{{ pinned ? '' : '-outline' }}</v-icon>
                </v-btn>
              </template>
              <span>{{ pinned ? 'Unpin' : 'Pin' }} Note</span>
            </v-tooltip>

            <!-- COLOR -->
            <v-menu
              offset-y
              top
            >
              <template #activator="{ on: menu }">
                <v-tooltip bottom>
                  <template #activator="{ on: tooltip }">
                    <v-btn
                      icon
                      v-on="{ ...menu, ...tooltip }"
                    >
                      <v-icon>mdi-palette-outline</v-icon>
                    </v-btn>
                  </template>

                  <span>Change Color</span>
                </v-tooltip>
              </template>

              <v-card
                class="pa-1"
                width="117"
              >
                <template v-for="(_color, i) in colors">
                  <v-btn
                    @click="color = _color"
                    :key="`color-col-${i}`"
                    icon
                    small
                    :class="`${_color} ${ darkMode ? 'darken-4' : 'lighten-4' } ma-1 btn-color`"
                    elevation="1"
                  >
                    <v-icon>
                      {{ color === _color ? 'mdi-check' : '' }}
                    </v-icon>
                  </v-btn>
                </template>
              </v-card>
            </v-menu>

            <!-- TOGGLE ARCHIVE -->
            <template v-if="viewingNote">
              <v-tooltip bottom>
                <template #activator="{ on }">
                  <v-btn
                    @click.stop="toggleArchive"
                    v-on="on"
                    icon
                  >
                    <v-icon>mdi-archive-arrow-{{ archived ? 'up' : 'down' }}-outline</v-icon>
                  </v-btn>
                </template>
                <span>{{ archived ? 'Unarchive' : 'Archive' }}</span>
              </v-tooltip>
            </template>
          </template>

          <v-spacer />
          <v-btn
            text
            type="button"
            @click.stop="close(false)"
          >
            Close
          </v-btn>
          <v-btn
            text
            type="submit"
            :disabled="saveDisabled || noteTrashed"
            :loading="noteSaving"
          >
            Save
          </v-btn>
        </v-card-actions>
      </v-card>
    </v-form>
  </v-dialog>
</template>

<script>
import moment from 'moment'
import { mapActions, mapGetters } from 'vuex'

export default {
  props: {
    show: {
      type: Boolean
    },
    note: {
      type: Object
    }
  },

  data () {
    return {
      title: null,
      content: null,
      color: 'default',
      pinned: false,
      archived: false,
      updated: null,

      colors: [
        'default', 'pink', 'indigo', 'light-blue', 'teal', 'amber'
      ]
    }
  },

  watch: {
    show (shown) {
      if (shown) {
        this.clearFields()
      }
    }
  },

  computed: {
    ...mapGetters({
      userSettings: 'auth/USER_SETTINGS',
      notesLoading: 'notes/LOADING',
      noteSaving: 'notes/SAVING'
    }),
    darkMode () {
      return this.userSettings?.dark
    },
    viewingNote () {
      return !!this.note
    },
    noteTrashed () {
      return this.note?.trashed
    },
    noteArchived () {
      return this.note?.archived
    },
    noteColor () {
      return this.note?.color
    },
    selectedColor () {
      const color = this.color === 'default' ? null : this.color
      const theme = this.darkMode ? 'darken-4' : 'lighten-4'

      return `${color} ${theme}`
    },
    saveDisabled () {
      return !this.content
    },
    noteDetails () {
      return {
        id: this.note?.id,
        title: this.title,
        contents: this.content,
        color: this.color,
        pinned_at: this.pinned ? moment().format('Y-MM-DD HH:mm:ss') : null
      }
    }
  },

  methods: {
    ...mapActions({
      _saveNote: 'notes/SAVE',
      _updateNote: 'notes/UPDATE',
      _deleteNote: 'notes/DELETE',
      setLoading: 'notes/CHANGE_LOADING_STATUS',
      setSaving: 'notes/CHANGE_SAVING_STATUS'
    }),

    clearFields () {
      this.title = this.note?.title
      this.content = this.note?.contents
      this.color = this.note?.color || 'default'
      this.pinned = this.note?.pinned
      this.archived = this.note?.archived
      this.updated = this.note?.updated_at
    },

    close (saved) {
      this.$emit('close', (!!this.note || saved))
    },

    save () {
      this._saveNote(this.noteDetails)
        .then((saved) => {
          this.close(saved)
          this.$emit('snackbar', this.note ? 'Note updated' : 'Note Saved')
        })
    },

    togglePin () {
      if (this.note) {
        this.$emit('update', {
          id: this.note?.id,
          data: {
            pinned_at: this.pinned ? null : moment().format('Y-MM-DD HH:mm:ss')
          }
        })
      }

      this.pinned = !this.pinned
    },

    toggleArchive () {
      if (this.note) {
        this.$emit('archive', {
          status: !this.archived,
          note: {
            id: this.note.id,
            data: {
              pinned_at: null,
              archived_at: this.archived ? null : moment().format('Y-MM-DD HH:mm:ss')
            }
          }
        })
      }
    },

    deleteForever () {
      this.$emit('delete-forever', this.note)
    },

    restore () {
      this.$emit('restore', {
        id: this.note?.id
      })
    }
  }
}
</script>

<style>
  .v-textarea {
    word-break: keep-all;
  }

  #createNoteCard .v-text-field--solo > .v-input__control> .v-input__slot {
    background: transparent !important;
  }
</style>
