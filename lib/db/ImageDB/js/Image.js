class Image {
	constructor(obj = {}) {
		this.ImageID = null;
		this.FilePath = null;
		this.FileName = null;
		this.FileExtension = null;
		this.Width = null;
		this.Height = null;
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

export default Image;