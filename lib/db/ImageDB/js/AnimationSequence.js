class AnimationSequence {
	constructor(obj = {}) {
		this.MappingID = null;
		this.AnimationID = null;
		this.SequenceID = null;
		this.Ordinality = null;
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

export default AnimationSequence;