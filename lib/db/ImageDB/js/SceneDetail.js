class SceneDetail {
	constructor(obj = {}) {
		this.SceneDetailID = null;
		this.SceneID = null;
		this.CameraID = null;
		this.TrackID = null;
		this.ZIndex = null;
		this.IsRequired = null;
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

export default SceneDetail;