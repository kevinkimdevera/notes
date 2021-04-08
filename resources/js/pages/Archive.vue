<template>
  <div class="pb-4">
    <v-container>
      <v-row :justify="listView ? 'center' : 'start'">
        <v-col cols="12" :lg="gridView ? 12 : 5">
          <v-row>
            <template v-for="(note, i) in archivedNotes">
              <v-col cols="12" :md="colMD" :lg="colLG" :xl="colXL" :key="`note-${i}`">
                <note-card
                  :note="note"
                  @update="updateNote"
                  @delete="deleteNote"/>
              </v-col>
            </template>
          </v-row>
        </v-col>
      </v-row>
    </v-container>
  </div>
</template>

<script>
import NoteCard from '../components/NoteCard'

import { mapActions, mapGetters } from 'vuex'

export default {
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
      archivedNotes: 'notes/ARCHIVED',
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
      _updateNote: 'notes/UPDATE',
      _deleteNote: 'notes/DELETE',
      setLoading: 'notes/CHANGE_LOADING_STATUS',
    }),
    getNotes() {
      this.setLoading(true)

      this._getNotes()
        .then(() => {
          this.setLoading(false)
        })
    },
    updateNote(id, data) {
      this.setLoading(true)

      this._updateNote(id, data)
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
