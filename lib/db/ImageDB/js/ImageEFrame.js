class ImageEFrame {
	constructor(obj = {}) {
		this.MappingID = null;
		this.ImageID = null;
		this.EFrameID = null;
		this.UUID = null;

		this.Set(obj);
	}

	Set(obj = {}) {
		for(let key in obj) {
			this[ key ] = obj[ key ];
		}

		return this;
	}
}

export default ImageEFrame;