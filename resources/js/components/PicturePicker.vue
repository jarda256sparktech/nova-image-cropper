<template>
    <div :class="{ 'picker-wrapper': parsedValueObject.loaded }">
        <input
            :id="hash"
            class="inputfile"
            name="image"
            style="font-size: 1.2em; padding: 10px 0;"
        >
        <div
            class="bg-30 flex flex-wrap px-8 py-4"
            v-if="parsedValueObject.loaded"
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
                :image="parsedValueObject.binaryImg"
                :ratio="ratio"
                :parsed-value-object="parsedValueObject"
                @update-value-object="passUpdateValueObject"
                :originalFileType="originalFileType"
                :originalName="originalName"
                ref="cropper"
                v-show="parsedValueObject.loaded"
            />
            <PicturePickerFile
                @change="pickImage"
                class="picker-file"
                v-if="!parsedValueObject.loaded"
            />
            <PicturePreview
                ref="preview"
                :parsed-value-object="parsedValueObject"
                v-show="parsedValueObject.loaded"
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

		props: ['value', 'parsedValueObject', 'aspectRatio', 'loaded'],

		data() {
			return {
				hash: Math.random()
					.toString(36)
					.substring(7),
				imgSrc: '',
				cropImgSrc: '',
				maxWidth: 584,
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
			},
		},

		methods: {
			passUpdateValueObject(parsedValueObject) {
				this.$emit('update-value-object', parsedValueObject);
			},
			// updateCropper(image) {
			// 	// console.log('updateCropper');
			// 	if (this.$refs.cropper) {
			// 		this.$refs.cropper.replace(image)
			// 	}
			// },

			pickImage(e) {
				let file;
				if (e.raw) {
					file = e.raw
				} else {
					file = e.target.files[0]
				}
				if (!file.type.includes('image/')) {
					alert('Please select an image file');
					return;
				}
				// this.originalName = file.name;
				// this.originalFileType = file.type;
				if (typeof FileReader === 'function') {
					const reader = new FileReader();
					reader.onload = event => {
						resizeImage(
							event.target.result,
							file.type,
							({dataUrl}) => {
								// this.updateCropper(dataUrl);
								if (dataUrl) {
									console.log('pickImage');
									this.$emit('update-value-object', {
										...this.parsedValueObject, modified: true, binaryImg:
										dataUrl, loaded: true, binaryCrop: null, cropBoxData: null
									})
								}
							}
						)
					};
					reader.readAsDataURL(file)
				} else {
					alert('Sorry, FileReader API not supported')
				}
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
