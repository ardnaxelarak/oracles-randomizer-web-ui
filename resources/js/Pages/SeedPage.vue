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
      <li v-if="metadata?.settings?.hard != null" class="list-group-item">
        Difficulty: {{ metadata.settings.hard ? "Hard" : "Normal" }}
      </li>
      <li class="list-group-item">
        <button type="submit" class="btn btn-primary submit-btn" :disabled="!rom" @click="download">
          Download
        </button>
        <a id="downloader" style="display: none;" />
      </li>
    </ul>
  </div>
</template>

<script>
import { defineComponent } from 'vue';

import axios from 'axios';
import CRC32 from 'crc-32';
import localforage from 'localforage';

import BPS from '@/patching/BPS.js';
import RomLoader from '../components/RomLoader.vue';

export default defineComponent({
  components: {
    RomLoader,
  },
  props: {
    hash: "",
    game: "",
    build: null,
    bps: null,
    metadata: {},
    spoiler: null,
  },
  data() {
    return {
      rom: null,
    };
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
    async download() {
      var output = this.rom;
      const basepatch = await this.getBasePatch();
      console.log(basepatch);
      const base_bps = new BPS(basepatch);
      output = base_bps.apply(output);

      const seedpatch = await this.getSeedPatch();
      const bps = new BPS(seedpatch);
      output = bps.apply(output);

      const blob = new Blob([output], { type: 'octet/stream' });
      const link = document.getElementById('downloader');
      link.href = URL.createObjectURL(blob);
      link.download = `OO${this.game.charAt(0)}_${this.hash}.gbc`;
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
