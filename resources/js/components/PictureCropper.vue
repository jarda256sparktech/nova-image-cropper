<template>
	<div
			ref="container"
			class="container"
	>
		<img
				ref="img"
				:src="image"
				alt="Picture"
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
			window.addEventListener('resize', this.setWidth)
			this.buildCropper()
		},

		destroyed() {
			window.removeEventListener('resize', this.setWidth)
			this.cropper.destroy()
		},

		methods: {
			buildCropper() {
				if (this.cropper) {
					this.cropper.destroy()
				}

				this.setWidth()
				const self = this

				this.$refs.img.addEventListener('cropmove', (event) => {
					this.updateThumb()
				});
				this.$refs.img.addEventListener('cropstart', (event) => {
					this.updateThumb()
				});

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
				})
				this.cropper.replace(this.image)
			},

			updateThumb() {
				const canvas = this.getCroppedCanvas();
				if (canvas) {
				    const thumbFile = canvas.toDataURL();
				    console.log('thumbFile',thumbFile);
					this.$emit('thumbFileChanged', thumbFile)
				}
			},


			setWidth() {
				const width = this.$refs.container.clientWidth
				if (!width) {
					return
				}
				this.width = width
				this.height = this.ratio ? this.width / this.ratio : this.height
			},

			getCroppedCanvas() {
				if (!this.cropper) {
					return
				}
				return this.cropper.getCroppedCanvas()
			},

			replace(image) {
				this.image = image

				this.buildCropper()
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
