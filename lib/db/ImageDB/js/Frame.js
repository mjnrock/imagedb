class Frame {
	constructor(obj = {}) {
		this.FrameID = null;
		this.TrackID = null;
		this.EFrameID = null;
		this.Duration = null;
		this.Ordinality = null;
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

export default Frame;