<template>
    <default-field :field="field">
        <template slot="field">
            <loading-view :loading="deleting">
                <div class="picker-wrapper">
                    <PicturePicker
                        :aspect-ratio="field.aspectRatio"
                        :parsed-value-object="parsedValueObject"
                        @update-value-object="onUpdateValueObject"
                        @deleteImage="confirmRemoval"
                        @resetChanges="confirmReset"
                        ref="picturePicker"
                        v-model="value"
                    />
                    <p
                        class="my-2 text-danger"
                        v-if="hasError"
                    >{{ firstError }}</p>

                    <portal to="modals">
                        <transition name="fade">
                            <confirm-upload-removal-modal
                                @close="closeRemoveModal"
                                @confirm="removeFile"
                                v-if="removeModalOpen"
                            />
<!--                            <confirm-upload-removal-modal-->
<!--                                @close="closeResetModal"-->
<!--                                @confirm="resetChanges"-->
<!--                                v-if="resetModalOpen"-->
<!--                            />-->
                        </transition>
                    </portal>
                </div>
            </loading-view>
        </template>
    </default-field>
</template>

<script>
	import {Errors, FormField, HandlesValidationErrors} from 'laravel-nova'
	import PicturePicker from './PicturePicker'
	import {UrlToBase64} from '../utils/image'

	require('element-ui/lib/theme-chalk/index.css');

	export default {
		components: {PicturePicker},

		mixins: [FormField, HandlesValidationErrors],

		props: ['resourceName', 'resourceId', 'field'],

		data() {
			return {
				isNew: false,
				parsedValueObject: {
					modified: false,
					loaded: false,
                    binaryImg:null,
                    binaryCrop:null,
                    cropBoxData:null,
                    imgSrc: null,
                    cropSrc: null,
                },
				originalValueObject: {},
				editingImage: true,
				resetModalOpen: false,
				removeModalOpen: false,
				deleting: false,
				uploadErrors: new Errors()
			}
		},

		computed: {
			isNew() {
				return !Boolean(this.field.previewUrl)
			},
		},
		watch: {
			parsedValueObject() {
				let stringObject = JSON.stringify(this.parsedValueObject);
				console.log('watch val object');
				this.value = stringObject;

			}
		},

		beforeMount() {
			// console.log('before mounted FormField');
			this.value = this.field.value || '';

			let parsedValueFromJson = JSON.parse(this.field.value);
			this.parsedValueObject = {...this.parsedValueObject,...parsedValueFromJson};
			if (!this.isNew) {
				// let file = new File(this.field.previewUrl);
				// if (typeof FileReader === 'function') {
				// 	const reader = new FileReader();
				// 	reader.onload = event => {
				// 		this.parsedValueObject.binaryImg = event.target.result
				// 	};
				// 	reader.readAsDataURL(file)
				// } else {
				// 	alert('Sorry, FileReader API not supported')
				// }
				//
				this.parsedValueObject.binaryImg = UrlToBase64(this.field.previewUrl);
				this.originalValueObject = this.parsedValueObject;
			}
		},

		methods: {
			async removeFile() {
				this.closeRemoveModal();
                alert('remove');
				// if (this.isNew) {
				// 	this.resetFile();
				// 	return
				// }
                //
				// this.deleting = true;
				// this.uploadErrors = new Errors();
                //
				// const {
				// 	resourceName,
				// 	resourceId,
				// 	relatedResourceName,
				// 	relatedResourceId,
				// 	viaRelationship
				// } = this;
				// const attribute = this.field.attribute;
                //
				// const uri = this.viaRelationship
				// 	? `/nova-api/${resourceName}/${resourceId}/${relatedResourceName}/${relatedResourceId}/field/${attribute}?viaRelationship=${viaRelationship}`
				// 	: `/nova-api/${resourceName}/${resourceId}/field/${attribute}`;
                //
				// try {
				// 	await Nova.request().delete(uri);
				// 	this.deleting = false;
				// 	this.resetFile();
				// 	this.$emit('file-deleted')
				// } catch (error) {
				// 	this.deleting = false;
                //
				// 	if (error.response.status == 422) {
				// 		this.uploadErrors = new Errors(error.response.data.errors)
				// 	}
				// }
			},

			onUpdateValueObject(newValueObject) {
				console.log('onUpdateValueObject');
				this.parsedValueObject = newValueObject;
			},
			confirmRemoval() {
				this.removeModalOpen = true
			},
			closeRemoveModal() {
				this.removeModalOpen = false
			},
			confirmReset() {
				this.resetModalOpen = true
			},
			closeResetModal() {
				this.resetModalOpen = false
			},
			resetChanges() {
				this.parsedValueObject = this.originalValueObject;
			},
		}
	}
</script>
<style scoped>
</style>
