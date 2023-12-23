export default class StatefulBuffer {
  constructor(buffer) {
    this.buffer = buffer;
    this.pos = 0;
    this.decoder = new TextDecoder("utf-8");
  }

  get length() {
    return this.buffer.length;
  }

  seek(address) {
    this.pos = address;
  }

  skip(length) {
    this.pos += length;
  }

  readByte() {
    return this.buffer[this.pos++];
  }

  readInt() {
    var value = 0;
    for (var i = 0; i < 4; i++) {
      const byte = this.readByte();
      value |= byte << (i * 8);
    }
    return value;
  }

  readArray(length) {
    const result = this.buffer.subarray(this.pos, this.pos + length);
    this.pos += length;
    return result;
  }

  readVlv() {
    var data = 0;
    var shift = 0;

    while (true) {
      const byte = this.readByte();
      data += (byte & 0x7F) << shift
      if ((byte & 0x80) > 0) {
        return data;
      }
      shift += 7;
      data += 1 << shift;
    }
  }

  readSignedVlv() {
    var value = this.readVlv();
    if ((value & 1) > 0) {
      return -(value >> 1);
    } else {
      return value >> 1;
    }
  }

  readString(length) {
    const string = this.decoder.decode(this.buffer.subarray(this.pos, length));
    this.pos += length;
    return string;
  }
}
