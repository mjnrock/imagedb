class Camera {
	constructor(obj = {}) {
		this.CameraID = null;
		this.Name = null;
		this.X = null;
		this.Y = null;
		this.Z = null;
		this.Pitch = null;
		this.Yaw = null;
		this.Roll = null;
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

export default Camera;