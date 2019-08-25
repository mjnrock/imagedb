class EFrame {
	constructor(obj = {}) {
		this.EFrameID = null;
		this.ETrackID = null;
		this.Name = null;
		this.Description = null;
		this.Tags = null;
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

export default EFrame;