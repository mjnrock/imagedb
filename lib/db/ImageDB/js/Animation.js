class Animation {
	constructor(obj = {}) {
		this.AnimationID = null;
		this.EAnimationID = null;
		this.Name = null;
		this.Description = null;
		this.Value = null;
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

export default Animation;