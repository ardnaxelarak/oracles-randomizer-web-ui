<template>
  <div class="card content-div mt-3 mb-3">
    <div class="card-header">
      <h5>Game Details - {{ hash }}</h5>
    </div>
    <ul class="list-group list-group-flush">
      <li v-if="game == 'Ages'" class="list-group-item">
        <RomLoader rom_name="ages" crc="3800A387" @rom_update="romUpdated" />
      </li>
      <li v-if="game == 'Seasons'" class="list-group-item">
        <RomLoader rom_name="seasons" crc="D7E9F5D7" @rom_update="romUpdated" />
      </li>
      <li class="list-group-item">
        <div>
          Rom Build Date: {{ romDate }}
        </div>
        <div v-if="metadata?.settings?.hard != null">
          Difficulty: {{ metadata.settings.hard ? "Hard" : "Normal" }}
        </div>
        <div v-if="metadata?.settings?.cross_items != null">
          Cross Items: {{ metadata.settings.cross_items ? "True" : "False" }}
        </div>
        <div v-if="metadata?.settings?.linked_items != null">
          Linked Items: {{ metadata.settings.linked_items ? "True" : "False" }}
        </div>
        <div v-if="metadata?.settings?.maple_item != null">
          Maple Item: {{ metadata.settings.maple_item ? "True" : "False" }}
        </div>
        <div v-if="metadata?.settings?.keysanity != null">
          Keysanity: {{ metadata.settings.keysanity ? "True" : "False" }}
        </div>
        <div v-if="metadata?.settings?.dungeon_shuffle != null">
          Dungeon Shuffle: {{ metadata.settings.dungeon_shuffle ? "True" : "False" }}
        </div>
        <div v-if="metadata?.settings?.portal_shuffle != null">
          Portal Shuffle: {{ metadata.settings.portal_shuffle ? "True" : "False" }}
        </div>
        <div v-if="metadata?.settings?.auto_mermaid != null">
          Auto Mermaid Suit: {{ metadata.settings.auto_mermaid ? "True" : "False" }}
        </div>
        <div v-if="metadata?.settings?.starting_items != null">
          Starting Items: {{ metadata.settings.starting_items.length > 0 ? metadata.settings.starting_items.join(", ") : "None" }}
        </div>
      </li>
      <li class="list-group-item">
        <div>
          Custom sprites are a work in progress but should be stable. Please report any issues you run into!
        </div>
        <label class="form-label" for="sprite_select">Player Sprite</label>
        <select id="sprite_select" autocomplete="off" class="form-select" v-model="sprite">
          <option value="link">Link</option>
          <option value="marin">Marin</option>
          <option value="likelike">Like Like</option>
        </select>
        <label class="form-label" for="palette_select">Player Sprite</label>
        <select id="palette_select" autocomplete="off" class="form-select" v-model="palette">
          <option value="green">Green</option>
          <option value="blue">Blue</option>
          <option value="gold">Gold</option>
          <option value="red">Red</option>
        </select>
      </li>
      <li class="list-group-item">
        <div class="form-check form-switch">
          <input class="form-check-input" type="checkbox" autocomplete="off" role="switch" id="heart_beeps" v-model="heart_beeps">
          <label class="form-check-label" for="heart_beeps">Half-Speed Low Health Beep</label>
        </div>
      </li>
      <li class="list-group-item">
        <button type="submit" class="btn btn-primary submit-btn" :disabled="!rom" @click="download">
          Download ROM
        </button>
      </li>
      <li class="list-group-item">
        <button type="submit" class="btn btn-primary submit-btn" @click="downloadSpoiler">
          Download Spoiler
        </button>
      </li>
      <a id="downloader" style="display: none;" />
    </ul>
  </div>
</template>

<script>
import { defineComponent } from 'vue';

import axios from 'axios';
import CRC32 from 'crc-32';
import localforage from 'localforage';

import BPS from '@/patching/BPS.js';
import IPS from '@/patching/IPS.js';
import RomLoader from '../components/RomLoader.vue';

export default defineComponent({
  components: {
    RomLoader,
  },
  props: {
    hash: "",
    game: "",
    build: null,
    metadata: {},
    spoiler: null,
  },
  data() {
    return {
      rom: null,
      sprite: "link",
      palette: "green",
      heart_beeps: true,
    };
  },
  computed: {
    romDate() {
      const date = new Date(this.build * 1000);
      return date.toLocaleDateString();
    },
  },
  methods: {
    romUpdated(rom) {
      this.rom = rom;
    },
    getBasePatch() {
      return new Promise(
        function(resolve, reject) {
          axios
            .get(`/api/base_patches/${this.build}/${this.game.toLowerCase()}`, {responseType: 'arraybuffer'})
            .then(response => {
              resolve(new Uint8Array(response.data));
            })
            .catch(error => {
              reject(error);
            });
        }.bind(this)
      );
    },
    getSeedPatch() {
      return new Promise(
        function(resolve, reject) {
          axios
            .get(`/api/seeds/${this.hash}`, {responseType: 'arraybuffer'})
            .then(response => {
              resolve(new Uint8Array(response.data));
            })
            .catch(error => {
              reject(error);
            });
        }.bind(this)
      );
    },
    getSpritePatch() {
      return new Promise(
        function(resolve, reject) {
          axios
            .get(`/api/sprites/${this.build}/${this.game.toLowerCase()}/${this.sprite}`, {responseType: 'arraybuffer'})
            .then(response => {
              resolve(new IPS(new Uint8Array(response.data)));
            })
            .catch(error => {
              reject(error);
            });
        }.bind(this)
      );
    },
    getPatch(patchName) {
      return new Promise(
        function(resolve, reject) {
          axios
            .get(`/api/patches/${this.build}/${this.game.toLowerCase()}/${patchName}`, {responseType: 'arraybuffer'})
            .then(response => {
              resolve(new IPS(new Uint8Array(response.data)));
            })
            .catch(error => {
              reject(error);
            });
        }.bind(this)
      );
    },
    async download() {
      var output = this.rom;
      const basepatch = await this.getBasePatch();
      const base_bps = new BPS(basepatch);
      output = base_bps.apply(output);

      const seedpatch = await this.getSeedPatch();
      const bps = new BPS(seedpatch);
      output = bps.apply(output);

      if (this.sprite != "link") {
        const patch = await this.getSpritePatch();
        output = patch.apply(output);
      }

      if (this.palette != "green") {
        const patch = await this.getPatch(`palette_${this.palette}`);
        output = patch.apply(output);
      }

      if (this.heart_beeps) {
        const patch = await this.getPatch("heartbeep");
        output = patch.apply(output);
      }

      const blob = new Blob([output], { type: 'octet/stream' });
      const link = document.getElementById('downloader');
      link.href = URL.createObjectURL(blob);
      link.download = `OO${this.game.charAt(0)}_${this.hash}.gbc`;
      link.click();
    },
    downloadSpoiler() {
      const link = document.getElementById('downloader');
      link.href = `/api/logs/${this.hash}`;
      link.download = `OO${this.game.charAt(0)}_${this.hash}_spoiler.txt`;
      link.click();
    },
  },
});
</script>

<style>
.content-div {
  width: 30rem;
  margin: auto;
}
</style>
