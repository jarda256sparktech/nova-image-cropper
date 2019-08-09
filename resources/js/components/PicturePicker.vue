<template>
    <div :class="{ 'picker-wrapper': imgSrc }">
        <input
            :id="hash"
            @change="setImage"
            class="inputfile"
            name="image"
            style="font-size: 1.2em; padding: 10px 0;"
        >
        <div
            class="bg-30 flex flex-wrap px-8 py-4"
            v-if="imgSrc"
        >
            <div
                @click="$emit('deleteImage')"
                class="btn btn-default btn-primary mr-3 mb-2 cursor-pointer"
            >{{__('Delete')}}
            </div>
            <label
                :for="hash"
                class="btn btn-default btn-primary mr-3 mb-2 cursor-pointer"
            >{{__('Change Image')}}</label>
        </div>
        <br>
        <div class="cropper-wrapper">
            <PictureCropper
                :image="imgSrc"
                :ratio="ratio"
                :value-object="valueObject"
                @update-value-object="passUpdateValueObject"
                :originalFileType="originalFileType"
                :originalName="originalName"
                @setCropImageSrc="setCropImage"
                ref="cropper"
                v-show="imgSrc"
            />
            <PicturePickerFile
                @change="setImage"
                class="picker-file"
                v-if="!imgSrc"
            />
            <PicturePreview
                :image="imgSrc"
                :cropImage="cropImgSrc"
                ref="preview"
                v-show="imgSrc"
            />
        </div>
        <br>
    </div>
</template>
<script>
	import {resizeImage} from '../utils/image'
	import PicturePickerFile from './PicturePickerFile'
	import PictureCropper from './PictureCropper'
	import PicturePreview from "./PicturePreview";

	export default {
		name: 'PicturePicker',

		components: {PicturePreview, PicturePickerFile, PictureCropper},

		props: ['value', 'valueObject', 'aspectRatio'],

		data() {
			return {
				hash: Math.random()
					.toString(36)
					.substring(7),
				hashCrop: Math.random()
					.toString(36)
					.substring(7),
				imgSrc: '',
				cropImgSrc: '',
				maxWidth: 584,
				cropImg: '',
				cropImgW: 0,
				cropImgH: 0,
				originalName: 'uploaded_file.jpg',
				originalFileType: 'image/jpeg'
			}
		},

		computed: {
			ratio() {
				return this.aspectRatio
			},

			wrapperStyle() {
				return {
					padding: '10px 20px'
				}
			}
		},

		watch: {
			value(value) {
				// console.log('watchValue', value);

				this.updateCropper(value)
			}
		},

		methods: {
			passUpdateValueObject(valueObject){
				this.$emit('update-value-object',valueObject);
            },
			updateCropper(image) {
				console.log('updateCropper');
				this.imgSrc = image;


				this.cropImg = image;

				if (this.$refs.cropper) {
					this.$refs.cropper.replace(image)
				}
			},

			setImage(e) {
				// console.log('setImage');
				// console.log('img',e);
				let file;

				if (e.raw) {
					file = e.raw
				} else {
					file = e.target.files[0]
				}

				if (!file.type.includes('image/')) {
					alert('Please select an image file');
					return
				}

				this.originalName = file.name;
				this.originalFileType = file.type;

				if (typeof FileReader === 'function') {
					const reader = new FileReader();

					reader.onload = event => {
						resizeImage(
							event.target.result,
							file.type,
							({dataUrl, width, height, file}) => {
								// console.log('resize');
								if (dataUrl) {
									this.$emit("update-value-object", {...this.valueObject, modified: true, originalBinary: dataUrl});
								} else {
									this.$emit("update-value-object", {});
								}
								this.updateCropper(dataUrl);
								this.$emit('input', dataUrl);
								this.$emit('setWidth', width);
								this.$emit('setHeight', height);
								this.$emit('fileChanged', file);
								this.$emit('cropFileChanged', file);
								this.cropImgSrc = dataUrl;
							}
						)
					};

					reader.readAsDataURL(file)
				} else {
					alert('Sorry, FileReader API not supported')
				}
			},

			setCropImage(cropDataUrl) {
				// console.log('setThumbImage');
				this.cropImgSrc = cropDataUrl;
			},
		}
	}
</script>
<style scoped>
    .cropper-wrapper {
        padding: 10px 20px;
    }

    .edit-buttons {
        height: 45px;
        width: 100%;
        display: flex;
        justify-content: flex-end;
        align-items: center;
        background-color: #ffffff;
        border-bottom: solid 1px #c4cdd5;
    }

    .edit-buttons i {
        cursor: pointer;
        color: #767e89;
        margin-right: 15px;
    }

    .picker-container {
        display: flex;
        justify-content: flex-start;
        align-items: flex-start;
    }

    .vertical-slider {
        display: inline-block;
        margin-left: 10px;
    }

    .horizontal-slider {
        margin-top: 10px;
    }

    .inputfile {
        width: 0.1px;
        height: 0.1px;
        opacity: 0;
        overflow: hidden;
        position: absolute;
        z-index: -1;
    }

    .inputfile + label {
        display: block;
    }

    .picker-button {
        display: flex;
        justify-content: center;
        align-items: center;
        cursor: pointer;
        font-family: Nunito;
        font-size: 14px;
        color: #233c44;
        height: 25px;
        padding: 0 10px;
        border-radius: 4px;
        box-shadow: 0 1px 0 0 rgba(22, 29, 37, 0.05);
        background-image: linear-gradient(to top, #ffffff, #f9fafb);
        border: solid 1px #c4cdd5;
        margin: 0 10px;
    }

    .inputfile:focus + label,
    .inputfile + label:hover {
        background-color: red;
    }
</style>
