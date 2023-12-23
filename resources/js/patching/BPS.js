import CRC32 from 'crc-32';
import StatefulBuffer from './StatefulBuffer.js';

export default class BPS {
  constructor(buffer) {
    this.patch = new StatefulBuffer(buffer);
    this.headerLength = 4;
    this.footerLength = 12;

    const header = this.patch.readString(this.headerLength);
    this.valid = (header == 'BPS1');
  }

  apply(rom) {
    this.patch.seek(this.patch.length - this.footerLength);

    const sourceCrc = this.patch.readInt();
    const targetCrc = this.patch.readInt();

    if (sourceCrc != CRC32.buf(rom, 0)) {
      console.log("Expected CRC: " + sourceCrc.toString(16));
      console.log("Actual CRC: " + (CRC32.buf(rom, 0) >>> 0).toString(16));
      throw "Patch source CRC does not match"
    }

    this.patch.seek(this.headerLength);

    const sourceSize = this.patch.readVlv();
    const targetSize = this.patch.readVlv();
    const metadataSize = this.patch.readVlv();

    this.patch.skip(metadataSize);

    const target = new Uint8Array(targetSize);

    var outputOffset = 0;
    var sourceRelativeOffset = 0;
    var targetRelativeOffset = 0;

    while (this.patch.pos + this.footerLength < this.patch.length) {
      const chunk = this.patch.readVlv();
      const action = chunk & 3;
      const length = (chunk >> 2) + 1;

      if (action == 0) {
        target.set(rom.subarray(outputOffset, outputOffset + length), outputOffset);
        outputOffset += length;
      } else if (action == 1) {
        target.set(this.patch.readArray(length), outputOffset);
        outputOffset += length;
      } else if (action == 2) {
        sourceRelativeOffset += this.patch.readSignedVlv();
        target.set(rom.subarray(sourceRelativeOffset, sourceRelativeOffset + length), outputOffset);
        outputOffset += length;
        sourceRelativeOffset += length;
      } else if (action == 3) {
        targetRelativeOffset += this.patch.readSignedVlv();
        for (var i = 0; i < length; i++) {
          target[outputOffset++] = target[targetRelativeOffset++];
        }
      }
    }

    if (targetCrc != CRC32.buf(target, 0)) {
      throw "Patch target CRC does not match"
    }

    return target;
  }
}
