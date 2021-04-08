<template>
  <div class="pb-4">
    <v-container>
      <v-row :justify="listView ? 'center' : 'start'">
        <v-col cols="12" :lg="gridView ? 12 : 5">
          <template v-if="hasPinnedNotes">
            <p class="overline" key="pinned-notes-label">PINNED</p>
          </template>

          <v-row>
            <template v-for="(note, i) in pinnedNotes">
              <v-col cols="12" :md="colMD" :lg="colLG" :xl="colXL" :key="`note-${i}`">
                <note-card
                  @click="viewNote(note)"
                  :note="note"
                  @update="updateNote"
                  @delete="deleteNote"/>
              </v-col>
            </template>
          </v-row>

          <template v-if="hasPinnedNotes && hasUnpinnedNotes">
            <p class="overline mt-5" key="unpinned-notes-label">OTHERS</p>
          </template>

          <v-row :justify="listView ? 'center' : 'start'">
            <template v-for="(note, i) in unpinnedNotes">
              <v-col cols="12" :md="colMD" :lg="colLG" :xl="colXL" :key="`note-${i}`">
                <note-card
                  @click="viewNote(note)"
                  :note="note"
                  @update="updateNote"
                  @delete="deleteNote"/>
              </v-col>
            </template>
          </v-row>
        </v-col>
      </v-row>
    </v-container>

    <!-- CREATE / EDIT NOTE -->
    <v-dialog width="580" v-model="noteDialog" persistent scrollable>
      <v-form @submit.prevent="saveNote">
        <v-card :color="selectedNoteColor" id="createNoteCard">
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
              v-model="noteTitle"
              class="font-weight-bold"
              hide-details>
            </v-textarea>
          </v-card-title>
          <v-card-text class="px-2">
            <!-- CONTENT -->
            <v-textarea
              rows="2"
              auto-grow
              flat
              solo
              no-resize
              v-model="noteContent"
              placeholder="Take a note..." />

            <v-row dense no-gutters class="px-2">
              <v-col cols="12"></v-col>
              <template v-if="!!noteUpdated">
                <v-col  cols="12" class="text-right text-small text-caption">Edited: {{ noteUpdated }}</v-col>
              </template>
            </v-row>
          </v-card-text>

          <v-card-actions>
            <!-- PINNED -->
            <v-tooltip bottom>
              <template #activator="{ on }">
                <v-btn @click="notePinned = !notePinned" v-on="on" icon>
                  <v-icon>mdi-pin{{ notePinned ? '' : '-outline'}}</v-icon>
                </v-btn>
              </template>
              <span>{{ notePinned ? 'Unpin' : 'Pin' }} Note</span>
            </v-tooltip>

            <!-- COLOR -->
            <v-menu offset-y top>
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

              <v-card class="pa-1" width="155">
                <template v-for="(color, i) in colors">
                  <v-btn @click="noteColor = color" :key="`color-col-${i}`" icon small :class="`${color} lighten-2 ma-1`" elevation="1">
                    <v-icon>
                      {{ color == noteColor ? 'mdi-check' : '' }}
                    </v-icon>
                  </v-btn>
                </template>
              </v-card>
            </v-menu>

            <!-- ARCHIVED -->
            <template v-if="!!selectedNote">
            <v-tooltip bottom>
              <template #activator="{ on }">
                <v-btn @click.stop="archiveNote" v-on="on" icon>
                  <v-icon>mdi-archive-arrow-{{ noteArchived ? 'up' : 'down' }}-outline</v-icon>
                </v-btn>
              </template>
              <span>{{ noteArchived ? 'Unarchive' : 'Archive' }}</span>
            </v-tooltip>
            </template>

            <v-spacer></v-spacer>
            <v-btn text @click="closeCreateNote">Cancel</v-btn>
            <v-btn text type="submit" :disabled="saveDisabled" :loading="notesLoading">Save</v-btn>
          </v-card-actions>
        </v-card>
      </v-form>
    </v-dialog>

    <!-- ADD NEW NOTE BUTTON -->
    <v-tooltip left>
      <template #activator="{ on }">
        <v-btn
          fab
          large
          fixed
          bottom
          right
          v-on="on"
          @click="createNote"
          class=" black--text"
          color="primary"
        >
          <v-icon>mdi-plus</v-icon>
        </v-btn>
      </template>
      <span>New Note</span>
    </v-tooltip>
  </div>
</template>

<script>
import moment from 'moment'

import NoteCard from '../components/NoteCard'

import { mapActions, mapGetters } from 'vuex'

export default {
  components: {
    NoteCard
  },
  data() {
    return {
      noteDialog: false,

      noteTitle: null,
      noteContent: null,
      noteColor: 'white',
      notePinned: false,
      noteArchived: false,
      noteUpdated: null,
      selectedNote: null,

      colors: [
        'white', 'red', 'pink', 'purple', 'indigo', 'blue', 'cyan', 'teal', 'green', 'blue-grey', 'orange', 'brown'
      ]
    }
  },

  beforeRouteEnter(to, from, next) {
    next(vm => {
      vm.getNotes()
    })
  },

  computed: {
    ...mapGetters({
      pinnedNotes: 'notes/PINNED',
      unpinnedNotes: 'notes/UNPINNED',
      notesLoading: 'notes/LOADING',
      noteSaving: 'notes/SAVING',

      viewMode: 'notes/VIEW_MODE'
    }),

    gridView() {
      return this.viewMode == 'grid'
    },

    listView() {
      return this.viewMode == 'agenda'
    },

    colXL() {
      return this.gridView ? 2 : 12
    },

    colLG() {
      return this.gridView ? 3 : 12
    },

    colMD() {
      return this.gridView ? 4 : 12
    },

    colXS() {
      return this.gridView ? 6 : 12
    },

    saveDisabled() {
      return !this.noteContent
    },

    selectedNoteColor() {
      return `${ this.noteColor } lighten-4`
    },

    hasPinnedNotes() {
      return this.pinnedNotes?.length > 0
    },

    hasUnpinnedNotes() {
      return this.unpinnedNotes?.length > 0
    },

    noteDetails() {
      return {
        title: this.noteTitle,
        contents: this.noteContent,
        color: this.noteColor,
        pinned_at: this.notePinned ? moment().format('Y-MM-DD HH:mm:ss') : null,
        archived_at: this.noteArchived ? moment().format('Y-MM-DD HH:mm:ss') : null
      }
    }
  },

  methods: {
    ...mapActions({
      _getNotes: 'notes/GET',
      _saveNote: 'notes/SAVE',
      _updateNote: 'notes/UPDATE',
      _deleteNote: 'notes/DELETE',
      setLoading: 'notes/CHANGE_LOADING_STATUS',
      setSaving: 'notes/CHANGE_SAVING_STATUS'
    }),

    clearFields() {
      this.noteTitle = null
      this.noteContent = null
      this.noteColor = 'white'
      this.notePinned = false
      this.noteArchived = false
      this.noteUpdated = null
    },
    createNote() {
      this.selectedNote = null
      this.clearFields()
      this.noteDialog = true
    },
    closeCreateNote() {
      this.selectedNote = null
      this.noteDialog = false
    },
    getNotes() {
      this.setLoading(true)

      this._getNotes()
        .then(() => {
          this.setLoading(false)
        })
    },
    viewNote(note) {
      this.selectedNote = note

      this.noteTitle = note?.title
      this.noteContent = note?.contents
      this.noteColor = note?.color
      this.notePinned = note?.pinned
      this.noteArchived = note?.archived
      this.noteUpdated = note?.updated_at

      this.noteDialog = true
    },
    saveNote() {
      this.setLoading(true)

      if (!!this.selectedNote) {
        this._updateNote({
          id: this.selectedNote.id,
          data: this.noteDetails
        })
        .then((saved) => {
          if (saved) {
            this.noteDialog = false
            this._getNotes()
          }
        })
        .then(() => {
          this.setLoading(false)
        })
      }
      else
      {
        this._saveNote(this.noteDetails)
          .then((saved) => {
            if (saved) {
              this.noteDialog = false
              this._getNotes()
            }
          })
          .then(() => {
            this.setLoading(false)
          })
      }
    },
    archiveNote() {
      if (!!this.selectedNote) {
        this.setLoading(true)

        this._updateNote({
          id: this.selectedNote.id,
          data: {
            pinned_at: null,
            archived_at: this.noteArchived ? null : moment().format('Y-MM-DD HH:mm:ss')
          }
        })
          .then((saved) => {
            if (saved) {
              this.noteDialog = false
              this._getNotes()
            }
          })
          .then(() => {
            this.setLoading(false)
          })
      }
    },
    updateNote(data) {
      this.setLoading(true)

      this._updateNote(data)
        .then((saved) => {
          if (saved) {
            this._getNotes()
          }
        })
        .then(() => {
          this.setLoading(false)
        })
    },
    deleteNote(data) {
      this.setLoading(true)

      this._deleteNote(data)
        .then((deleted) => {
          if (deleted) {
            this._getNotes()
          }
        })
        .then(() => {
          this.setLoading(false)
        })
    }
  }
}
</script>

<style>
  #createNoteCard .v-text-field--solo > .v-input__control> .v-input__slot {
    background: transparent !important;
  }
</style>
