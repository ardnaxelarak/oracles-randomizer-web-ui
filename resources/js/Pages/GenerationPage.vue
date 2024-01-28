<template>
  <div class="card content-div mt-3 mb-3">
    <div class="card-header">
      <h5>Generate Randomized Game</h5>
    </div>
    <ul class="list-group list-group-flush">
      <li class="list-group-item d-flex column-gap-2">
        <input type="radio" autocomplete="off" class="btn-check" name="options-game" id="ages-game" value="ages" v-model="game">
        <label class="btn btn-outline-primary" for="ages-game">Oracle of Ages</label>

        <input type="radio" autocomplete="off" class="btn-check" name="options-game" id="seasons-game" value="seasons" v-model="game">
        <label class="btn btn-outline-danger" for="seasons-game">Oracle of Seasons</label>
      </li>
      <li class="list-group-item">
        <div class="form-check form-switch">
          <input class="form-check-input" type="checkbox" autocomplete="off" role="switch" id="hard_mode" v-model="hard">
          <label class="form-check-label" for="hard_mode">Hard Mode</label>
        </div>
        <div class="form-inline">
          <label class="form-label me-1" for="essences">Essences for Maku Seed</label>
          <select id="essences" autocomplete="off" class="custom-select form-select-sm mt-1 mb-1 pe-2" v-model="essences">
            <option value="8">8 Essences</option>
            <option value="7">7 Essences</option>
            <option value="6">6 Essences</option>
            <option value="5">5 Essences</option>
            <option value="4">4 Essences</option>
            <option value="3">3 Essences</option>
            <option value="2">2 Essences</option>
            <option value="1">1 Essence</option>
          </select>
        </div>
        <div class="form-check form-switch">
          <input class="form-check-input" type="checkbox" autocomplete="off" role="switch" id="cross_items" v-model="cross_items">
          <label class="form-check-label" for="cross_items">Cross Items</label>
        </div>
        <div class="form-check form-switch">
          <input class="form-check-input" type="checkbox" autocomplete="off" role="switch" id="linked_items" v-model="linked_items">
          <label class="form-check-label" for="linked_items">Linked Items</label>
        </div>
        <div class="form-check form-switch">
          <input class="form-check-input" type="checkbox" autocomplete="off" role="switch" id="maple_item" v-model="maple_item">
          <label class="form-check-label" for="maple_item">Include Maple Item</label>
        </div>
        <div class="form-check form-switch">
          <input class="form-check-input" type="checkbox" autocomplete="off" role="switch" id="gasha_item" v-model="gasha_item">
          <label class="form-check-label" for="gasha_item">Include Gasha Nut Item</label>
        </div>
        <div class="form-check form-switch">
          <input class="form-check-input" type="checkbox" autocomplete="off" role="switch" id="keysanity" v-model="keysanity">
          <label class="form-check-label" for="keysanity">Keysanity</label>
        </div>
        <div class="form-check form-switch">
          <input class="form-check-input" type="checkbox" autocomplete="off" role="switch" id="dungeon_shuffle" v-model="dungeon_shuffle">
          <label class="form-check-label" for="dungeon_shuffle">Dungeon Shuffle</label>
        </div>
        <div class="form-check form-switch" v-if="game == 'seasons'">
          <input class="form-check-input" type="checkbox" autocomplete="off" role="switch" id="portal_shuffle" v-model="portal_shuffle">
          <label class="form-check-label" for="portal_shuffle">Portal Shuffle</label>
        </div>
        <div class="form-check form-switch">
          <input class="form-check-input" type="checkbox" autocomplete="off" role="switch" id="auto_mermaid" v-model="auto_mermaid">
          <label class="form-check-label" for="auto_mermaid">Auto Mermaid</label>
        </div>
        <div class="form-inline">
          <label class="form-label me-1" for="fools_ore">Fool's Ore: </label>
          <select id="fools_ore" autocomplete="off" class="custom-select form-select-sm mt-1 mb-1 pe-2" v-model="fools_ore">
            <option value="0">Not in Pool</option>
            <option value="4">4 Damage (balanced)</option>
            <option value="12">12 Damage (vanilla)</option>
          </select>
        </div>
        <p class="mb-1 mt-1"><b>Starting Items:</b></p>
        <div class="form-check form-switch">
          <input class="form-check-input" type="checkbox" autocomplete="off" role="switch" id="starting_sword" v-model="starting_sword">
          <label class="form-check-label" for="starting_sword">Sword</label>
        </div>
        <div class="form-check form-switch">
          <input class="form-check-input" type="checkbox" autocomplete="off" role="switch" id="starting_big_sword" v-model="starting_big_sword">
          <label class="form-check-label" for="starting_big_sword">Biggoron's Sword</label>
        </div>
        <div class="form-check form-switch" v-if="game == 'seasons'">
          <input class="form-check-input" type="checkbox" autocomplete="off" role="switch" id="starting_treasure_map" v-model="starting_treasure_map">
          <label class="form-check-label" for="starting_treasure_map">Treasure Map</label>
        </div>
        <div class="form-check form-switch">
          <input class="form-check-input" type="checkbox" autocomplete="off" role="switch" id="starting_small_keys" v-model="starting_small_keys">
          <label class="form-check-label" for="starting_small_keys">All Small Keys</label>
        </div>
        <div class="form-check form-switch">
          <input class="form-check-input" type="checkbox" autocomplete="off" role="switch" id="starting_boss_keys" v-model="starting_boss_keys">
          <label class="form-check-label" for="starting_boss_keys">All Boss Keys</label>
        </div>
        <div class="form-check form-switch">
          <input class="form-check-input" type="checkbox" autocomplete="off" role="switch" id="starting_compasses" v-model="starting_compasses">
          <label class="form-check-label" for="starting_compasses">All Compasses</label>
        </div>
        <div class="form-check form-switch">
          <input class="form-check-input" type="checkbox" autocomplete="off" role="switch" id="starting_maps" v-model="starting_maps">
          <label class="form-check-label" for="starting_maps">All Dungeon Maps</label>
        </div>
        <div class="form-check form-switch" v-if="game == 'ages'">
          <input class="form-check-input" type="checkbox" autocomplete="off" role="switch" id="starting_slates" v-model="starting_slates">
          <label class="form-check-label" for="starting_slates">Slates</label>
        </div>
      </li>
      <li class="list-group-item">
        <button type="submit" class="btn btn-primary submit-btn" :disabled="generating" @click="generate">
          {{ generating ? "Generating..." : "Generate" }}
        </button>
      </li>
    </ul>
  </div>
</template>

<script>
import { defineComponent } from 'vue';

import axios from 'axios';

export default defineComponent({
  data() {
    return {
      generating: false,
      game: "ages",
      hard: false,
      essences: 8,
      cross_items: false,
      linked_items: false,
      maple_item: false,
      gasha_item: false,
      keysanity: false,
      dungeon_shuffle: false,
      portal_shuffle: false,
      auto_mermaid: true,
      fools_ore: 0,
      starting_sword: false,
      starting_big_sword: false,
      starting_treasure_map: false,
      starting_small_keys: false,
      starting_boss_keys: false,
      starting_compasses: false,
      starting_maps: false,
      starting_slates: false,
    };
  },
  methods: {
    romUpdated(rom) {
      this.rom = rom;
    },
    callApi() {
      return new Promise(
        function(resolve, reject) {
          const starting_items = [];
          if (this.starting_sword) {
            starting_items.push("sword");
          }
          if (this.starting_big_sword) {
            starting_items.push("biggoron's sword");
          }
          if (this.starting_small_keys) {
            starting_items.push("small keys");
          }
          if (this.starting_boss_keys) {
            starting_items.push("boss keys");
          }
          if (this.starting_compasses) {
            starting_items.push("compasses");
          }
          if (this.starting_maps) {
            starting_items.push("dungeon maps");
          }
          if (this.game == 'ages' && this.starting_slates) {
            starting_items.push("slates");
          }
          if (this.game == 'seasons' && this.starting_treasure_map) {
            starting_items.push("treasure map");
          }
          const settings = {
             game: this.game,
             hard: this.hard,
             cross_items: this.cross_items,
             linked_items: this.linked_items,
             maple_item: this.maple_item,
             gasha_item: this.gasha_item,
             keysanity: this.keysanity,
             dungeon_shuffle: this.dungeon_shuffle,
             auto_mermaid: this.auto_mermaid,
             fools_ore: this.fools_ore,
             essences: this.essences,
             starting_items: starting_items,
          };
          if (this.game == 'seasons') {
            settings.portal_shuffle = this.portal_shuffle;
          }
          axios
            .post('/api/generate', settings, {responseType: 'json'})
            .then(response => {
              resolve(response.data);
            })
            .catch(error => {
              reject(error);
            });
        }.bind(this)
      );
    },
    async generate() {
      this.generating = true;
      try {
        const response = await this.callApi();
        if (response.hash) {
          window.location.href = `h/${response.hash}`;
        }
      } catch (error) {
        this.generating = false;
      }
    },
  },
});
</script>

<style scoped>
.content-div {
  width: 30rem;
  margin: auto;
}
</style>

