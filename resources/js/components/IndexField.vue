<template>
    <p>
        <img
            :src="field.thumbnailUrl"
            class="w-8 h-8"
            style="object-fit: cover;"
            v-if="field.thumbnailUrl"
        />
        <img
            :src="field.previewUrl"
            class="w-8 h-8"
            style="object-fit: cover;"
            v-if="field.previewUrl"
        />
        <span v-else>{{ '&mdash;' }}</span>
    </p>
</template>

<script>
	export default {
		props: ['resourceName', 'field'],
		computed: {
		},
        methods:{
        }
	};
</script>
