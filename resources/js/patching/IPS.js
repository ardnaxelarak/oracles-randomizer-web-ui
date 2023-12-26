export default class IPS {
  constructor(buffer) {
    this.patch = buffer;
    const dec = new TextDecoder("utf-8");
    const header = dec.decode(buffer.subarray(0, 5));
    const footer = dec.decode(buffer.subarray(buffer.length - 3, buffer.length));
    this.valid = (header == 'PATCH' && footer == 'EOF');

    if (this.valid) {
      var pos = header.length;
      this.maxWrite = 0;
      while (pos + footer.length < buffer.length) {
        const offset = (buffer[pos++] << 16) | (buffer[pos++] << 8) | buffer[pos++];
        var size = (buffer[pos++] << 8) | buffer[pos++];
        if (size > 0) {
          pos += size;
        } else {
          size = (buffer[pos++] << 8) | buffer[pos++];
          pos++;
        }
        if (offset + size > this.maxWrite) {
          this.maxWrite = offset + size;
        }
      }
    }
  }

  apply(rom) {
    if (!this.valid) {
      throw "Invalid Patch";
    }

    const header = 5;
    const footer = 3;

    var newRom = rom;
    if (this.maxWrite > rom.length) {
      newRom = new Uint8Array(this.maxWrite);
      newRom.set(rom, 0);
    }

    // actually apply patch
    var pos = header;
    while (pos + footer < this.patch.length) {
      const offset = (this.patch[pos++] << 16) | (this.patch[pos++] << 8) | this.patch[pos++];
      var size = (this.patch[pos++] << 8) | this.patch[pos++];
      if (size > 0) {
        newRom.set(this.patch.subarray(pos, pos + size), offset);
        pos += size;
      } else {
        size = (this.patch[pos++] << 8) | this.patch[pos++];
        const value = this.patch[pos++];
        newRom.fill(value, offset, offset + size);
      }
    }

    return newRom;
  }
}
