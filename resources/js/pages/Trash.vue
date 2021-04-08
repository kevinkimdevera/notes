<template>
  <div class="pb-4">
    <v-container>
      <v-row :justify="listView ? 'center' : 'start'">
        <v-col cols="12" :lg="gridView ? 12 : 5">
          <v-row>
            <template v-for="(note, i) in trashedNotes">
              <v-col cols="12" :md="colMD" :lg="colLG" :xl="colXL" :key="`note-${i}`">
                <note-card
                  :note="note"
                  @delete-forever="confirmDelete"
                  @restore="restore"/>
              </v-col>
            </template>
          </v-row>
        </v-col>
      </v-row>
    </v-container>

    <v-dialog width="290" v-model="confirmDeleteDialog">
      <v-card>
        <v-card-title class=" text-subtitle-1">Delete note forever?</v-card-title>
        <v-card-text>
          This action cannot be undone.
        </v-card-text>
        <v-card-actions>
          <v-spacer></v-spacer>
          <v-btn text @click="closeDelete">Cancel</v-btn>
          <v-btn color="blue" text @click="deleteForever(selectedNote)" :loading="notesLoading">Delete</v-btn>
        </v-card-actions>
      </v-card>
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
          dark
          v-on="on"
          color="red"
        >
          <v-icon>mdi-delete-empty-outline</v-icon>
        </v-btn>
      </template>
      <span>Empty Trash</span>
    </v-tooltip>
  </div>
</template>

<script>
import NoteCard from '../components/NoteCard'

import { mapActions, mapGetters } from 'vuex'

export default {
  data() {
    return {
      selectedNote: null,
      confirmDeleteDialog: false
    }
  },
  components: {
    NoteCard
  },

  beforeRouteEnter(to, from, next) {
    next(vm => {
      vm.getNotes()
    })
  },

  computed: {
    ...mapGetters({
      trashedNotes: 'notes/TRASHED',
      notesLoading: 'notes/LOADING',
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
  },

  methods: {
    ...mapActions({
      _getNotes: 'notes/GET',
      _deleteForever: 'notes/DELETE_FOREVER',
      _restoreNote: 'notes/RESTORE',
      setLoading: 'notes/CHANGE_LOADING_STATUS',
    }),
    getNotes() {
      this.setLoading(true)

      this._getNotes()
        .then(() => {
          this.setLoading(false)
        })
    },
    closeDelete() {
      this.selectedNote = null
      this.confirmDeleteDialog = false
    },
    confirmDelete(note) {
      this.selectedNote = note
      this.confirmDeleteDialog = true
    },
    deleteForever(data) {
      this.setLoading(true)

      this._deleteForever(data)
        .then((deleted) => {
          if (deleted) {
            this._getNotes()
          }
        })
        .then(() => {
          this.closeDelete()
          this.setLoading(false)
        })
    },
    restore(data) {
      this.setLoading(true)

      this._restoreNote(data)
        .then((restored) => {
          if (restored) {
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
