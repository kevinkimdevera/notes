<template>
  <div class="pb-4">
    <v-container>
      <v-row :justify="listView ? 'center' : 'start'">
        <v-col cols="12" :xl="gridView ? 12 : 5" :lg="gridView ? 12 : 7" mode="in-out">
          <!-- PINNED NOTES -->
          <v-row>
            <template v-if="hasPinnedNotes">
              <p class="overline my-0 col-12" key="pinned-notes-label">PINNED</p>
            </template>

            <template v-for="(note, i) in pinnedNotes">
              <v-col
                cols="12" :sm="colSM" :md="colMD" :lg="colLG" :xl="colXL" :key="`note-${i}`"
                class="d-flex flex-column">
                <note-card
                  :note="note"
                  @click="viewNote(note)"
                  @archive="toggleArchive"
                  @update="updateNote"
                  @delete="deleteNote"
                  @delete-forever="confirmDelete"
                  @restore="restoreNote"/>
              </v-col>
            </template>
          </v-row>

          <!-- UNPINNED NOTES -->
          <v-row>
            <template v-if="hasPinnedNotes && hasUnpinnedNotes">
              <p class="overline my-0 col-12" key="unpinned-notes-label">OTHERS</p>
            </template>

            <template v-for="(note, i) in unpinnedNotes">
              <v-col
                cols="12" :sm="colSM"  :md="colMD" :lg="colLG" :xl="colXL"
                :key="`note-${i}`"
                class="d-flex flex-column"
              >
                <v-fade-transition hide-on-leave>
                  <note-card
                    :note="note"
                    @click="viewNote(note)"
                    @archive="toggleArchive"
                    @update="updateNote"
                    @delete="deleteNote"
                    @delete-forever="confirmDelete"
                    @restore="restoreNote"/>
                </v-fade-transition>
              </v-col>
            </template>
          </v-row>
        </v-col>
      </v-row>
    </v-container>

    <!-- CREATE / EDIT NOTE -->
    <note-dialog
      :show="noteDialog"
      :note="selectedNote"
      @update="updateNote"
      @archive="toggleArchive"
      @close="closeCreateNote"
      @snackbar="showSnackbar"
      @delete-forever="confirmDelete"
      @restore="restoreNote" />

    <!-- CONFIRM DELETE NOTE -->
    <v-dialog width="290" v-model="confirmDeleteDialog">
      <v-card>
        <v-card-title class=" text-subtitle-1">Delete note forever?</v-card-title>
        <v-card-text>
          This action cannot be undone.
        </v-card-text>
        <v-card-actions>
          <v-spacer></v-spacer>
          <v-btn text @click="closeDelete">Cancel</v-btn>
          <v-btn color="red" text @click="deleteNoteForever" :loading="noteSaving">Delete</v-btn>
        </v-card-actions>
      </v-card>
    </v-dialog>

    <!-- CONFIRM EMPTY TRASH -->
    <v-dialog width="290" v-model="confirmEmptyTrashDialog">
      <v-card>
        <v-card-title class=" text-subtitle-1">Empty Trash?</v-card-title>
        <v-card-text>
          All notes in Trash will be permanently deleted.
        </v-card-text>
        <v-card-actions>
          <v-spacer></v-spacer>
          <v-btn text @click="confirmEmptyTrashDialog = false">Cancel</v-btn>
          <v-btn color="red" text @click="emptyTrash" :loading="noteSaving">Empty Trash</v-btn>
        </v-card-actions>
      </v-card>
    </v-dialog>

    <v-fab-transition group hide-on-leave>

      <template v-if="filter == 'trash'">
        <!-- EMPTY TRASH -->
        <template v-if="hasTrash">
          <v-btn
            @click="confirmEmptyTrashDialog = true"
            key="trash-fab"
            fab
            large
            fixed
            bottom
            right
            dark
            color="red"
          >
            <v-icon>mdi-delete-empty-outline</v-icon>
          </v-btn>
        </template>
      </template>

      <template v-if="!filter">
        <!-- ADD NEW NOTE BUTTON -->
        <v-btn
          key="new-note-fab"
          fab
          large
          fixed
          bottom
          right
          @click="createNote"
          class=" black--text"
          color="primary"
        >
          <v-icon>mdi-plus</v-icon>
        </v-btn>
      </template>

    </v-fab-transition>

    <!-- SNACKBAR -->
    <v-snackbar v-model="snackbar" app>
      {{ snackbarMessage }}

      <template #action="{ attrs }">
        <v-btn
          icon
          v-bind="attrs"
          @click="snackbar = false"
        >
          <v-icon>mdi-close</v-icon>
        </v-btn>
      </template>
    </v-snackbar>
  </div>
</template>

<script>
import moment from 'moment'

import NoteCard from '../components/Notes/Card'
import NoteDialog from '../components/Notes/Dialog'

import { mapActions, mapGetters } from 'vuex'

export default {
  props: {
    filter: {
      type: String,
      default: null
    }
  },

  components: {
    NoteCard,
    NoteDialog
  },

  data () {
    return {
      noteDialog: false,
      confirmDeleteDialog: false,
      confirmEmptyTrashDialog: false,
      selectedNote: null,
      snackbar: false,
      snackbarMessage: null,
      colors: [
        'default', 'red', 'pink', 'purple', 'indigo', 'light-blue', 'cyan', 'teal', 'green', 'blue-grey', 'orange', 'brown'
      ]
    }
  },

  beforeRouteEnter (to, from, next) {
    next(vm => {
      vm.getNotes(to.params.filter)
    })
  },

  beforeRouteUpdate (to, from, next) {
    this.clearNotes()
    this.getNotes(to.params.filter)
    next()
  },

  computed: {
    ...mapGetters({
      notes: 'notes/NOTES',
      notesLoading: 'notes/LOADING',
      noteSaving: 'notes/SAVING',

      viewMode: 'settings/VIEW_MODE'
    }),

    pinnedNotes () {
      return this.notes?.filter(n => n.pinned)
    },

    unpinnedNotes () {
      return this.notes?.filter(n => !n.pinned)
    },

    gridView () {
      return this.viewMode === 'grid'
    },

    listView () {
      return this.viewMode === 'agenda'
    },

    colXL () {
      return this.gridView ? 2 : 12
    },

    colLG () {
      return this.gridView ? 3 : 12
    },

    colMD () {
      return this.gridView ? 6 : 12
    },

    colSM () {
      return this.gridView ? 6 : 12
    },

    hasTrash () {
      return this.filter === 'trash' && this.notes?.length > 0
    },

    hasPinnedNotes () {
      return this.pinnedNotes?.length > 0
    },

    hasUnpinnedNotes () {
      return this.unpinnedNotes?.length > 0
    }
  },

  methods: {
    ...mapActions({
      _getSettings: 'settings/GET',
      clearNotes: 'notes/CLEAR_NOTES',
      _getNotes: 'notes/GET',
      _updateNote: 'notes/UPDATE',
      _deleteNote: 'notes/DELETE',
      _restoreNote: 'notes/RESTORE',
      _deleteForever: 'notes/DELETE_FOREVER',
      _emptyTrash: 'notes/EMPTY_TRASH',
      setLoading: 'notes/CHANGE_LOADING_STATUS',
      setSaving: 'notes/CHANGE_SAVING_STATUS'
    }),

    // SHOW SNACKBAR
    showSnackbar (message) {
      this.snackbarMessage = message
      this.snackbar = true
    },

    // SHOW CREATE NOTE
    createNote () {
      this.selectedNote = null
      this.noteDialog = true
    },

    // CLOSE CREATE NOTE DIALOG
    closeCreateNote (saved) {
      this.noteDialog = false

      if (saved) {
        this.reloadNotes()
      }

      this.selectedNote = null
    },

    // SHOW CONFIRMATION WHEN DELETING NOTE FOREVER
    confirmDelete (note) {
      this.selectedNote = note
      this.confirmDeleteDialog = true
    },

    // CLOSE DELETE FOREVER CONFIRMATION
    closeDelete () {
      this.confirmDeleteDialog = false
    },

    reloadNotes () {
      this._getNotes({
        filter: this.$router.currentRoute.params?.filter
      })
    },

    getNotes (filter) {
      this._getNotes({
        filter: filter
      })
    },

    viewNote (note) {
      this.selectedNote = note
      this.noteDialog = true
    },

    // ARCHIVE NOTE
    archiveNote () {
      if (this.selectedNote) {
        this._updateNote({
          id: this.selectedNote.id,
          data: {
            pinned_at: null,
            archived_at: this.noteArchived ? null : moment().format('Y-MM-DD HH:mm:ss')
          }
        }).then((saved) => {
          if (saved) {
            this.showSnackbar('Note archived.')
            this.noteDialog = false
            this.reloadNotes()
          }
        })
      }
    },

    // TOGGLE ARCHIVE
    toggleArchive (data) {
      const archive = data.status

      this._updateNote(data.note)
        .then((saved) => {
          if (saved) {
            if (this.noteDialog) {
              this.closeCreateNote(saved)
            }

            if (archive) {
              this.showSnackbar('Note archived.')
            } else {
              this.showSnackbar('Note unarchived.')
            }

            this.reloadNotes()
          }
        })
    },

    // UPDATE NOTE
    updateNote (data) {
      this._updateNote(data)
        .then((saved) => {
          if (saved) {
            if (!this.noteDialog) {
              this.reloadNotes()
            }
          }
        })
    },

    // DELETE NOTE
    deleteNote (data) {
      this._deleteNote(data)
        .then((deleted) => {
          if (deleted) {
            this.noteDialog = false
            this.showSnackbar('Note trashed.')
            this.reloadNotes()
          }
        })
    },

    // RESTORE NOTE
    restoreNote (data) {
      this._restoreNote(data)
        .then((restored) => {
          if (restored) {
            this.noteDialog = false
            this.showSnackbar('Note restored.')
            this.reloadNotes()
          }
        })
    },

    // DELETE NOTE PERMANENTLY
    deleteNoteForever () {
      this._deleteForever(this.selectedNote)
        .then((deleted) => {
          if (deleted) {
            this.noteDialog = false
            this.confirmDeleteDialog = false
            this.showSnackbar('Note deleted forever.')
            this.reloadNotes()
          }
        })
    },

    emptyTrash () {
      this._emptyTrash()
        .then((deleted) => {
          if (deleted) {
            this.confirmEmptyTrashDialog = false
            this.showSnackbar('Trash emptied.')
            this.reloadNotes()
          }
        })
    }
  }
}
</script>
