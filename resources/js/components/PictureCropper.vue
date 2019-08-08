<template>
    <div
        class="container"
        ref="container"
    >
        <img
            :src="image"
            alt="Picture"
            ref="img"
        >
    </div>
</template>
<script>
	import Cropper from 'cropperjs'
	import 'cropperjs/dist/cropper.css'

	export default {
		name: 'PictureCropper',

		props: {
			image: {
				type: String,
				default: ''
			},

			ratio: {
				type: Number,
				default: 1
			},
			originalFileType: {
				type: String,
				default: ''
			},
			originalName: {
				type: String,
				default: ''
			}
		},

		data() {
			return {
				cropper: null,
				width: 0,
				height: 0
			}
		},

		watch: {
			image(image) {
				if (image) {
					this.buildCropper()
				}
			}
		},

		mounted() {
			window.addEventListener('resize', this.setWidth);
			this.buildCropper()
		},

		destroyed() {
			window.removeEventListener('resize', this.setWidth);
			this.cropper.destroy()
			// this.$refs.img.removeEventListener('cropmove', this.updateThumb);
		},

		methods: {
			buildCropper() {
				if (this.cropper) {
					this.cropper.destroy();
				}

				this.setWidth();
				const self = this;

				this.$refs.img.addEventListener('cropmove', this.updateThumb);

				this.cropper = new Cropper(this.$refs.img, {
					viewMode: 1,
					dragMode: 'none',
					autoCropArea: 1,
					aspectRatio: self.ratio,
					checkCrossOrigin: false,
					movable: false,
					rotatable: false,
					zoomable: false,
					minContainerWidth: self.width,
					minContainerHeight: self.height,
					preview: '#cropped-preview'
				});
				this.cropper.replace(this.image)
			},

			updateThumb() {
				const canvas = this.getCroppedCanvas();
				if (canvas) {
					// console.log('updateThumb');
					const thumbFileSrc = canvas.toDataURL();
					this.$emit('setThumbImageSrc', thumbFileSrc);
					canvas.toBlob(blob => {
						const {type} = blob;
						const file = new File([blob], this.originalName, {
							type,
							lastModified: Date.now()
						});
						// console.log(file,thumbFileSrc);
						this.$emit('thumbFileChanged', file);
					}, this.originalFileType);
				}
			},


			setWidth() {
				const width = this.$refs.container.clientWidth;
				if (!width) {
					return
				}
				this.width = width;
				this.height = this.ratio ? this.width / this.ratio : this.height
			},

			getCroppedCanvas() {
				if (!this.cropper) {
					return
				}
				return this.cropper.getCroppedCanvas()
			},

			replace(image) {
				this.image = image;

				this.buildCropper();
				this.$emit('thumbFileChanged', image)
			}
		}
	}
</script>
<style scoped>
    .container {
        max-width: 640px;
        margin: 20px auto;
    }

    img {
        width: 100%;
        max-width: 100%;
    }
</style>
