<template>
    <div class="el-row--flex">
        <div class="width50">
            <div class="full-preview-wrapper">
                <img :src="image">
            </div>
        </div>
        <div class="width50">
            <div id="cropped-preview"></div>
        </div>
<!--        <div class="width50">-->
<!--            <div class="full-preview-wrapper">-->
<!--                <img :src="thumbImage">-->
<!--            </div>-->
<!--        </div>-->
    </div>
</template>
<script>

	export default {
		name: 'PicturePreview',
		props: ['image', 'thumbImage'],

		data() {
			return {}
		},

		computed: {},

		watch: {},

		methods: {}
	}
</script>
<style scoped>
    .width50 {
        /*width: 50%;*/
        box-sizing: border-box;
        padding: 15px;
        background-color: #808080;
        display: flex;
        align-items: center;
        justify-content: center;
    }

    .full-preview-wrapper {
        max-height: 300px;
    }

    #cropped-preview {
        overflow: hidden;
        max-width: 300px;
        min-width: 300px;
        width: 300px;
        max-height: 200px;
        min-height: 200px;
        height: 200px;
    }
</style>
