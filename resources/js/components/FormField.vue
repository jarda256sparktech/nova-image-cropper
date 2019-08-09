<template>
    <default-field :field="field">
        <template slot="field">
            <loading-view :loading="deleting">
                <div class="picker-wrapper">
                    <PicturePicker
                        :aspect-ratio="field.aspectRatio"
                        :value-object="valueObject"
                        @update-value-object="onUpdateValueObject"
                        @deleteImage="confirmRemoval"
                        @fileChanged="setFile"
                        @cropFileChanged="setCropFile"
                        ref="picturePicker"
                        v-model="value"
                        v-show="editingImage || !value"
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

	require('element-ui/lib/theme-chalk/index.css');

	export default {
		components: {PicturePicker},

		mixins: [FormField, HandlesValidationErrors],

		props: ['resourceName', 'resourceId', 'field'],

		data() {
			return {
				deleting: false,
				editingImage: false,
				cropFile: null,
				file: null,
				origFile: null,
				fileName: '',
				removeModalOpen: false,
                valueObject: {},
				uploadErrors: new Errors()
			}
		},

		computed: {
			/**
			 * Determine if should hit the endpoint for delete
			 */
			imageExistsOnServer() {
				return Boolean(this.field.previewUrl)
			}
		},

		mounted() {
			console.log('mounted field',this.field);
			this.value = this.field.value || '';
			this.valueObject = JSON.parse(this.field.value) || {};
		},

		methods: {
			setFile(file) {
				this.editingImage = true;
				// console.log('setFile');
				this.file = file;
				this.fileName = file.name
			},
			setCropFile(file) {
				// console.log('setThumbFile');
				this.cropFile = file
			},

			/*
             * Reset the file state.
             */
			resetFile(file) {
				this.file = null;
				this.cropFile = null;
				this.fileName = '';
				this.value = '';
				this.valueObject = {};
			},

			/**
			 * Confirm removal of the linked file
			 */
			confirmRemoval() {
				// this.editingImage = false
				this.removeModalOpen = true
			},

			/**
			 * Close the upload removal modal
			 */
			closeRemoveModal() {
				this.removeModalOpen = false
				// this.editingImage = true
			},



			async deleteImage(index) {
				axios.delete('/nova-vendor/nova-image-cropper/delete/');
				this.images = {};
				this.value = '';
			},
			/**
			 * Remove the linked file from storage
			 */
			async removeFile() {
				this.closeRemoveModal();

				if (!this.imageExistsOnServer) {
					this.resetFile();
					return
				}

				this.deleting = true;
				this.uploadErrors = new Errors();

				const {
					resourceName,
					resourceId,
					relatedResourceName,
					relatedResourceId,
					viaRelationship
				} = this;
				const attribute = this.field.attribute;

				const uri = this.viaRelationship
					? `/nova-api/${resourceName}/${resourceId}/${relatedResourceName}/${relatedResourceId}/field/${attribute}?viaRelationship=${viaRelationship}`
					: `/nova-api/${resourceName}/${resourceId}/field/${attribute}`;

				try {
					await Nova.request().delete(uri);
					this.deleting = false;
					this.resetFile();
					this.$emit('file-deleted')
				} catch (error) {
					this.deleting = false;

					if (error.response.status == 422) {
						this.uploadErrors = new Errors(error.response.data.errors)
					}
				}
			},

			// setInitialValue() {
			// 	console.log('init field value',this.field.value);
			// 	this.value = this.field.value || '';
			// 	this.valueObject = JSON.parse(this.field.value) || {};
            //
			// },

			fill(formData) {
				formData.append(this.field.attribute, this.value || '');
			},

			/**
			 * Update the field's internal value.
			 */
			handleChange(value) {
				this.value = value
			},

			onUpdateValueObject(valueObject){
				console.log('onUpdateValueObject',valueObject);
				this.valueObject = valueObject;
            }
		}
	}
</script>
<style scoped>
</style>
