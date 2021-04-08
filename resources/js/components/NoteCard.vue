<template>
  <v-hover v-slot="{ hover }">
    <v-card :elevation="hover ? 2 : 0" outlined :color="noteColor" rounded @click="$emit('click', note)">
      <template v-if="hasTitle">
        <v-card-title class="text-subtitle-2 font-weight-bold pb-0">{{ noteTitle }}
      </v-card-title>
      </template>
      <v-card-text :style="{ 'min-height': hasTitle ? '58px' : '96px'}" class=" text-truncate d-inline-block">
        <span class="note-text text-subtitle-1">{{ noteContent }}</span>
      </v-card-text>

      <v-card-actions style="min-height: 52px;">
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

              <v-card class="pa-1" width="155">
                <template v-for="(color, i) in colors">
                  <v-btn @click.stop="changeColor(color)" :key="`color-col-${i}`" icon small :class="`${color} lighten-3 ma-1`" elevation="1">
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
    note: {
      type: Object
    },
  },

  data() {
    return {
      colors: [
        'white', 'red', 'pink', 'purple', 'indigo', 'blue', 'cyan', 'teal', 'green', 'blue-grey', 'orange', 'brown'
      ],
    }
  },

  computed: {
    ...mapGetters({
      viewMode: 'notes/VIEW_MODE'
    }),
    hasTitle() {
      return !!this.note?.title
    },
    noteId() {
      return this.note?.id
    },
    notePinned() {
      return this.note?.pinned
    },
    noteArchived() {
      return this.note?.archived
    },
    noteColor() {
      return this.note?.color == 'white' ? null : `${this.note?.color} lighten-4`
    },
    noteTitle() {
      return this.note?.title
    },
    noteContent() {
      var limit = ((this.viewMode == 'grid') && (this.$vuetify.breakpoint.mdAndUp)) ? '50' : '150'

      return (this.note?.contents?.length > limit)
        ? this.note?.contents?.substring(0, limit) + '...'
        : this.note?.contents
    },
    noteTrashed() {
      return this.note?.trashed
    }
  },

  methods: {
    changeColor(color) {
      this.$emit('update', {
        id: this.noteId,
        data: {
          color: color
        }
      })
    },
    togglePin() {
      this.$emit('update', {
        id: this.noteId,
        data: {
          pinned_at: this.notePinned ? null : moment().format('Y-MM-DD HH:mm:ss')
        }
      })
    },
    toggleArchive() {
      this.$emit('update', {
        id: this.noteId,
        data: {
          pinned_at: null,
          archived_at: this.noteArchived ? null : moment().format('Y-MM-DD HH:mm:ss')
        }
      })
    },
    deleteNote() {
      this.$emit('delete', {
        id: this.noteId
      })
    },
    deleteForever() {
      this.$emit('delete-forever', {
        id: this.noteId
      })
    },
    restore() {
      this.$emit('restore', {
        id: this.noteId
      })
    }
  }
}
</script>

<style>
  .note-text {
    white-space: pre-line;
  }
</style>
