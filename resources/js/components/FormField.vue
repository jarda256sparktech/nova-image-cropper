<template>
    <default-field :field="field">
        <template slot="field">
            <loading-view :loading="deleting">
                <div class="picker-wrapper">
                    <PicturePicker
                        :aspect-ratio="field.aspectRatio"
                        :reset-count="resetCount"
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
                            <confirm-upload-reset-modal
                                @close="closeResetModal"
                                @confirm="resetChanges"
                                v-if="resetModalOpen"
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
					isNew: true,
					binaryImg: null,
					binaryCrop: null,
					cropBoxData: null,
					imgSrc: null,
					cropSrc: null,
				},
				originalValueObject: {},
				editingImage: true,
				resetModalOpen: false,
				removeModalOpen: false,
				deleting: false,
				resetCount: 1,
				uploadErrors: new Errors()
			}
		},

		watch: {
			parsedValueObject() {
				let stringObject = JSON.stringify(this.parsedValueObject);
				this.value = stringObject;
			}
		},

		beforeMount() {
			this.value = this.field.value || '';

			let parsedValueFromJson = JSON.parse(this.field.value);
			this.parsedValueObject = {...this.parsedValueObject, ...parsedValueFromJson};
			if (Boolean(this.field.previewUrl)) {
				this.parsedValueObject.isNew = false;
				UrlToBase64(this.field.thumbnailUrl, (base64) => {
					this.parsedValueObject.binaryCrop = base64;
				});
				UrlToBase64(this.field.previewUrl, (base64) => {
					this.parsedValueObject.binaryImg = base64;
				});
				this.parsedValueObject.loaded = true;

				this.originalValueObject = this.parsedValueObject;
			}
		},

		methods: {
			removeFile() {
				this.closeRemoveModal();
				if (!Boolean(this.field.previewUrl)) {
					this.parsedValueObject = {
						modified: false,
						loaded: false,
						isNew: true,
						binaryImg: null,
						binaryCrop: null,
						cropBoxData: null,
						imgSrc: null,
						cropSrc: null,
					};
					return;
				}
				this.parsedValueObject = {
					...this.parsedValueObject,
					binaryImg: null,
					binaryCrop: null,
					cropBoxData: null,
					loaded: false,
					isNew: true,
				};
			},

			onUpdateValueObject(newValueObject) {
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
				this.resetCount = this.resetCount + 1;
				this.closeResetModal();
				this.parsedValueObject = this.originalValueObject;
			},
		}
	}
</script>
<style scoped>
</style>
