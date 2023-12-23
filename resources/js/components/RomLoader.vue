<template>
  <label :for="'romInput_' + rom_name" class="form-label">Vanilla {{ rom_display }} Rom:</label>
  <input :id="'romInput_' + rom_name" class="form-control" type="file" accept=".gbc" @change="uploadBaseRom($event.target.files[0])" />
  <div v-if="rom_error" class="invalid">{{ rom_error }}</div>
</template>

<script>
import { defineComponent } from 'vue';

import CRC32 from 'crc-32';
import localforage from 'localforage';

export default defineComponent({
  props: {
    rom_name: "",
    crc: "",
  },
  computed: {
    rom_display() {
      return `Oracle of ${this.rom_name.charAt(0).toUpperCase()}${this.rom_name.slice(1)}`;
    },
  },
  data() {
    return {
      rom: null,
      rom_error: null,
    };
  },
  async mounted() {
    const file = await localforage.getItem(`vanilla_${this.rom_name}`);
    if (file) {
      const dataTransfer = new DataTransfer();
      dataTransfer.items.add(file);
      document.getElementById(`romInput_${this.rom_name}`).files = dataTransfer.files;
      this.uploadBaseRom(file);
    }
  },
  methods: {
    setRom(rom) {
      this.rom = rom;
      this.$emit('rom_update', rom);
    },
    uploadBaseRom(file) {
      if (!file) {
        this.rom_error = null;
        this.setRom(null);
        return;
      }
      const reader = new FileReader();
      reader.onload = function() {
        const buffer = new Uint8Array(reader.result);
        const crc = (CRC32.buf(buffer, 0) >>> 0).toString(16).toUpperCase();

        if (crc != this.crc) {
          this.rom_error = `Expected CRC ${this.crc}, but got ${crc}`;
          this.setRom(null);
          return;
        }

        this.rom_error = null;
        this.setRom(buffer);
        localforage.setItem(`vanilla_${this.rom_name}`, file);
      }.bind(this);
      reader.readAsArrayBuffer(file);
    },
  }
});
</script>

<style>
.invalid {
  text-align: center;
  color: red;
}
</style>
