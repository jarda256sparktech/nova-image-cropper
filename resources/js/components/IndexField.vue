<template>
    <p>
        <img
            :src="field.thumbnailUrl"
            class="w-8 h-8"
            style="object-fit: cover;"
            v-if="field.thumbnailUrl"
        />
        <span v-else>{{ field.value || '&mdash;' }}</span>
    </p>
</template>

<script>
	export default {
		props: ['resourceName', 'field']
	};
</script>
